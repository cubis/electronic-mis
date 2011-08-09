<?php

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

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
    $outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<DOCCOUNT>" . $doctorNamePrep->rowCount() . "</DOCCOUNT>\n";
        while ($doctorName = $doctorNamePrep->fetch(PDO::FETCH_ASSOC)) {
            $outputString .= "<Names>";
	    $outputString .= "<DOCID>" . $doctorName['PK_DoctorID'] . "</DOCID>\n";
            $outputString .= "<LastName>" . $doctorName['LastName'] . "</LastName>\n";
            $outputString .= "</Names>";
            //logToDB($user . " access patient info for " . $target, $memberInfo['PK_member_id'], $user);
        }
	
    } else {
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
   
   
   
	$qry = "SELECT Doctor.PK_DoctorID, Users.LastName FROM Doctor, Users WHERE Users.PK_member_id =  Doctor.FK_member_id";

        $doctorNamePrep = $db->prepare($qry);
        $doctorNameSuccess = $doctorNamePrep->execute();
       if (!$doctorNameSuccess) {
            $errMsgArr[] = "DATABASE ERROR TWO";
            $errNum++;
        }
        if ($errNum == 0) {
            $retVal = outputXML($errNum, $errMsgArr, $doctorNamePrep);
        } else {
            $retVal = outputXML($errNum, $errMsgArr, '');
        }
	
	
    } else {
        $errMsgArr[] = "Unauthorized to view information";
        $errNum++;
        $retVal = outputXML($errNum, $errMsgArr, '');
    }
   
 //  $retVal = "STUFF";
   
   return $retVal;
}

//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce
// set up some useful variables
$output = doService();

print($output);
//print("SHIT = " . $_GET['u']);
?>