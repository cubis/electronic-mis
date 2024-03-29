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
//Type 4 = View all infotmat0ion
/*
if (isset($_POST['type'])) {
    if ($_POST['type'] == 1) {
        $output = doServiceAp($db);
    } else if ($_POST['type'] == 2) {
        $output = doServiceVi($db);
    } else if ($_POST['type'] == 3) {
        $output = doServiceUp($db);
    } else {
        $output = doServiceView($db);
    }
}
else
    $output = doServiceView($db);
*/


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
//Type 2 Service
function doService($db) {
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

    /* //Input Validations (still need to do
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

        if (!$updateVisitSuccess) {
            $errMsgArr[] = 'update visit failed';
            $errNum += 1;
        }

        if ($med && $dos && $sdate && $edate) {
            $medPrep = $db->prepare("SELECT * FROM Appointment WHERE PK_AppID = '" . $id . "'");
            $medSuccess = $medPrep->execute();
            $meds = $medPrep->fetch(PDO::FETCH_ASSOC);
            $insertMedPrep = $db->prepare("INSERT INTO Medications (FK_PatientID, Medication, Dosage, StartDate, EndDate)
												VALUES( '" . $meds['FK_PatientID'] . "', :med, :dos, :sdate, :edate)");
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

$output = doService($db);
print($output);

?>
