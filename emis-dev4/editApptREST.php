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
		$outputString .= "<ApptID>" . $_POST['aid'] . "</ApptID>";
		logToDB($user." update appointment", $memberInfo['PK_member_id'], $user); 		
	} 
	else {
		$ct = 0;
		while( $ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		if( !isset($memberInfo['PK_member_id']) ){
			logToDB($user." failed to update appointment", NULL, $user);
		} else {		
			logToDB($user." failed to update appointment", $memberInfo['PK_member_id'], $user);
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

		
		if($recKey == $trustedKey || $recKey == $currKey){
			
			//ENSURE OLD PASS AND TWO NEW PASSWORDS PROVIDED
			//FIGURE OUT IF WE'RE ADDING A NEW ONE OR OLD
			$aidIsSet = isset($_POST['aid']);
			
			if( $aidIsSet && (!isset($_POST['status']) || $_POST['status'] == '') ){
				$thing = false;
				$errMsgArr[] = "No status provided";
				$errNum++;
			} else if(!$aidIsSet){
				$_POST['status'] = true;
			}
			
			if(!isset($_POST['reminder']) || $_POST['reminder'] == ''){
				$errMsgArr[] = "No reminder setting provided";
				$errNum++;
			}
			
			if(!isset($_POST['reason']) || $_POST['reason'] == ''){
				$errMsgArr[] = "No reason for appointment provided";
				$errNum++;
			}
			
			if(!isset($_POST['time']) || $_POST['time'] == ''){
				$errMsgArr[] = "No appointment time provided";
				$errNum++;
			} else if( !preg_match('/^([1-9]|0[1-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $_POST['time']) ){
				$errMsgArr[] = "Improper Time Format";
				$errNum++;
			}
			
			if(!isset($_POST['date']) || $_POST['date'] == ''){
				$errMsgArr[] = "No appointment date provided";
				$errNum++;
			} else if( !preg_match('/^(2[0-9][0-9][0-9])-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/', $_POST['date']) ){
				$errMsgArr[] = "Improper date format";
				$errNum++;
			}
			
			$needDoc = ($memberInfo['Type'] == 1 || $memberInfo['Type'] == 200 || $memberInfo['Type'] == 400);
			$needPat = ($memberInfo['Type'] == 200 || $memberInfo['Type'] == 300 || $memberInfo['Type'] == 400);
			
			if(!$needDoc){
				$thisDocIDPrep = $db->prepare("SELECT Doctor.PK_DoctorID FROM Doctor WHERE FK_member_id = :memID;");		
				$thisDocIDSuccess = $thisDocIDPrep->execute( array(":memID"=>$memberInfo['PK_member_id']) );
				//failed to access database for user info
				if(!$thisDocIDSuccess){
					$errMsgArr[] = "Getting doctor id error";
					$errNum++;
					return outputXML($errNum, $errMsgArr, '');
				}
				$docIDArray = $thisDocIDPrep->fetch(PDO::FETCH_ASSOC);
				$docID = $docIDArray['PK_DoctorID'];
				$_POST['doctor'] = $docID;
				
			}
			
			if(!$needPat){
				$thisPatIDPrep = $db->prepare("SELECT Patient.PK_PatientID FROM Patient WHERE FK_member_id = :memID;");		
				$thisPatIDSuccess = $thisPatIDPrep->execute( array(":memID"=>$memberInfo['PK_member_id']) );
				//failed to access database for user info
				if(!$thisPatIDSuccess){
					$errMsgArr[] = "Getting patient id error";
					$errNum++;
					return outputXML($errNum, $errMsgArr, '');
				}
				$patIDArray = $thisPatIDPrep->fetch(PDO::FETCH_ASSOC);
				$patID = $patIDArray['PK_PatientID'];
				$_POST['patient'] = $patID;
			}
			if(!isset($_POST['doctor']) || $_POST['doctor'] == '' || $_POST['doctor'] == 0){
				$errMsgArr[] = "No doctor provided";
				$errNum++;
			}
			if(!isset($_POST['patient']) || $_POST['patient'] == '' || $_POST['patient'] == 0){
				$errMsgArr[] = "No patient provided";
				$errNum++;
			}
		
			//Make sure old password correct
			$aid = $_POST['aid'];
			$status = $_POST['status'];
			if($_POST['reminder'] == 'true'){
				$reminder = 1;
			} else {
				$reminder = 0;
			}
			//print($reminder);
			//$reminder = $_POST['reminder'];
			$reason = $_POST['reason'];
			$time = $_POST['time'] . ":00";
			$date = $_POST['date'];
			$doctor = $_POST['doctor'];
			$patient = $_POST['patient'];
			$address = "12345 Hospital Lane";
			
			
			$str = "SELECT Appointment.PK_AppID FROM Appointment WHERE 
				Date=:date  AND Time=:time AND (FK_PatientID=:patID OR FK_DoctorID=:docID) AND Status!='Cancelled'";
			$availParam = array(":date"=>$date, ":time"=>$time, ":patID"=>$patient, ":docID"=>$doctor);
			if($aidIsSet){
				$str .= " AND PK_AppID!=:aid";
				$availParam["aid"] = $aid;
			}
			$availPrep = $db->prepare($str);
			$availSuccess = $availPrep->execute($availParam);
			//failed to access database for user info
			if(!$availSuccess){
				$errMsgArr[] = "Checking schedule conflict error";
				$errNum++;
				return outputXML($errNum, $errMsgArr, '');
			}
			if($availPrep->rowCount() != 0 && $status!='Cancelled'){
				//$answerArray = $availPrep->fetch(PDO::FETCH_ASSOC);
				//$errMsgArr[] = $answerArray['PK_AppID'] . "Scheduling Conflict" . " Row Count " . $availPrep->rowCount();
				$errMsgArr[] = "Scheduling conflict";
				$errNum++;
			}
			
			//update database with new appt info
			if($errNum == 0){
			
			
				if($aidIsSet){
					$str = "UPDATE Appointment SET `FK_DoctorID`='$doctor', FK_PatientID='$patient', `Date`='$date', `Time`='$time', `Address`='$address',
						`Status`='$status', `Reason`='$reason', `Reminder`='$reminder' WHERE `PK_AppID`='$aid';";
				//	print($str);
					$insertAppt = $db->prepare($str);
					$success = $insertAppt->execute();
					if(!$success){
						$sqlError = $insertAppt->errorInfo();
						$errMsgArr[] = $sqlError[2];
						$errNum++;
					}
				
				} else {
					$str = "INSERT INTO Appointment (FK_DoctorID, FK_PatientID, `Date`, `Time`, `Address`, `Status`, `Reason`, `Reminder`)
						VALUES ('$doctor', '$patient', '$date', '$time', '$address', '$status', '$reason', '$reminder');";
					$insertAppt = $db->prepare($str);
					$success = $insertAppt->execute();
					if(!$success){
						$sqlError = $insertAppt->errorInfo();
						$errMsgArr[] = $sqlError[2];
						$errNum++;
					} else {
						$getApptID = $db->prepare("SELECT @@IDENTITY");
						$apptIDSucc = $getApptID->execute();
						if(!$apptIDSucc){
							$sqlError = $insertAppt->errorInfo();
							$errMsgArr[] = $sqlError[2];
							$errNum++;
						} else{
							$apptIDArray = $getApptID->fetch(PDO::FETCH_ASSOC);
							$_POST['aid'] = $apptIDArray['@@IDENTITY'];
						}
					}
				}
				
	
			}
			
			
			
		} else {
			$errMsgArr[] = "Unauthorized to change appointment information";
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