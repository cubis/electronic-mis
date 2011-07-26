<?php
/* Assumptions:  transfer is over secure https
 *               password is already hashed+salt before transfer
 *
 * Access:  This WS may be accessed by anyone
 * Input:  https://[URL]/Authenticate.php?u=[username]&password=[pass]
 * Output: XML
 *   result  [0 or 1]  if user and pass is correct
 *   key     [hashed key] validate auth was performed on this WS
 *
 *
 ****EXAMPLE OUTPUT DIGEST*****
 *  <?xml version="1.0"?>
 *      <result>    1       </result>
 *      <key>       fb504a91465213203ae7c3866bbf3cf4</key>
 *      <userID>    12345   </userID>
 *      <AccessType>400     </type>
 *
 *  */




require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr, $memberInfo) {


/* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
	/*$controlString = "3p1XyTiBj01EM0360lFw";
	$AUTH_KEY = md5($user.$pw.$controlString);
	
	*/	
		global $db;
	if( isset($_GET['u']) ){
		$user = $_GET['u'];
	} else {
		$user = "UNKOWN";
	}
	if( isset($memberInfo['PK_member_id']) ){
		$myID = $memberInfo['PK_member_id'];
	} else {
		$myID = NULL;
	}
	$outputString = ''; //start empty
	$outputString .= "<?xml version=\"1.0\"?>\n";
	$outputString .= "<content>\n";
	$outputString .= "<errNum>" . $errNum . "</errNum>\n";
	if($errNum == 0){
		$outputString .= "<RESULT>SUCCESSFUL REGISTER!</RESULT>";
		logToDB($user. " changed doctor for a patient", $myID, $user);
	} else {
		$ct = 0;
		while($ct < $errNum){
			$outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
			$ct++;
		}
		logToDB($user. " failed to change doctor for a patient", $myID, $user);
	}		
	$outputString .= "</content>";	
	return $outputString;	
}

function doService($db) {

	$errMsgArr = array();
	$errNum = 0;
	
	//Input Validations
	if (!isset($_GET['pat']) || $_GET['fname'] == '') {
		$errMsgArr[] = 'Patient ID number missing';
		$errNum++;
	}
	if (!isset($_GET['doc']) || $_GET['lname'] == '') {
		$errMsgArr[] = 'Doctor ID number missing';
		$errNum++;
	}
	//end
	if (!isset($_GET['u']) || $_GET['u'] == '') {
		$errMsgArr[] = 'Login ID missing';
		$errNum++;
	}
	if (!isset($_GET['key']) || $_GET['p'] == '' || $_GET['p'] == 'd41d8cd98f00b204e9800998ecf8427e') {
		$errMsgArr[] = 'Password missing';
		$errNum++;
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
		
		
		if($recKey == $trustedKey || $reKey == $currKey){
			$patID = $_GET['pat'];
			$docID = $_GET['doc'];
			if($docID == -1){
				$docID = NULL;
			}
			
			$qry = "UPDATE Patient SET FK_DoctorID = :doc WHERE PK_PatientID = :pat";
			$patientInfoPrep = $db->prepare($qry);
			$patientInfoSuccess = $patientInfoPrep->execute( array(":doc"=>$docID, ":pat"=>$patID ) );
			if(!$patientInfoSuccess){
				$errMsgArr[] = "DATABASE ERROR TWO";
				$errNum++;
			}
			if(errNum == 0){
				$retVal = outputXML($errNum, $errMsgArr, $memberInfo);
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
	
	
	$output = doService($db);
	
	print($output);


?>