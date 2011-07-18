<?php
require_once('/configREST.php');     //sql connection information
require_once('/bootstrapREST.php');  //link information

function outputXML($result, $message, $targetType, $target) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><result>" . $result . "</result>\n";
	
	
	if($result == '1'){
	
		if($targetType == '' || $target == ''){
			$qry="SELECT * FROM Users";
		} else {
			$qry="SELECT * FROM Users WHERE ".$targetType."='".$target."'";
		}
		$sqlResult=mysql_query($qry);	
		if(!$sqlResult)
		{
			$message = mysql_error();
			$outputString .= "<message>".$message."</message>\n";
		}
		else
		{
			$userCount = mysql_num_rows($sqlResult);
			$outputString .= "<count>".$userCount."</count>\n";
		
			while ($row = mysql_fetch_assoc($sqlResult))
			{
				$outputString .= "<Entry>";
				$outputString .="<ID>".$row['PK_member_id']."</ID>\n";
				$outputString .= "<FirstName>".$row['FirstName']."</FirstName>\n";
				$outputString .= "<LastName>".$row['LastName']."</LastName>\n";
				$outputString .= "<Sex>".$row['Sex']."</Sex>\n";
				$outputString .= "<UserName>".$row['UserName']."</UserName>\n";
				$outputString .= "<Email>".$row['Email']."</Email>\n";
				$outputString .= "<Birthday>".$row['Birthday']."</Birthday>\n";
				$outputString .= "<PhoneNumber>".$row['PhoneNumber']."</PhoneNumber>\n";
				$outputString .= "<SSN>".$row['SSN']."</SSN>\n";
				$outputString .= "<Type>".$row['Type']."</Type>\n";
				$outputString .= "<NeedApproval>".$row['NeedApproval']."</NeedApproval>\n";
				$outputString .= "<Locked>".$row['Locked']."</Locked>\n";
				$outputString .= "</Entry>";
			}
		}
		
	}
	else
	{
		$outputString .= "<message>" . $message . "</message>";
	}
	
	$outputString .= "</content>";
	
	

	return $outputString;
}

function doService($url, $method, $levelForAll) {
	if($method == 'GET'){
	
		$user = strtoupper($_GET['u']);
		$qry="SELECT * FROM Users WHERE UserName='" . $user . "'";
		$result=mysql_query($qry);
		$member = mysql_fetch_assoc($result);
		$pwd = $member['Password'];
				
		$trustedKey = "xolJXj25jlk56LJkk5677LS";
		$controlString = "3p1XyTiBj01EM0360lFw";
		
		
		$AUTH_KEY = md5($user.$pwd.$controlString);
		$TRUST_KEY = md5($AUTH_KEY.$trustedKey);
		$postKey = $_GET['key'];
		
		// admin requesting info
		if($postKey == $TRUST_KEY && ((int)$member['Type'])>=$levelForAll)
		{
		
			// request all users' info
			if($_GET['targetType'] == '' || $_GET['target'] == '')
			{
				$retVal = outputXML('1', '', '', '');
//HEEEEEEEEERRRRRRRREEEE				
				logToDB($user." accessing information for all users", true, $member['PK_member_id']);
			}
			
			// request a single user's info
			else
			{
				$retVal = outputXML('1', '', $_GET['targetType'], $_GET['target']);
//HEEEEEEEEERRRRRRRREEEE					
				logToDB($user." accessing information for user that has " . $_GET['targetType'] . " of " . $_GET['target'], true, $member['PK_member_id']);
			}
		}
		
		// non-admin user, receives only his info 
		else if($postKey == $TRUST_KEY)
		{
			$retVal = outputXML('1', '', 'UserName', $user);
//HEEEEEEEEERRRRRRRREEEE				
			logToDB($user." accessing information for self", true, $member['PK_member_id']);
		}
		
		// untrusted client
		else if($postKey == $AUTH_KEY)
		{
			$retVal = outputXML('0', 'UNTRUSTED CLIENTS UNABLE TO UPDATE ACCOUNT INFORMATION');
		}
		
		// random error, fuck off
		else {
			$retVal = outputXML('0',  'UNAUTHORIZED ACCESS');
		}
	}
	
	else
	{
		$retVal = outputXML('0', 'RECEIVED INCORRECT MESSAGE');	
	}
	
	return $retVal;
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$retVal = doService($serviceURL, $serviceMethod, 400);

	
print($retVal);
//print($_GET['target']);
?>