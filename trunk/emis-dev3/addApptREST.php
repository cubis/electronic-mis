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

function outputXML($errNum, $errMsgArr) {


    /* @var $AUTH_KEY A key that will be used to prove authentication occurred from this service. */
    /* $controlString = "3p1XyTiBj01EM0360lFw";
      $AUTH_KEY = md5($user.$pw.$controlString);

     */
    global $db;
    if (isset($_POST['u'])) {
        $user = $_POST['u'];
    } else {
        $user = "UNKOWN";
    }

    $outputString = ''; //start empty
    $outputString .= "<?xml version=\"1.0\"?>\n";
    $outputString .= "<content>\n";
    $outputString .= "<errNum>" . $errNum . "</errNum>\n";
    if ($errNum == 0) {
        $outputString .= "<RESULT>SUCCESSFUL ADD APPT!</RESULT>";
        logToDB($user . " successfuly registered", NULL, $user);
    } else {
        $ct = 0;
        while ($ct < $errNum) {
            $outputString .= "<ERROR>" . $errMsgArr[$ct] . "</ERROR>\n";
            $ct++;
        }
        logToDB($user . " unsuccessful registered", NULL, $user);
    }
    $outputString .= "</content>";
    return $outputString;
}

function doService($db) {

    $errMsgArr = array();
    $errNum = 0;

    $doctor = $_POST['doctor'];
    $month = $_POST['month'];
    $day = $_POST['day'];
    $year = $_POST['year'];
    $hour = $_POST['hour'];
    $reason = $_POST['reason'];
    $reminder = $_POST['reminder'];
    if ($errNum == 0) {
        //set up and insert values into the user table
        
        //getting the patient id from the user table
        $getPID = $db->prepare("Select PK_PatientID FROM Patient WHERE FK_member_id = (Select PK_member_id From Users where UserName = '".$_GET['u']."');");
        $succes = $getPID->execute();
        $member = $getPID->fetch(PDO::FETCH_ASSOC);
        
        $addApptPrep = $db->prepare("INSERT INTO Appointment(FK_DoctorID, FK_PatientID, Date, Time, Address, Status, Reason, Reminder) 
                                        VALUES(:doc, '".$member['PK_member_id']."', :date, :time, :address, :status, :reason, :reminder);");
        //$tableType = '';
        $status = "scheduled";
        $date = $year . "-" . $month . "-" . $day;
        $time = $hour . "";
        $vals = array(
            ':doc' => $doctor,
            //':pat' => $pat,
            ':date' => $date,
            ':time' => $time,
            ':address' => $address,
            ':status' => $status,
            ':reason' => $reason,
            ':reminder' => $reminder
        );
        $insertApptSuccess = $addApptPrep->execute($vals);

        //$needapproval;
        //$type;
        if (!$insertApptSuccess) {
            $errMsgArr[] = 'Add Appt failed';
            $errNum += 1;
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
