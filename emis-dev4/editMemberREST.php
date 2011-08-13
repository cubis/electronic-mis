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
		logToDB($user." changed password", $memberInfo['PK_member_id'], $user); 		
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

		
		if($recKey == $trustedKey){
			
		if($memberInfo['Type'] == 1){
			$userName = $_POST['u'];
		} else {
			$userName = $_POST['UserName'];
		}
			//update database with new password
			if($errNum == 0){						
				$updateSQL = 'UPDATE Users SET';
				$updateSQL .= " Users.FirstName=:FirstName";
				$updateSQL .= ", Users.LastName=:LastName";
				$updateSQL .= ", Users.Sex=:Sex";
				$updateSQL .= ", Users.Birthday=:Birthday";
				$updateSQL .= ", Users.SSN=:SSN";
				$updateSQL .= ", Users.Email=:Email";
				$updateSQL .= ", Users.PhoneNumber=:PhoneNumber";
				$paramArray = array(
					":FirstName" => $_POST['FirstName'],
					":LastName" => $_POST['LastName'],
					":Sex" => $_POST['Sex'],
					":Birthday" => $_POST['Birthday'],
					":SSN" => $_POST['SSN'],
					":Email" => $_POST['Email'],
					":PhoneNumber" => $_POST['PhoneNumber'],
				);
				
				
				if ($_POST['Status'] == 'lock')
					$updateSQL .= ", Users.Locked='1'";
				else
					$updateSQL .= ", Users.Locked='0'";
					
				if($_POST['NeedApproval'] == 'Approve')
					$updateSQL .= ", Users.NeedApproval='0'";
					
				$updateSQL .= " WHERE Users.UserName=:user";
				$paramArray[":user"] = $userName;
				
				$prep = $db->prepare($updateSQL);
				//$updateSucc = $prep->execute($paramArray);
				$updateSucc = true;
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				
				
				$updateSQL = 'UPDATE Insurance SET';
				$updateSQL .= " Insurance.Company_Name=:Company_Name";
				$updateSQL .= ", Insurance.Plan_Type=:Plan_Type";
				$updateSQL .= ", Insurance.Plan_Num=:Plan_Num";
				$updateSQL .= ", Insurance.Co-Pay=:Co-Pay";
				$updateSQL .= ", Insurance.Coverage-Start=:Coverage-Start";
				$updateSQL .= ", Insurance.Coverage-End=:Coverage-End";
				$updateSQL .= " WHERE Insurance.FK_PatientID=:FK_PatientID";
				$paramArray = array(
					":Company_Name" => $_POST['Company_Name'],
					":Plan_Type" => $_POST['Plan_Type'],
					":Plan_Num" => $_POST['Plan_Num'],
					":Co-Pay" => $_POST['Co-Pay'],
					":Coverage-Start" => $_POST['Coverage-Start'],
					":Coverage-End" => $_POST['Coverage-End'],
					":FK_PatientID" => $_POST['FK_PatientID']
				);
	
	
			//	$updateSucc = $prep->execute($paramArray);
				if (!$updateSucc) {
					$errorInfoArray = $prep->errorInfo();
					$errMsgArr[] = $errorInfoArray[2];
					$errNum++;					
					return outputXML($errNum, $errMsgArr, $memberInfo);
				}
				
			}
			$thin = $_POST['FK_PatientID'];
			print("_----" . $thin);
			
			//return $_POST['FK_PatientID'];
		} else {
			$errMsgArr[] = "Unauthorized to change password";
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
?>