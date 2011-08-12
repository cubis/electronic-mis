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
        $outputString .= "<RESULT>SUCCESSFUL ADD COPAY!</RESULT>";
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

    $amount = $_POST['amount'];
    $insCover = $_POST['insCover'];
    $month = $_POST['month'];
    $day = $_POST['day'];
    $year = $_POST['year'];
    $appID = $_POST['appID'];
    if ($errNum == 0) {
        //set up and insert values into the user table
        //getting the patient id from the user table
        //$getPID = $db->prepare("Select * FROM Patient WHERE FK_member_id = (Select PK_member_id From Users where UserName = '" . $_POST['u'] . "');");
        //$succes = $getPID->execute();
        //$member = $getPID->fetch(PDO::FETCH_ASSOC);
        //$pid = $member['PK_PatientID'];

        $addCoPayPrep = $db->prepare("INSERT INTO Copayment(Amount, InsCover Date, FK_AppID) 
                                        VALUES(:amount, :insCover, :date, :appID);");
        //$tableType = '';
        //$status = "scheduled";
        $date = $year . "-" . $month . "-" . $day;
        $time = $hour . "";
        $vals = array(
            ':amount' => $amount,
            ':date' => $date,
            ':appID' => $appID,
            ':insCover' => $insCover,
        );
        $addCoPaytSuccess = $addCoPayPrep->execute($vals);

        //$needapproval;
        //$type;
        if (!$insertApptSuccess) {
            $errMsgArr[] = 'Add CoPay failed';
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