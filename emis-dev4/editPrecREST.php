<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

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
		$outputString .= "<PrecID>" . $_POST['prec'] . "</PrecID>";
		logToDB($user." update precondition", $memberInfo['PK_member_id'], $user); 		
	} 
	else {
		$ct = 0;
		while( $ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		if( !isset($memberInfo['PK_member_id']) ){
			logToDB($user." failed to update precondition", NULL, $user);
		} else {		
			logToDB($user." failed to update precondition", $memberInfo['PK_member_id'], $user);
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
		$userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user;");		
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

		if(($recKey == $trustedKey || $recKey == $currKey) && $memberInfo['Type'] > 1){
			
			//ENSURE OLD PASS AND TWO NEW PASSWORDS PROVIDED
			//FIGURE OUT IF WE'RE ADDING A NEW ONE OR OLD
			
			$precIsSet = isset($_POST['prec']);
			
			
			if(!isset($_POST['desc']) || $_POST['desc'] == ''){
				$errMsgArr[] = "No description provided";
				$errNum++;
			}
			if(!isset($_POST['pat']) || $_POST['pat'] == ''){
				$errMsgArr[] = "No patient provided";
				$errNum++;
			}
			$prec = $_POST['prec'];
			$desc = $_POST['desc'];
			$patient = $_POST['pat'];
			//update database with new appt info
			if($errNum == 0){
			
			
				if($precIsSet){
					$str = "UPDATE Precondition SET `Description`='$desc' WHERE `PK_ConditionID`='$prec';";
					$update = $db->prepare($str);
					$success = $update->execute();
					if(!$success){
						$sqlError = $update>errorInfo();
						$errMsgArr[] = $sqlError[2];
						$errNum++;
					}
				
				} 
				else {
					$str = "INSERT INTO Precondition (`FK_PatientID`, `Description`) VALUES ('$patient', '$desc');";
					$insert = $db->prepare($str);
					$success = $insert->execute();
					if(!$success){
						$sqlError = $insert->errorInfo();
						$errMsgArr[] = $sqlError[2];
						$errNum++;
					} else {
						$getID = $db->prepare("SELECT @@IDENTITY");
						$success = $getID->execute();
						if(!$success){
							$sqlError = $getID->errorInfo();
							$errMsgArr[] = $sqlError[2];
							$errNum++;
						} else{
							$apptIDArray = $getID->fetch(PDO::FETCH_ASSOC);
							$_POST['prec'] = $apptIDArray['@@IDENTITY'];
						}
					}
					
				}
				
	
			}
			
			
			
		} else {
			$errMsgArr[] = "Unauthorized to change precondition information";
			$errNum++;
		}
		
		$retVal = outputXML($errNum, $errMsgArr, $memberInfo);
		return $retVal;
	
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$output = doService();

print($output);
//print("SHIT");
?>