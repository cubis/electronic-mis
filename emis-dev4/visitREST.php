<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information
require_once('bootstrap.php');  //link information

clean(&$_POST);
$output = doService();
print($output);

function outputXML($errNum, $errMsgArr, $memberInfo) {

	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
	if($errNum == 0){
		//logToDB($user." changed password", $memberInfo['PK_member_id'], $user); 		
	}
	else {
		$ct = 0;
		while( $ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		/*if( !isset($memberInfo['PK_member_id']) ){
			logToDB($user." failed to change password", NULL, $user);
		} else {		
			logToDB($user." failed to change password", $memberInfo['PK_member_id'], $user);
		}*/
	}
		
	$outputString .= "</content>";
	return $outputString;
}

function doService() {

		global $db;
		
		$errMsgArr = array();
		$errNum = 0;
		
		//MAKE SURE THEY PASSED US CREDENTIALS	
		if(!isset($_POST['u']) || $_POST['u'] == ''){
			$errMsgArr[] = "No username provided for authentication";
			$errNum++;
		}
		if(!isset($_POST['key']) || $_POST['key'] == ''){
			$errMsgArr[] = "No key provided for authentication";
			$errNum++;
		}
		if($errNum != 0){
			return outputXML($errNum, $errMsgArr, '');
		}
		
		
		//USE CREDENTIALS AND AUTHENTICATE
		$user = $_POST['u'];
		$recKey = $_POST['key'];
		$aid = $_POST['aid'];
		$userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user");		
		$userInfoSuccess = $userInfoPrep->execute( array(":user"=>$user) );
		//failed to access database for user info
		if(!$userInfoSuccess){
			$errMsgArr[] = "DATABASE ERROR ONE";
			$errNum++;
			return outputXML($errNum, $errMsgArr, '');
		}
		$memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
		$currKey = $memberInfo['CurrentKey'];
		$trustString = "xolJXj25jlk56LJkk5677LS";
		$trustedKey = md5($currKey.$trustString);
		
		if($recKey == $trustedKey) {
				
			$updateSQL2 = "UPDATE Appointment SET Status='Completed', Reason=:Reason, 
						bp = :BP, weight = :Weight, symptoms = :Symptoms,
						diagnosis = :Diagnosis, bill = :Bill, fileLocation = :file, fileSize = :fsize 
						WHERE PK_AppID = :aid";
			
			$paramArray = array(
				":aid" => $_POST['aid'],
				":BP" => $_POST['bp'],
				":Weight" => $_POST['weight'],
				":Reason" => $_POST['reason'],
				":Diagnosis" => $_POST['diagnosis'],
				":Symptoms" => $_POST['symptoms'],
				":Bill" => $_POST['totalBill'],
				":file" => $_POST['fileName'],
				":fsize" => $_POST['fileSize']
			);
			
			$prep2 = $db->prepare($updateSQL2);
			$updateSucc = $prep2->execute($paramArray);
			if (!$updateSucc) {
				$errorInfoArray = $prep2->errorInfo();
				//$errMsgArr[] = $errorInfoArray[2];
				$errMsgArr[] = "prep2";
				$errNum++;					
				return outputXML($errNum, $errMsgArr, $memberInfo);
			}
			
			global $currentPath;
			$request = $currentPath . "apptViewREST.php?";
			$request .= "u=" . urlencode($user);
			$request .= "&key=" . urlencode($recKey);
			$request .= "&aid=" . urlencode($aid);

			//die($request);
			
			//format and send request
			$ch = curl_init($request);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
			curl_setopt($ch, CURLOPT_TIMEOUT, 8);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
			curl_close($ch); //string from server has been returned <XML> closethe channel

			//die($RESToutput);

			if( $RESToutput == ''){
			  die("CONNECTION ERROR");
			}


			//parse return string
			$parser = xml_parser_create();	
			xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
			xml_parser_free($parser);

			$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
			if ($errNum != 0) {
				$ct = 0;
				while($ct < $errNum){
					$err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
					$ct++;
				}
				$_SESSION['ERRMSG_ARR'] = $err_msg_arr;
			}

			
			
			$pid = $wsResponse[$wsIndices['PATID'][0]]['value'];
			//die("FK_PATIENTID " . $pid);

			$updateSQL4 = "INSERT INTO Medications(FK_PatientID, Medication, Dosage)
							VALUES(:pid, :Medicine, :Dosage)";
			
			$paramArray3 = array(
				":Medicine" => $_POST['medicine'],
				":Dosage" => $_POST['dosage'],
				":pid" => $pid
			);
			
			$prep4 = $db->prepare($updateSQL4);
			$updateSucc = $prep4->execute($paramArray3);
			if (!$updateSucc) {
				$errorInfoArray = $prep->errorInfo();
				//$errMsgArr[] = $errorInfoArray[2];
				$errMsgArr[] = "DATABASE ERROR TWO";
				$errNum++;					
				return outputXML($errNum, $errMsgArr, $memberInfo);
			}
				
				
				
			//return $_POST['FK_PatientID'];
		} else {
			$errMsgArr[] = "Unauthorized to change password";
			$errNum++;
			
		}
		
		//do file stuff
		
	$retVal = outputXML($errNum, $errMsgArr, $memberInfo);
	return $retVal;
	
}
?>
