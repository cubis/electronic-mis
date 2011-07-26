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
 * ***EXAMPLE OUTPUT DIGEST*****
 *  <?xml version="1.0"?>
 *      <result>    1       </result>
 *      <key>       fb504a91465213203ae7c3866bbf3cf4</key>
 *      <userID>    12345   </userID>
 *      <AccessType>400     </type>
 *
 *  */

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information
//Test to see if this is either an new or old appoint
//Wherther there exists an appt id
//type1 =  insert (create)
//type 2 = update (execute)
//type 3 = reschedule;
//Type 4 = View all infotmation
if ($_POST['type'] == 1)
    $output = doServiceAp($db);
else if ($_POST['type'] == 2)
    $output = doServiceVi($db);
else if ($_POST['type'] == 3)
    $output = doServiceUp($db);
else if ($_POST['type'] == 4)
    $output = doServiceView($db);
else
    $output = "ERROR NO TYPE SPECIFIED IN EXECUTION CODE";

print($output);

function outputXML($errNum, $errMsgArr, $db) {


    /* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
    /* $controlString = "3p1XyTiBj01EM0360lFw";
      $AUTH_KEY = md5($user.$pw.$controlString);

     */
    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content>\n";
    $outputString .= "<errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<RESULT>SUCCESSFUL Service</RESULT>";
        logToDB($_POST['u'] . " successful query", false, -1, $db);
    } else {
        $ct = 0;
        while ($ct < $errNum) {
            $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
            $ct++;
        }
        logToDB($_POST['u'] . " unsuccessful visit", false, -1, $db);
    }
    $outputString .= "</content>";
    return $outputString;
}

//Used when visit happend
//Type 1 service
function doServiceAp($db) {
    //NOTE: GET WITH MU TO SEE WHAT HE USING TO POST
    $errMsgArr = array();
    $errNum = 0;
    $doc = $_POST[''];
    $pat = $_POST[''];
    $date = $_POST[''];
    $time = $_POST[''];
    $address = $_POST[''];
    $status = $_POST[''];
    $reason = $_POST[''];
    /*
      //Input Validations (still need to do
      if (!isset($_POST['bp']) || $_POST['bp'] == '') {
      $errMsgArr[] = 'Blood Pressure missing';
      $errNum += 1;
      }
      if (!isset($_POST['weight']) || $_POST['weight'] == '') {
      $errMsgArr[] = 'Weight missing';
      $errNum += 1;
      }
      //test
      if (!isset($_POST['sym']) || $_POST['sym'] == '') {
      $errMsgArr[] = 'Symptoms missing';
      $errNum += 1;
      }
      if (!isset($_POST['diag']) || $_POST['diag'] == '') {
      $errMsgArr[] = 'Diagnosis address missing';
      $errNum += 1;
      } */

    if ($errNum == 0) {
        //set up and insert values into the appointment table
        $addApptPrep = $db->prepare("INSERT INTO Appointment(FK_DoctorID, FK_PatientID, Date, Time, Address, Status, Reason) 
                                        VALUES(:doc, :pat, :date, :time, :address, :status, :reason);");
        //$tableType = '';

        $vals = array(
            ':doc' => $doc,
            ':pat' => $pat,
            ':date' => $date,
            ':time' => $time,
            ':address' => $address,
            ':status' => $status,
            ':reason' => $reason
        );
        $insertApptSuccess = $addApptPrep->execute($vals);

        if (!$insertApptSuccess) {
            $errMsgArr[] = 'update visit failed';
            $errNum += 1;
        }

        $retVal = outputXML($errNum, $errMsgArr, $db);
    } else {
        $retVal = outputXML($errNum, $errMsgArr, $db);
    }


    return $retVal;
}

//Type 2 Service
function doServiceVi($db) {
    $errMsgArr = array();
    $errNum = 0;
    $id = $_POST['id'];
    $bp = $_POST['bp'];
    $weight = $_POST['weight'];
    $sym = $_POST['sym'];
    $diag = $_POST['diag'];
    $med = $_POST['med'];
    $dos = $_POST['dos'];
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    $bill = $_POST['bill'];
    $pp = $_POST['pp'];
    $numon = $_POST['numan'];
    $rd = $_POST['rd'];
    $fname = $_POST['fname'];
    $floc = $_POST['floc'];
    $status = "close";

    //Input Validations (still need to do
    if (!isset($_POST['bp']) || $_POST['bp'] == '') {
        $errMsgArr[] = 'Blood Pressure missing';
        $errNum += 1;
    }
    if (!isset($_POST['weight']) || $_POST['weight'] == '') {
        $errMsgArr[] = 'Weight missing';
        $errNum += 1;
    }
    //test
    if (!isset($_POST['sym']) || $_POST['sym'] == '') {
        $errMsgArr[] = 'Symptoms missing';
        $errNum += 1;
    }
    if (!isset($_POST['diag']) || $_POST['diag'] == '') {
        $errMsgArr[] = 'Diagnosis address missing';
        $errNum += 1;
    }
    //end
    if ($errNum == 0) {
        //Do update
        $updateVistPrep = $db->prepare("UPDATE Appointment SET bp = :bp, weight = :weight, symptoms = :sym, diagnosis = :diag,
												bill = :bill, paymentPlan = :pp, NumMonths = :numan, FK_ReferalDoc = :rd,
												fileName = :fname, fileLocation = :floc, Status = :status
												WHERE PK_AppID = :id");
        //$tableType = '';

        $vals = array(
            ':bp' => $bp,
            ':weight' => $weight,
            ':sym' => $sym,
            ':diag' => $diag,
            ':bill' => $bill,
            ':pp' => $pp,
            ':numan' => $numan,
            ':rd' => $ssn,
            ':fname' => $ssn,
            ':floc' => $floc,
            ':id' => $id,
            ':status' => $status
        );
        $updateVisitSuccess = $updateVistPrep->execute($vals);

        if (!$insertUserSuccess) {
            $errMsgArr[] = 'update visit failed';
            $errNum += 1;
        }

        if ($med && $dos && $sdate && edate) {
            //get the patient id from visit key for the recently entered row
            $patIDPrep = $db->prepare("SELECT * FROM Appointment WHERE PK_APPTID = '" . $id . "'");
            //$getIDSuccess = $memIDPrep->execute();
            $memIDPrep = $db->prepare("SELECT * FROM Users WHERE UserName = '" . $user . "'");
            $getIDSuccess = $patIDPrep->execute();
            $patient = $memIDPrep->fetch(PDO::FETCH_ASSOC);
            $insertMedPrep = $db->prepare("INSERT INTO Medications (FK_PatientID, Medication, Dosage, StartDate, EndDate)
												VALUSE( '" . $patient['FK_PatientID'] . "', :med, :dos, :sdate, :edate)");
            $vals2 = array(
                ':med' => $med,
                ':dos' => $dos,
                ':sdate' => $sdate,
                ':edate' => $edate
            );
            if (!($insertMedPrep->execute($vals2))) {
                $errMsgArr[] = "Medication insert fail";
                $errNum += 1;
            }
        }


        $retVal = outputXML($errNum, $errMsgArr, $db);
    } else {
        $retVal = outputXML($errNum, $errMsgArr, $db);
    }


    return $retVal;
}

//Type 3 service
function doServiceUp($db) {
    //NOTE: GET WITH MU TO SEE WHAT HE USING TO POST
    $errMsgArr = array();
    $errNum = 0;
    $id = $_POST[''];
    $doc = $_POST[''];
    $pat = $_POST[''];
    $date = $_POST[''];
    $time = $_POST[''];
    $address = $_POST[''];
    $status = $_POST[''];
    $reason = $_POST[''];
    /*
      //Input Validations (still need to do
      if (!isset($_POST['bp']) || $_POST['bp'] == '') {
      $errMsgArr[] = 'Blood Pressure missing';
      $errNum += 1;
      }
      if (!isset($_POST['weight']) || $_POST['weight'] == '') {
      $errMsgArr[] = 'Weight missing';
      $errNum += 1;
      }
      //test
      if (!isset($_POST['sym']) || $_POST['sym'] == '') {
      $errMsgArr[] = 'Symptoms missing';
      $errNum += 1;
      }
      if (!isset($_POST['diag']) || $_POST['diag'] == '') {
      $errMsgArr[] = 'Diagnosis address missing';
      $errNum += 1;
      } */

    if ($errNum == 0) {
        //set up and insert values into the appointment table
        $upApptPrep = $db->prepare("UPDATE Appointment SET FK_DoctorID = :doc, FK_PatientID = :pat, Date = :date, Time = :time, 
											Address = :address, Status = :status, Reason = :reason WHERE PK_AppID = :id");
        //$tableType = '';
        $vals = array(
            ':doc' => $doc,
            ':pat' => $pat,
            ':date' => $date,
            ':time' => $time,
            ':address' => $address,
            ':status' => $status,
            ':reason' => $reason,
            ':id' => $id
        );
        $insertApptSuccess = $upApptPrep->execute($vals);

        if (!$insertApptSuccess) {
            $errMsgArr[] = 'update visit failed';
            $errNum += 1;
        }

        $retVal = outputXML($errNum, $errMsgArr, $db);
    } else {
        $retVal = outputXML($errNum, $errMsgArr, $db);
    }


    return $retVal;
}

//Type 4 Service
function doServiceView($db) {
    $errMsgArr = array();
    $errNum = 0;

    $pid = $_POST['pid'];

    //end
    if ($errNum == 0) {
        //all appointments per for user
        $appoint = $db->prepare("SELECT * FROM Appointment WHERE PK_APPID = :pid");


        $vals = array(
            ':pid' => $pid
        );
        $getAppt = $appoint->execute($vals);

        if (!$updateVisitSuccess) {
            $errMsgArr[] = 'Gather Appoint failed';
            $errNum += 1;
        }

        $retVal = outputXML2($errNum, $errMsgArr, $db);
    } else {
        $retVal = outputXML($errNum, $errMsgArr, $db);
    }
    //Not going to use method
    $outputString .= "<?xml version=\"1.0\"?>\n";
    while ($appt = $getAppt->fetch(PDO::FETCH_ASSOC)) {
        $outputString = ''; //start empty
        $outputString .= "<Appointment>\n";
        $outputString .= "<ID>" . $appt['PK_AppID'] . "</ID>\n";
        $outputString .= "<DoctorID>" . $appt['FK_DoctorID'] . "</DoctorID>\n";
        $outputString .= "<PatientID>" . $appt['FK_PatientID'] . "</PatientID>\n";
        $outputString .= "<Date>" . $appt['Date'] . "</Date>\n";
        $outputString .= "<Time>" . $appt['Time'] . "</Time>\n";
        $outputString .= "<Address>" . $appt['Address'] . "</Address>\n";
        $outputString .= "<Status>" . $appt['Status'] . "</Status>\n";
        $outputString .= "<Reason>" . $appt['Reason'] . "</Reason>\n";
        $outputString .= "<BloodPressure>" . $appt['bp'] . "</BloodPressure>\n";
        $outputString .= "<Weight>" . $appt['weight'] . "</Weight>\n";
        $outputString .= "<Symptoms>" . $appt['symptoms'] . "</Symptoms>\n";
        $outputString .= "<Diagnosis>" . $appt['diagnosis'] . "</Diagnosis>\n";
        $outputString .= "<Bill>" . $appt['bill'] . "</Bill>\n";
        $outputString .= "<PaymentPlan>" . $appt['paymentPlan'] . "</PaymentPlan>\n";
        $outputString .= "<NumberMonths>" . $appt['NumMonths'] . "</NumberMonths>\n";
        $outputString .= "<ReferalDoc>" . $appt['FK_ReferalDoc'] . "</ReferalDoc>\n";
        $outputString .= "<FileNane>" . $appt['fileName'] . "</FileNane>\n";
        $outputString .= "<FileLocation>" . $appt['fileLocation'] . "</FileLocation>\n";
        $outputString .= "<Reminder>" . $appt['Reminder'] . "</Reminder>\n";
        $outputString .= "</Appointment>\n";
    }
    $retVal = $outputString;


    return $retVal;
}


?>