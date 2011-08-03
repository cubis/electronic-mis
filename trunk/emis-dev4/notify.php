<?php
require_once('configREST.php');    
require_once('bootstrapREST.php');

// I had to add this, quick fix my connection string is not working... please refactor
 $connection = @mysql_connect("devdb.fulgentcorp.com","495311team2user","680c12D5!gP592xViF") or die(mysql_error());
                $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());

function sendReminders(){
    $qry = "SELECT Appointment.PK_AppID, Appointment.Date, Appointment.Time, Appointment.Reminder, Users.Email, Users.FirstName, Users.LastName
            FROM Appointment, Patient, Users
            WHERE Appointment.FK_PatientID = Patient.PK_PatientID
            AND Patient.FK_member_id = Users.PK_member_id
            AND Appointment.Date+0 <= CURDATE() + 1
            AND Appointment.Reminder = 1";

    $result=mysql_query($qry);

// main dispatching loop
    while ($row = mysql_fetch_array($result)){
        if(sendMail($row['Email'],$row['Date'],$row['Time'])){
            $qry = "UPDATE Appointment SET Reminder='0' WHERE PK_AppID=".$row['PK_AppID'];
        }
    }
 }

  // task manager
    switch( $_GET['method']){
        case "talk":
            print "<p> Hello! </p>\n";
            break;
        
        case "notify":
            sendReminders();
            break;
        case "reset":
             $rstqry = "Update Appointment set Reminder = '1' ";
             $mysql_query($qry);
             echo "<p>Notifications Reset</p>";
            break;
    }

    function sendMail($address, $date, $time){
        $to = $address;
        $subject = "Apointment Reminder";
        $message = "Hello, This is a reminder that you have an appointment on $date at $time";
        $from = "cpe-67-10-181-224.satx.res.rr.com";
        $headers = "From:" . $from;

        if( mail($to,$subject,$message,$headers)){
            return true; //mail was sent
        }else
            return false; //mail not sent error with server
        }

?>
