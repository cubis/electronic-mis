<?php
require_once('/configREST.php');     //sql connection information
require_once('/bootstrapREST.php');  //link information

function outputXML($result, $message) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><result>" . $result . "</result>\n";
	$outputString .= "<message>".$message."</message>\n";

		
	$outputString .= "</content>";
	return $outputString;
	
}

function doService($url, $method, $level) {
	if($method == 'POST'){
		$user = strtoupper($_POST['u']);
		$qry="SELECT * FROM Users WHERE UserName='" . $user . "'";
		$result=mysql_query($qry);
		$member = mysql_fetch_assoc($result);
		$pwd = $member['Password'];
				
		$trustedKey = "xolJXj25jlk56LJkk5677LS";
		$controlString = "3p1XyTiBj01EM0360lFw";
		
		
		$AUTH_KEY = md5($user.$pwd.$controlString);
		$TRUST_KEY = md5($AUTH_KEY.$trustedKey);
		$postKey = $_POST['key'];
		
		if($postKey == $TRUST_KEY && ((int)$member['Type'])>=$level){
			$ID = clean($_POST['ID']);
			$f_name = clean($_POST['Firstname']);
			$l_name = clean($_POST['Lastname']);
			$sex = clean($_POST['Sex']);
			$email = clean($_POST['Email']);
			$birthday = clean($_POST['Birthday']);
			$phone = clean($_POST['Phonenumber']);
			$ssn = clean($_POST['SSN']);
			$type = clean($_POST['Type']);
			$need = clean($_POST['Need']);
			$table_name = 'Users';
			//  $address = clean($_POST['Address']);
			//  $policy = clean($_POST['Policy']);
			$status = clean($_POST['Status']);
		
			if($need == 1){ 
				$updateQry = "UPDATE $table_name Set FirstName='$f_name',LastName='$l_name',Sex='$sex',Email='$email',Birthday='$birthday',PhoneNumber='$phone',SSN='$ssn', Type = '$type', NeedApproval='0' WHERE PK_member_id = '$ID'";
			}else{
				$updateQry = "UPDATE $table_name Set FirstName='$f_name',LastName='$l_name',Sex='$sex',Email='$email',Birthday='$birthday',PhoneNumber='$phone',SSN='$ssn', Type = '$type', NeedApproval='1' WHERE PK_member_id = '$ID'";
			}
			
			if(strcmp($status,"lock") == 0)
				$statusQry = "UPDATE $table_name SET Locked = '1' WHERE PK_member_id = '$ID'";
			else
				$statusQry = "UPDATE $table_name SET Locked = '0' WHERE PK_member_id = '$ID'";
			
			if(mysql_query($updateQry)){
				if( mysql_query($statusQry) )
					$retVal = outputXML('1', 'SUCCESSFUL UPDATE!');
				else 
					$retVal = outputXML('0', mysql_error());
			}
			else 
			{
				$retVal = outputXML('0', mysql_error());
			}
		} else if($postKey == $AUTH_KEY){
			$retVal = outputXML('0', 'UNTRUSTED CLIENTS UNABLE TO UPDATE ACCOUNT INFORMATION');
		} else {
			$retVal = outputXML('0',  'UNAUTHORIZED ACCESS');
		}
	}else{
		$retVal = outputXML('0', 'RECEIVED INCORRECT MESSAGE');
	}
	return $retVal;
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$getArgs = $_GET;
$postArgs = $_POST; //don't care about post
$retVal = doService($serviceURL, $serviceMethod, 400);

	
print($retVal);

?>