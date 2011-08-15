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

clean(&$_GET);

$output = doService();
print($output);

function outputXML($resultMsg, $errNum, $errMsgArr) {
	$xml = '';
	$xml .= "<?xml version=\"1.0\"?>\n";
	$xml .= "<content>\n";
	$xml .= "<result>" . $resultMsg . "</result>\n";
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

function doService() {
	$errMsgArr = array();
	$errNum = 0;
	$userName = $_GET['u'];
	$authKey = $_GET['k'];
	
	// no username supplied, output error and return
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
	
	global $db;
	$prep = $db->prepare('SELECT CurrentKey, Locked, NeedApproval, PK_member_id 
							FROM Users WHERE UserName = :u');
	if ( $prep->execute(array(':u' => $userName)) ) {
		$result = $prep->fetch(PDO::FETCH_ASSOC);
	}
	else
		return "SQL ERROR GETTING USER DATA";
	
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
?>