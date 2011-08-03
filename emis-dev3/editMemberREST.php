<?php
/* Access:  This WS may be accessed by anyone
 * Input:  https://[URL]/logoutREST.php?u=[username]&k=[current user auth key]
 * Output: XML
 *	result:		[0 or 1]	if user and pass is correct
 *	errNum:		[0+]		number of errors encountered
 *	[error]:	[if errors are present, a text description of the error]
 *
 ****EXAMPLE OUTPUT DIGEST*****
 <?xml version="1.0"?>
 <content>      
 <result>1</result>
 <errNum>0</errNum>
 </content>
 */
 
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

//cleanPostvalues();
$output = doService();
print $output;

function cleanPostValues() {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = mysql_real_escape_string($value);
	}
}

function doService() {
	$errMsgArr = array();
	$errNum = 0;
	$userName = $_POST['UserName'];
	$authKey = $_POST['AuthKey'];
	$callingUserName = $_POST['CallingUserName'];
	
	global $db;
	
	/*// no username supplied, output error and return
	if(!isset($userName) || $userName == "") {
		$errMsgArr[] = 'Username Missing';
		$errNum++;
		$xmlOutput = outputXML('0', $errNum, $errMsgArr);
		return $xmlOutput;
	}	
	// no authkey supplied, output error and return
	if(!isset($authKey) || $authKey == "") {
		$errMsgArr[] = 'Key Missing';
		$errNum++;
		$xmlOutput = outputXML('0', $errNum, $errMsgArr);
		return $xmlOutput;
	}
	// no authkey supplied, output error and return
	if(!isset($callingUserName) || $callingUserName == "") {
		$errMsgArr[] = 'Calling User Missing';
		$errNum++;
		$xmlOutput = outputXML('0', $errNum, $errMsgArr);
		return $xmlOutput;
	}
	
	$data = '';
	foreach ($_POST as $key => $value) {
		$data .= "$key = $value\n";
	}
	return $data;*/
	
	$updateSQL = 'UPDATE Users SET';
	$updateSQL .= " Users.FirstName='" . $_POST['FirstName'];
	$updateSQL .= "', Users.LastName='" . $_POST['LastName'];
	$updateSQL .= "', Users.Sex='" . $_POST['Sex'];
	$updateSQL .= "', Users.Birthday='" . $_POST['Birthday'];
	$updateSQL .= "', Users.SSN='" . $_POST['SSN'];
	$updateSQL .= "', Users.Email='" . $_POST['Email'];
	$updateSQL .= "', Users.PhoneNumber='" . $_POST['PhoneNumber'];
	if ($_POST['Status'] == 'lock')
		$updateSQL .= "', Users.Locked='1";
	else
		$updateSQL .= "', Users.Locked='0";
	$updateSQL .= "' WHERE Users.UserName='" . $userName . "'";
	
	$prep = $db->prepare($updateSQL);
	if ( $prep->execute() ) {
	
	}
	else {
		$errorInfoArray = $prep->errorInfo();
		$errMsgArr[] = $errorInfoArray[2];
		$errNum++;
		$xmlOutput = outputXML($errNum, $errMsgArr);
		return $xmlOutput;
	}
	
	
	$updateSQL = 'UPDATE Insurance SET';
	$updateSQL .= " Insurance.Company_Name='" . $_POST['Company_Name'];
	$updateSQL .= "', Insurance.Plan_Type='" . $_POST['Plan_Type'];
	$updateSQL .= "', Insurance.Plan_Num='" . $_POST['Plan_Num'];
	$updateSQL .= "', Insurance.`Co-Pay`='" . $_POST['Co-Pay'];
	$updateSQL .= "', Insurance.`Coverage-Start`='" . $_POST['Coverage-Start'];
	$updateSQL .= "', Insurance.`Coverage-End`='" . $_POST['Coverage-End'];
	$updateSQL .= "' WHERE Insurance.FK_PatientID='" . $_POST['PersonalID'] . "'";
	
	print $updateSQL;
	

	$prep = $db->prepare($updateSQL);
	if ( $prep->execute() ) {
		$xmlOutput = outputXML($errNum, $errMsgArr);
		return $xmlOutput;
	}
	else {
		$errorInfoArray = $prep->errorInfo();
		$errMsgArr[] = $errorInfoArray[2];
		$errNum++;
		$xmlOutput = outputXML($errNum, $errMsgArr);
		return $xmlOutput;
	}
}

function outputXML($errNum, $errMsgArr) {
	$xml = '';
	$xml .= "<?xml version=\"1.0\"?>\n";
	$xml .= "<content>\n";
	$xml .= "<errNum>" . $errNum . "</errNum>\n";			
	if ($errNum > 0) {
		$i = 0;
		while($i < $errNum) {
			$xml .= "<error>" . $errMsgArr[$i] . "</error>\n";
			$i++;
		}
	}		
	$xml .= "</content>";	
	return $xml;	
}


?>