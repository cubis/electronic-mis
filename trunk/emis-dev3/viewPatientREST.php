<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr, $patientInfoPrep) {
/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	if( isset($_POST['u']) ){
		$user = $_POST['u'];
	} else {
		$user = "UNKOWN";
	}
	if(isset($_GET['p'])){
		$target = $_GET['pat'];
	} else {
		$target = 'all';
	}
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
	if($errNum == 0){
		$outputString .= "<PatientCount>" . $patientInfoPrep->rowCount() . "</PatientCount>\n";
		while( $patientInfo = $patientInfoPrep->fetch(PDO::FETCH_ASSOC) ){
			$outputString .= "<Patient>";
			$outputString .= "<UserName>" . $patientInfo['UserName'] . "</UserName>\n";
			$outputString .= "<FirstName>" . $patientInfo['FirstName'] . "</FirstName>\n";
			$outputString .= "<LastName>" . $patientInfo['LastName'] . "</LastName>\n";
			$outputString .= "<Sex>" . $patientInfo['Sex'] . "</Sex>\n";
			$outputString .= "<Birthday>" . $patientInfo['Birthday'] . "</Birthday>\n";
			$outputString .= "<SSN>" . $patientInfo['SSN'] . "</SSN>\n";
			$outputString .= "<Email>" . $patientInfo['Email'] . "</Email>\n";
			$outputString .= "<PhoneNumber>" . $patientInfo['PhoneNumber'] . "</PhoneNumber>\n";
			$outputString .= "<CompanyName>" . $patientInfo['Company_Name'] . "</CompanyName>\n";
			$outputString .= "<PlanType>" . $patientInfo['Plan_Type'] . "</PlanType>\n";
			$outputString .= "<PlanNum>" . $patientInfo['Plan_Num'] . "</PlanNum>\n";
			$outputString .= "<CoveragePercent>" . $patientInfo['CoveragePercent'] . "</CoveragePercent>\n";
			$outputString .= "<CoPay>" . $patientInfo['Co-Pay'] . "</CoPay>\n";
			$outputString .= "<CoverageStart>" . $patientInfo['CoverageStart'] . "</CoverageStart>\n";
			$outputString .= "<CoverageEnd>" . $patientInfo['CoverageEnd'] . "</CoverageEnd>\n";
			$outputString .= "<FKDoctorID>" . $patientInfo['FK_DoctorID'] . "</FKDoctorID>\n";
			$outputString .= "<Type>" . $patientInfo['Type'] . "</Type>\n";
			$outputString .= "<PatientID>" . $patientInfo['PK_PatientID'] . "</PatientID>\n";
			$outputString .= "</Patient>";
			logToDB($user." access patient info for " . $target, $memberInfo['PK_member_id'], $user); 
		}			
	} 
	else {
		$ct = 0;
		while( $ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		if( !isset($memberInfo['PK_member_id']) ){			
			logToDB($user." failed to access user info for " . $target, NULL, $user);
		} else {		
			logToDB($user." failed to access user info for " . $target, $memberInfo['PK_member_id'], $user);
		}		
	}
		
	$outputString .= "</content>";
	return $outputString;
	//return "SHIT";
}

function doService($level) {

		global $db;
		
		$errMsgArr = array();
		$errNum = 0;
		
		
		//MAKE SURE THEY PASSED US CREDENTIALS	
		if(!isset($_GET['u']) || $_GET['u'] == ''){
			$errMsgArr[] = "No username provided for authentication";
			$errNum++;
		}
		if(!isset($_GET['key']) || $_GET['key'] == ''){
			$errMsgArr[] = "No key provided for authentication";
			$errNum++;
		}
		if($errNum != 0){
			return outputXML($errNum, $errMsgArr, '');
		}
		
		
		//USE CREDENTIALS AND AUTHENTICATE
		$user = $_GET['u'];
		$recKey = $_GET['key'];
		$userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user;");		
		$userInfoSuccess = $userInfoPrep->execute( array(":user"=>$user) );
		$memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
		//failed to access database for user info
		if(!$userInfoSuccess){
			$errMsgArr[] = "DATABASE ERROR ONE";
			$errNum++;
			return outputXML($errNum, $errMsgArr, '');
		}
		$currKey = $memberInfo['CurrentKey'];
		$trustString = "xolJXj25jlk56LJkk5677LS";
		$trustedKey = md5($currKey.$trustString);
		
		
		if($recKey == $trustedKey || $recKey == $currKey){
			if(isset($_GET['pat']) && $memberInfo['Type'] >= $level){
				$target  = $_GET['pat'];
			} else {
				$target = $_GET['u'];
			}
			$qry = "SELECT * FROM Users LEFT JOIN Patient ON Users.PK_member_id = Patient.FK_member_id
					LEFT JOIN Insurance ON Insurance.FK_PatientID = Patient.PK_PatientID";
			if($target != "all"){
				$qry .= " WHERE UserName = :target";
			}
			$patientInfoPrep = $db->prepare($qry);
			$patientInfoSuccess = $patientInfoPrep->execute( array(":target"=>$target) );
			if(!$patientInfoSuccess){
				$errMsgArr[] = "DATABASE ERROR TWO";
				$errNum++;
			}
			if(errNum == 0){
				$retVal = outputXML($errNum, $errMsgArr, $patientInfoPrep);
				//print($patientInfoPrep->rowCount());
			} else {
				$retVal = outputXML($errNum, $errMsgArr, '');
			}
		} else {
			$errMsgArr[] = "Unauthorized to view information";
			$errNum++;
			$retVal = outputXML($errNum, $errMsgArr, '');
			
		}
	return $retVal;
	
}
//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce

// set up some useful variables
$output = doService(300);

print($output);
//print("SHIT = " . $_GET['u']);
?>