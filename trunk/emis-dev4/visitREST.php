<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

clean(&$_POST);
$output = doService();
print($output);

function outputXML($errNum, $errMsgArr, $memberInfo) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	global $db;
	if( isset($_POST['u']) ){
		$user = $_POST['u'];
	} else {
		$user = "UNKOWN";
	}
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
		if( !isset($memberInfo['PK_member_id']) ){
			logToDB($user." failed to change password", NULL, $user);
		} else {		
			logToDB($user." failed to change password", $memberInfo['PK_member_id'], $user);
		}		
	}
		
	$outputString .= "</content>";
	return $outputString;
	//return "SHIT";
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
		
		if($recKey == $trustedKey){
		
				$updateSQL = 'INSERT INTO Visit 
				(Visit.FK_AppID, Visit.BP, Visit.Weight, 
				 Visit.Reason, Visit.Diagnosis, Visit.Symptoms, Visit.Medicine, 
				 Visit.Dosage, Visit.StartDate, Visit.EndDate, Visit.Bill)';
				$updateSQL .= ' VALUES (:aid, :BP, :Weight, :Reason, :Diagnosis, :Symptoms, 
				:Medicine, :Dosage, :StartDate, :EndDate, :Bill)';
				
				$updateSQL2 = "UPDATE Appointment SET Status='Completed',
							bp = :BP, weight = :Weight, symptoms = :Symptoms,
							diagnosis = :Diagnosis, bill = :Bill
							WHERE PK_AppID = :aid";
				
				$paramArray = array(
					":aid" => $_POST['aid'],
					":BP" => $_POST['bp'],
					":Weight" => $_POST['weight'],
					":Reason" => $_POST['reason'],
					":Diagnosis" => $_POST['diagnosis'],
					":Symptoms" => $_POST['symptoms'],
					":Medicine" => $_POST['medicine'],
					":Dosage" => $_POST['dosage'],
					":StartDate" => $_POST['startDate'],
					":EndDate" => $_POST['endDate'],
					":Bill" => $_POST['totalBill']
				);
				
				$prep2 = $db->prepare($updateSQL2);
				$updateSucc = $prep2->execute($paramArray);
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				
				$prep = $db->prepare($updateSQL);
				$updateSucc = $prep->execute($paramArray);
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				//upload file
				$updateSQL3 = "INSERT INTO Files(Name, Size, Type, Content, FK_ApptID)
								VALUES(:fname,:fsize,:ftype,:content,:aid)";
				
				$paramArray2 = array(
					":fname" => $_POST['fname'],
					":fsize" => $_POST['fsize'],
					":ftype" => $_POST['ftype'],
					":content" => $_POST['content'],
					":aid" => $_POST['aid']
				);
				
				$prep3 = $db->prepare($updateSQL3);
				$updateSucc = $prep->execute($paramArray2);
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				
				$updateSQL4 = "INSERT INTO Medications(FK_PatientID, Medication, Dosage)
								VALUES(:pid,:Medicine,:Dosage)";
				
				$pidq = "SELECT FK_PatientID FROM Appointment WHERE PK_AppID = '".$_POST['aid']."'";
				$pid = mysql_query($pidq);

				$paramArray3 = array(
					":Medicine" => $_POST['medicine'],
					":Dosage" => $_POST['dosage'],
					":pid" => $pid
				);
				
				$prep4 = $db->prepare($updateSQL4);
				$updateSucc = $prep->execute($paramArray3);
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				
				
				$qry = "UPDATE Appointment SET Status='Completed' WHERE PK_AppID = :aid";
	
        $patientNamePrep = $db->prepare($qry);
        $patientNameSuccess = $patientNamePrep->execute(array(':aid' => $aid));
       if (!$patientNameSuccess) {
            $errMsgArr[] = "DATABASE ERROR THREE";
            $errNum++;
			$arr = $patientNamePrep->errorInfo();
			die(print_r($arr));
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
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables

/*
$updateSQL = 'INSERT INTO Visit ';
				$updateSQL .= " Visit.FK_AppID=:aid";
				$updateSQL .= ", Visit.BP=:BP";
				$updateSQL .= ", Visit.Weight=:Weight";
				$updateSQL .= ", Visit.Reason=:Reason";
				$updateSQL .= ", Visit.Diagnosis=:Diagnosis";
				$updateSQL .= ", Visit.Symptoms=:Symptoms";
				$updateSQL .= ", Visit.Medicine=:Medicine";
				$updateSQL .= ", Visit.Dosage=:Dosage";
				$updateSQL .= ", Visit.StartDate=:StartDate";
				$updateSQL .= ", Visit.EndDate=:EndDate";
				$updateSQL .= ", Visit.Bill=:Bill";
				*/

?>
