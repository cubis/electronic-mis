<?php

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr, $patientInfoPrep) {
    /* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
    if (isset($_POST['u'])) {
        $user = $_POST['u'];
    } else {
        $user = "UNKOWN";
    }
    if (isset($_GET['p'])) {
        $target = $_GET['pat'];
    } else {
        $target = 'all';
    }

    /*
      $outputString = '';
      $outputString .= "<?xml version=\"1.0\"?>\n";
      $outputString .= "<content>\n";
      while ($appt = $appoint->fetch(PDO::FETCH_ASSOC)) {

      $outputString .= "<apptcount>".$numrows."<apptcount>\n";
      //count for pat &doc
      $outputString .= "<appointment>\n";

      $outputString .= "<apptID>" . $appt['PK_AppID'] . "</apptID>\n";
      $outputString .= "<date>" . $appt['Date'] . "</date>\n";
      $outputString .= "<time>" . $appt['Time'] . "</time>\n";
      $outputString .= "<doctor>" . $appt['DocName'] . "</doctor>\n";
      $outputString .= "<reason>" . $appt['Reason'] . "</reason>\n";
      $outputString .= "<remind>" . $appt['Reminder'] . "</remind>\n";
      $outputString .= "</appointment>\n";
      }
      $outputString .= "</content>\n";
      $retVal = $outputString;

     */
    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<APPTCOUNT>" . $patientInfoPrep->rowCount() . "</APPTCOUNT>\n";
        while ($patientInfo = $patientInfoPrep->fetch(PDO::FETCH_ASSOC)) {
            $outputString .= "<Appointment>";
            $outputString .= "<APPTID>" . $patientInfo['PK_AppID'] . "</APPTID>\n";
            $outputString .= "<PatID>" . $patientInfo['FK_PatientID'] . "</PatID>\n";
            $outputString .= "<REASON>" . $patientInfo['Reason'] . "</REASON>\n";
            $outputString .= "<DATE>" . $patientInfo['Date'] . "</DATE>\n";
            $outputString .= "<TIME>" . $patientInfo['Time'] . "</TIME>\n";
            $outputString .= "<STATUS>" . $patientInfo['Status'] . "</STATUS>\n";
            $outputString .= "</Appointment>";
            logToDB($user . " access patient info for " . $target, $memberInfo['PK_member_id'], $user);
        }
    } else {
        $ct = 0;
        while ($ct < $errNum) {
            $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
            $ct++;
        }
        if (!isset($memberInfo['PK_member_id'])) {
            logToDB($user . " failed to access user info for " . $target, NULL, $user);
        } else {
            logToDB($user . " failed to access user info for " . $target, $memberInfo['PK_member_id'], $user);
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
    /*
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
*/

    if ($recKey == $trustedKey || $recKey == $currKey) {
        if (isset($_GET['pat']) && $memberInfo['Type'] >= $level) {
            $target = $_GET['pat'];
        } else {
            $target = $_GET['u'];
        }

        $qry = "Select * From Appointment INNER JOIN Patient WHERE Appointment.FK_DoctorID = 
                            (Select PK_DoctorID FROM Doctor WHERE FK_member_id = 
                            (SELECT PK_member_id FROM Users WHERE UserName = '".$_GET['u']."')) 
                            AND Appointment.FK_PatientID = Patient.PK_PatientID";

        //$qry = "SELECT * FROM Users LEFT JOIN Patient ON Users.PK_member_id = Patient.FK_member_id
        //		LEFT JOIN Insurance ON Insurance.FK_PatientID = Patient.PK_PatientID";
        if ($target != "all") {
            $qry .= " WHERE UserName = :target";
        }
        $patientInfoPrep = $db->prepare($qry);
        //$patientInfoSuccess = $patientInfoPrep->execute(array(":target" => $target));
        if (!$patientInfoSuccess) {
            $errMsgArr[] = "DATABASE ERROR TWO";
            $errNum++;
        }
        if (errNum == 0) {
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