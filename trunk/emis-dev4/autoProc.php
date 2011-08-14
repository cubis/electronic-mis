<?php

//Include database connection details
require_once('bootstrap.php');

//Array to store validation errors
$errmsg_arr = array();

//Validation error flag
$errflag = false;

//Sanitize the POST values
$login = "autoProc";
$password = "A123456z";
$epw = md5($password); // encrypt password to lame ass md5 for t-fer
global $currentPath;
$request = $currentPath . "authenticateREST.php?u=" . urlencode($login) . "&p=" . urlencode($epw);
//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

if ($output == '') {
    die("CONNECTION ERROR");
}

//parse return string
$parser = xml_parser_create();
xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);

//create trusted key from the given auth key and trusted string
$trustedKey = "xolJXj25jlk56LJkk5677LS";
$key = md5($wsResponse[$wsIndices['KEY'][0]]['value'] . $trustedKey);

//print("KEY: " . $wsResponse[$wsIndices['KEY'][0]]['value']);
$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
//print("OUTPUT = ".$output);


if ($errNum == 0) {

    if ($wsResponse[$wsIndices['KEY'][0]]['value'] == "MEMBER PROFILE LOCKED") {
        print("<p>You are locked out</p>");
    } else {
        session_regenerate_id();
        $_SESSION['SESS_MEMBER_ID'] = $wsResponse[$wsIndices['MEMBERID'][0]]['value'];
        $_SESSION['SESS_FIRST_NAME'] = $wsResponse[$wsIndices['FIRSTNAME'][0]]['value'];
        $_SESSION['SESS_LAST_NAME'] = $wsResponse[$wsIndices['LASTNAME'][0]]['value'];
        $_SESSION['SESS_TYPE'] = $wsResponse[$wsIndices['TYPE'][0]]['value'];
        $_SESSION['SESS_USERNAME'] = $wsResponse[$wsIndices['USERNAME'][0]]['value'];
        $_SESSION['SESS_NEED_APPROVAL'] = $wsResponse[$wsIndices['NEEDAPPROVAL'][0]]['value'];
        $_SESSION['SESS_PERSONAL_ID'] = $wsResponse[$wsIndices['PERSONALID'][0]]['value'];
        $_SESSION['SESS_AUTH_KEY'] = $key;
        print("<p>login success!</p>");

        ////////////////////////////////////// insert autoproc code here ///////////////////////////////////////////


        print("<p>autoproc start</p>");
        print("<p>autoproc after header</p>");

// I had to add this, quick fix my connection string is not working... please refactor
        $connection = @mysql_connect("devdb.fulgentcorp.com", "495311team2user", "680c12D5!gP592xViF") or die(mysql_error());
        $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());

        function doCopay() {


            global $currentPath;
            $request = $currentPath . "coPayViewREST.php?";
            $request .= "u=" . urlencode($userName);
            $request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
            $request .= "&d=" . urlencode($_GET['date']);

            $ch = curl_init($request);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);

            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
            curl_close($ch); //string from server has been returned <XML> closethe channel

            if ($RESToutput == '') {
                die("CONNECTION ERROR");
            }

            $parser = xml_parser_create();

            xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
        }


        function sendMail($address, $date, $time) {
            $to = $address;
            $subject = "No Reply - Apointment Reminder";
            $message = "Hello, This is a reminder that you have an appointment on $date at $time";
            $from = "cpe-67-10-181-224.satx.res.rr.com";
            $headers = "From:" . $from;

            if (mail($to, $subject, $message, $headers)) {
                print("<p>mail was sent to: $to</p>\n");
                return true; //mail was sent
            }else
                return false; //mail not sent error with server

        }


        function sendReminders() {
            $qry = "SELECT Appointment.PK_AppID, Appointment.Date, Appointment.Time, Appointment.Reminder, Users.Email, Users.FirstName, Users.LastName
            FROM Appointment, Patient, Users
            WHERE Appointment.FK_PatientID = Patient.PK_PatientID
            AND Patient.FK_member_id = Users.PK_member_id
            AND Appointment.Date+0 <= CURDATE() + 1
            AND Appointment.Reminder = 1";
            $result = mysql_query($qry);

// main dispatching loop
            while ($row = mysql_fetch_array($result)) {
                print("<p>email=" . $row['Email'] . " date:" . $row['Date'] . "</p>");
                if (sendMail($row['Email'], $row['Date'], $row['Time'])) {
                    $qry = "UPDATE Appointment SET Reminder='0' WHERE PK_AppID=".$row['PK_AppID'];
                    mysql_query($qry);
                }
            }
        }

        // task manager
        switch ($_GET['method']) {
            case "talk":
                print "<p> Hello! </p>\n";
                break;

            case "notify":
                sendReminders();
                break;

            case "reset":
                $rstqry = "Update Appointment set Reminder = '1' ";
                $mysql_query($rsqry);
                echo "<p>Notifications Reset</p>";
                break;

            case "purge":
                $rstqry = "Update Appointment set Reminder = '0' ";
                $mysql_query($rsqry);
                echo "<p>Notifications have all been canceled</p>";
                break;
            
            case "copay":
                if( $_GET['date'] ){ //don't even bother doing the call if date isn't provided
                    doCopay();
                }
                echo "<p>Notifications have all been canceled</p>";
                break;
        }
    }

/////////////////////////////////////////////end of autoproc code/////////////////////////////////////////////
    exit();
    //Login failed
} else {

    //login failed...output error to screen
    $ct = 0;
    while ($ct < $errNum) {
        $errmsg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
        $ct += 1;
    }
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();

    print("<p>error</p>");
    exit();
}
?>
