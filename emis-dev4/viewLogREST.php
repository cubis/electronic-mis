<?php
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

$output = doService();
print($output);

function doService() {
    global $db;
    $errMsgArr = array();
    $errNum = 0;

    //MAKE SURE THEY PASSED US CREDENTIALS	
    if (!isset($_GET['u']) || $_GET['u'] == '') {
        $errMsgArr[] = "No username provided for authentication";
        $errNum++;
    }
    if (!isset($_GET['key']) || $_GET['key'] == '') {
        $errMsgArr[] = "No key provided for authentication";
        $errNum++;
    }
    if ($errNum != 0) {
        return outputXML($errNum, $errMsgArr, '');
    }


    //USE CREDENTIALS AND AUTHENTICATE
    
    $user = $_GET['u'];
    $recKey = $_GET['key'];
    $userInfoPrep = $db->prepare("SELECT * FROM Users WHERE UserName = :user;");
    $userInfoSuccess = $userInfoPrep->execute(array(":user" => $user));
    $memberInfo = $userInfoPrep->fetch(PDO::FETCH_ASSOC);
    //failed to access database for user info
    if (!$userInfoSuccess) {
        $errMsgArr[] = "DATABASE ERROR ONE";
        $errNum++;
        return outputXML($errNum, $errMsgArr, '');
    }
    $currKey = $memberInfo['CurrentKey'];
    $trustString = "xolJXj25jlk56LJkk5677LS";
    $trustedKey = md5($currKey . $trustString);


    if ($recKey == $trustedKey || $recKey == $currKey) {
		$qry = "SELECT * FROM LogFiles";

        $doctorNamePrep = $db->prepare($qry);
        $doctorNameSuccess = $doctorNamePrep->execute();
       if (!$doctorNameSuccess) {
            $errMsgArr[] = "DATABASE ERROR TWO";
            $errNum++;
        }
		
        $retVal = outputXML($errNum, $errMsgArr, $doctorNamePrep);
		
    } else {
        $errMsgArr[] = "Unauthorized to view information";
        $errNum++;
        $retVal = outputXML($errNum, $errMsgArr, '');
    }
   
 //  $retVal = "STUFF";
   
   return $retVal;
}


function outputXML($errNum, $errMsgArr, $doctorNamePrep) {
    /* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
   if (isset($_GET['u'])) {
        $user = $_GET['u'];
    } else {
        $user = "UNKOWN";
    }
    if (isset($_GET['p'])) {
        $target = $_GET['pat'];
    } else {
        $target = 'all';
    }


    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content>\n<errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<LogCount>" . $doctorNamePrep->rowCount() . "</LogCount>\n";
        while ($doctorName = $doctorNamePrep->fetch(PDO::FETCH_ASSOC)) {
            $outputString .= "<Log>";
			$outputString .= "<LogID>" . $doctorName['PK_LogID'] . "</LogID>\n";
			$outputString .= "<SourceIP>" . $doctorName['SourceIP'] . "</SourceIP>\n";
			$outputString .= "<Type>" . $doctorName['Type'] . "</Type>\n";
			$outputString .= "<PurgeDate>" . $doctorName['PurgeDate'] . "</PurgeDate>\n";
			$outputString .= "<FK_member_id>" . $doctorName['FK_member_id'] . "</FK_member_id>\n";
			$outputString .= "<TimeStamp>" . $doctorName['TimeStamp'] . "</TimeStamp>\n";
			$outputString .= "<UserName>" . $doctorName['UserName'] . "</UserName>\n";
            $outputString .= "</Log>";
            //logToDB($user . " access patient info for " . $target, $memberInfo['PK_member_id'], $user);
        }
	}
	else {
        $ct = 0;
        while ($ct < $errNum) {
            $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
            $ct++;
        }
        if (!isset($memberInfo['PK_member_id'])) {
            //logToDB($user . " failed to access user info for " . $target, NULL, $user);
        } else {
            //logToDB($user . " failed to access user info for " . $target, $memberInfo['PK_member_id'], $user);
        }
	
    }

    $outputString .= "</content>";
    return $outputString; 
}
?>