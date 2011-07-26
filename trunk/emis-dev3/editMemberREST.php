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

$output = doService();
print $output;

function doService() {
	$errMsgArr = array();
	$errNum = 0;
	$userName = $_POST['UserName'];
	$authKey = $_POST['AuthKey'];
	$callingUserName = $_POST['CallingUserName'];
	
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
	
	$updateSQL = 'UPDATE Users,Insurance SET';
	$updateSQL .= " Users.FirstName='" . $_POST['FirstName'];
	$updateSQL .= "', Users.LastName='" . $_POST['LastName'];
	$updateSQL .= "', Users.Sex='" . $_POST['Sex'];
	$updateSQL .= "', Users.Birthday='" . $_POST['Birthday'];
	$updateSQL .= "', Users.SSN='" . $_POST['SSN'];
	$updateSQL .= "', Users.Email='" . $_POST['Email'];
	$updateSQL .= "', Users.PhoneNumber='" . $_POST['PhoneNumber'];
	$updateSQL .= "', Insurance.Company_Name='" . $_POST['Company_Name'];
	$updateSQL .= "', Insurance.Plan_Type='" . $_POST['Plan_Type'];
	$updateSQL .= "', Insurance.Plan_Num='" . $_POST['Plan_Num'];
	$updateSQL .= "', Insurance.`Co-Pay`='" . $_POST['Co-Pay'];
	$updateSQL .= "', Insurance.`Coverage-Start`='" . $_POST['Coverage-Start'];
	$updateSQL .= "', Insurance.`Coverage-End`='" . $_POST['Coverage-End'];
	$updateSQL .= "' WHERE username='" . $userName . "' AND Users.PK_member_id = Insurance.FK_PatientID";
		
	global $db;
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
	
	$dbCurrentKey = $result['CurrentKey'];
	$dbLocked = $result['Locked'];
	$dbNeedApproval = $result['NeedApproval'];
	$dbMemberID = $result['PK_member_id'];
	
	if ($dbLocked == '1') {
		$errMsgArr[] = 'Locked user trying to logout';
		$errNum++;
		$xmlOutput = outputXML('0', $errNum, $errMsgArr);
		return $xmlOutput;
	}
	
	$prep = $db->prepare("UPDATE Users SET CurrentKey = NULL WHERE UserName = :u");
	$prep->execute(array(":u" => $userName));
	if ($prep->rowCount() != 1) {
		$error = $prep->errorInfo();
		$errMsgArr[] = $error[2];
		$errNum += 1;
		$xmlOutput = outputXML('0', $errNum, $errMsgArr);
	}
	else {	
		$xmlOutput = outputXML('1', $errNum, $errMsgArr);		
	}
	
	return $xmlOutput;	
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