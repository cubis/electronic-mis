<?php

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information

function outputXML($errNum, $errMsgArr, $apptInfoPrep) {
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
	echo "date: ".$_GET['date'];
    $count = 1;
    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content><errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<COPAYCOUNT>" . $apptInfoPrep->rowCount() . "<\COPAYCOUNT>\n";
        $outputString .= "<DATE>" .$_GET['date'] . "</DATE>";
        while ($apptInfo = $apptInfoPrep->fetch(PDO::FETCH_ASSOC)) {
            $outputString .= "<CopayInfo>";
            $outputString .= "<ID>" . $count . "</ID>\n";
            $outputString .= "<PatUNAME>" . $apptInfo['UserName'] . "</PatUNAME>\n";
            $outputString .= "<ApptID>" . $apptInfo['PK_AppID'] . "</ApptID>\n";
            $outputString .= "<InsuranceCompany>" . $apptInfo['Company_Name'] . "</InsuranceCompany>\n";
            $outputString .= "<PlanName>" . $apptInfo['Plan_Type'] . "</PlanName>\n";
            $outputString .= "<PlanNo>" . $apptInfo['Plan_Num'] . "</PlanNo>\n";
            $outputString .= "<CoveragePerc>" . $apptInfo['Coverage_Percent'] . "</CoveragePerc>\n";
            $outputString .= "<TotalBill>" . $apptInfo['Bill'] . "</TotalBill>\n";
            $outputString .= "</CopayInfo>\n";
            $count++;                                                  
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
    $date = $_GET['date'];
    
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
    //are user credentials going to be used?

    if ($recKey == $trustedKey || $recKey == $currKey) {
        $qry = "SELECT Appointment.PK_AppID, Appointment.Bill, Users.UserName, Insurance.Company_Name, Insurance.Plan_Type, Insurance.Plan_Num, Insurance.Coverage_Percent
            FROM Appointment, Users, Insurance
            WHERE `Appointment`.`Date` = :date
            AND Users.PK_member_id=(SELECT Patient.FK_member_id FROM Patient WHERE Patient.PK_PatientID = Appointment.FK_PatientID)
            AND Insurance.FK_PatientID = Appointment.FK_PatientID";
        
        
        /*
        $qry = "Select Appointment.*, UDoc.LastName AS DocName, UPat.FirstName AS PatFirstName, UPat.LastName AS PatLastName
			From Appointment, Users UDoc, Users UPat WHERE ";

        if ($memberInfo['Type'] == 1) {
            //doctor name is $apptInfo['DocName']
            //patient name is $memberInfo['FirstName']  
            $qry .= "Appointment.FK_PatientID = (SELECT Patient.PK_PatientID FROM Patient WHERE FK_member_id = :user) 
				AND UPat.PK_member_id = :user AND  UDoc.PK_member_id =
				(SELECT Doctor.FK_member_id FROM Doctor WHERE Doctor.PK_DoctorID = Appointment.FK_DoctorID)";
            if (isset($_GET['aid'])) {
                $qry .= " AND Appointment.PK_AppID = :aid";
            }
            $target = $memberInfo['PK_member_id'];
        } else if ($memberInfo['Type'] == 300) {
            //doctor name is $memberInfo['LastName']
            //patient name is $apptInfo['LastName']
            $qry .= "Appointment.FK_DoctorID = (SELECT Doctor.PK_DoctorID FROM Doctor WHERE FK_member_id = :user) 
				AND UDoc.PK_member_id = :user AND  UPat.PK_member_id =
				(SELECT Patient.FK_member_id FROM Patient WHERE Patient.PK_PatientID = Appointment.FK_PatientID)";
            if (isset($_GET['aid'])) {
                $qry .= " AND Appointment.PK_AppID = :aid";
            }
            $target = $memberInfo['PK_member_id'];
        } else if ($memberInfo['Type'] == 400) {
            if (isset($_GET['pat'])) {
                $qry .= "Appointment.FK_DoctorID = (SELECT Doctor.PK_DoctorID FROM Doctor WHERE FK_member_id = :user) 
				AND UDoc.PK_member_id = :user AND  UPat.PK_member_id =
				(SELECT Patient.FK_member_id FROM Patient WHERE Patient.PK_PatientID = Appointment.FK_PatientID)";
                $target = $_GET['pat'];
            } else if (isset($_GET['doc'])) {
                $qry .= "Appointment.FK_DoctorID = (SELECT Doctor.PK_DoctorID FROM Doctor WHERE FK_member_id = :user) 
				AND UDoc.PK_member_id = :user AND  UPat.PK_member_id =
				(SELECT Patient.FK_member_id FROM Patient WHERE Patient.PK_PatientID = Appointment.FK_PatientID)";
                $target = $_GET['doc'];
            } else {
                $qry .= "UDoc.PK_member_id = (SELECT Doctor.FK_member_id FROM Doctor WHERE Doctor.PK_DoctorID = Appointment.FK_DoctorID)
				AND  UPat.PK_member_id = (SELECT Patient.FK_member_id FROM Patient WHERE Patient.PK_PatientID = Appointment.FK_PatientID)";
                $target = '';
            }
            if (isset($_GET['aid'])) {
                $qry .= " AND Appointment.PK_AppID =:aid";
            }
        }
           */
        $apptInfoPrep = $db->prepare($qry);
        $apptArray = array(':date' => $date);
        $apptInfoSuccess = $apptInfoPrep->execute($apptArray);
        if (!$apptInfoSuccess) {
            $pdoError = $apptInfoPrep->errorInfo();

            $errMsgArr[] = "DATABASE ERROR TWO";
            //$errMsgArr[] = ' aid = ' . $apptArray[':aid'];
            $errNum++;
        }
        if ($errNum == 0) {
            $retVal = outputXML($errNum, $errMsgArr, $apptInfoPrep);
        } else {
            $retVal = outputXML($errNum, $errMsgArr, '');
        }
    } else {
        // $errMsgArr[] = "Unauthorized to view information";
        $errMsgArr[] = $trustedKey;
        $errNum++;
        $retVal = outputXML($errNum, $errMsgArr, '');
    }

    // $retVal = "STUFF";
    return $retVal;
}

//8758e4c115ba4669e13a574464488496xolJXj25jlk56LJkk5677LS
//AUTH KEY 40fc9157068b426ea62b1134d57be6ce
// set up some useful variables
$output = doService();

print($output);
//print("SHIT = " . $_GET['aid']);
?>