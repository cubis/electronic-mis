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
			$qry="SELECT * FROM Users WHERE Type='1'";
		} else {
			$qry="SELECT * FROM Users WHERE ".$targetType."='".$target."' && Type='1'";
		}
		$sqlResult=mysql_query($qry);	
		if(!$sqlResult){
			$message = mysql_error();
			$outputString .= "<message>".$message."</message>\n";
		} else {		
			
			
			while ($row = mysql_fetch_assoc($sqlResult)){
				$myMemberID = $row['PK_member_id'];
				$patQry = "SELECT * FROM Patient WHERE FK_member_id = ".$myMemberID;
				$patResult = mysql_query($patQry);
				$patientRows = mysql_fetch_assoc($patResult);
				
				
				
				
				//INSURANCE STUFF
				$patientKey = $patientRows['PK_PatientID'];
				$insQry = "SELECT * FROM Insurance WHERE FK_PatientID =".$patientKey;
				$insResult = mysql_query($insQry);
				$insRows =  mysql_fetch_assoc($insResult);				
				$outputString .="<InsuranceID>".$insRows['PK_InsuranceID']."</InsuranceID>\n";
				$outputString .= "<InsuranceGroup>".$insRows['Company_Name']."</InsuranceGroup>\n";
				$outputString .= "<CoPay>".$insRows['Co-Pay']."</CoPay>\n";
				$outputString .= "<Start>".$insRows['Coverage-Start']."</Start>\n";
				$outputString .= "<End>".$insRows['Coverage-End']."</End>\n";
				
				
				//DOCTOR STUFF
				$docKey = $patientRows['FK_DoctorID'];
				$docQry = "SELECT * FROM Doctor WHERE PK_DoctorID = ".$docKey;
				$docResult = mysql_query($docQry);
				$docRows = mysql_fetch_assoc($docResult);
				$outputString .= "<Doctor>".$docRows['DocName']."</Doctor>";
				
				
				
				//MEDICATION STUFF
				$medQry = "SELECT * FROM Medications WHERE FK_PatientID=".$patientKey;
				$medResult = mysql_query($medQry);
				$numMeds = mysql_num_rows($medResult);
				$outputString .= "<NumMeds>".$numMeds."</NumMeds>";
				While($medRow = mysql_fetch_assoc($medResult)){
					$outputString .= "<Medication>".$medRow['Medication']."</Medication>";
				}
				
							
				
				
				
				//PRECONDITIONS STUFF
				$precQry = "SELECT * FROM PreconditionFiles WHERE FK_PatientID=".$patientKey;
				$precResult = mysql_query($precQry);
				$numPrecs = mysql_num_rows($precResult);
				$outputString .= "<NumPrecs>".$numPrecs."</NumPrecs>";
				While($precRow = mysql_fetch_assoc($precResult)){
					$outputString .= "<Precondition>".$precRow['Description']."</Precondition>";
				}
				
				
			} 
			
		}
		
	} else {
		$outputString .= "<message>".$message."</message>";
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
		
		if($postKey == $TRUST_KEY && ((int)$member['Type'])>=$levelForAll){
			if($_GET['targetType'] == '' || $_GET['target'] == ''){
				$retVal = outputXML('1', '', '', '');
			} else {
				$retVal = outputXML('1', '', $_GET['targetType'], $_GET['target']);
			}
		} else if($postKey == $TRUST_KEY){
			$retVal = outputXML('1', '', 'UserName', $user);
		}else if($postKey == $AUTH_KEY){
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
$retVal = doService($serviceURL, $serviceMethod, 300);

	
print($retVal);
//print($_GET['target']);
?>