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
			
			//ENSURE OLD PASS AND TWO NEW PASSWORDS PROVIDED
			if(!isset($_POST['oldpass']) || $_POST['oldpass'] == '' || $_POST['oldpass'] == 'd41d8cd98f00b204e9800998ecf8427e'){
				$errMsgArr[] = "Old password not provided";
				$errNum++;
			}
			if(!isset($_POST['newpass1']) || $_POST['newpass1'] == '' || $_POST['newpass1'] == 'd41d8cd98f00b204e9800998ecf8427e'){
				$errMsgArr[] = "First new password not provided";
				$errNum++;
			}
			if(!isset($_POST['newpass2']) || $_POST['newpass2'] == '' || $_POST['newpass2'] == 'd41d8cd98f00b204e9800998ecf8427e'){
				$errMsgArr[] = "Second new password not provided";
				$errNum++;
			}
			
			
			
			//Make sure old password correct
			$oldpass = $_POST['oldpass'];
			$epass = md5($oldpass);
			$newpass1 = $_POST['newpass1'];
			$newpass2 = $_POST['newpass2'];
			$currPass = $memberInfo['Password'];
			
			if($currPass != $epass){
				$errMsgArr[] = 'Old password incorrect';
				$errNum++;
			}
			
			
			//problems with new password
			if ($oldpass == $newpass1) {
				$errMsgArr[] = 'New and old passwords must be different';
				$errNum++;
			}
			if($newpass1 != $newpass2){
				$errMsgArr[] = 'New passwords do not match different';
				$errNum++;
			}
			if (!ctype_alnum($newpass1)) {
				$errMsgArr[] = 'New password should be numbers and digits only';
				$errNum++;
			}
			if (strlen($newpass1) < 7) {
				$errMsgArr[] = 'New password must be at least 7 chars';
				$errNum++;
			}
			if (strlen($newpass1) > 20) {
				$errMsgArr[] = 'New password must be at most 20 chars';
				$errNum++;
			}
			if (!preg_match('`[A-Z]`', $newpass1)) {
				$errMsgArr[] = 'New password must contain at least one upper case';
				$errNum++;
			}
			if (!preg_match('`[a-z]`', $newpass1)) {
				$errMsgArr[] = 'New password must contain at least one lower case';
				$errNum++;
			}
			if (!preg_match('`[0-9]`', $newpass1)) {
				$errMsgArr[] = 'New password must contain at least one digit';
				$errNum++;
			}
			
			
			//update database with new password
			if($errNum == 0){						
				$updatePassPrep = $db->prepare("UPDATE Users SET Password = :pass WHERE PK_member_id = :id;");
				$updatePassSuccess = $updatePassPrep->execute( array(":pass"=>md5($newpass1), ":id"=>$memberInfo['PK_member_id'] ) );
				if(!$updatePassSuccess){
					$errMsgArr[] = 'Password update failure';
					$errNum++;
				}
			}
									
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