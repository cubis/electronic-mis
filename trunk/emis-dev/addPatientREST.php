<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($result, $message) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	$outputString = '';
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content>\n";
	$outputString .= "<result>" . $result . "</result>\n";
	$outputString .= "<message>" . $message . "</message>\n";
	$outputString .= "</content>";
	
	return $outputString;
}

function doService($url, $method, $level)
{
	// method is POST
	if( strcmp($method,"POST") == 0 )
	{
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
		
		if($postKey == $TRUST_KEY && ((int)$member['Type'])>=$level)
		{
			$patientID = clean($_POST['patientID']);
			$doctorID = clean($_POST['doctorID']);
			$doctorMemberID = clean($_POST['doctorMemberID']);
		
			$updateQry = "UPDATE Patient SET FK_DoctorID = '" . $doctorID . "' WHERE PK_PatientID = '" . $patientID . "'";
				
			if(mysql_query($updateQry))
			{
				logToDB("Doctor added a patient", true, $doctorMemberID);
				$retVal = outputXML('1', "PATIENT ADDED");
			}
			else 
			{
				$retVal = outputXML('0', mysql_error());
			}
		}
		else if($postKey == $AUTH_KEY)
		{
			$retVal = outputXML('0', 'UNTRUSTED CLIENTS UNABLE TO ADD PATIENTS');
		}
		else
		{
			$retVal = outputXML('0',  'UNAUTHORIZED ACCESS');
		}
	}
	
	// method is GET
	else
	{
		$retVal = outputXML('0', 'RECEIVED INCORRECT MESSAGE');
	}
	$retVal .= "<br>$updateQry";
	return $retVal;
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$serviceURL = $_SERVER['REQUEST_URI'];
$serviceMethod = strtoupper($_SERVER['REQUEST_METHOD']);
$getArgs = $_GET;
$postArgs = $_POST; //don't care about post
$retVal = doService($serviceURL, $serviceMethod, 300);

	
print($retVal);

?>