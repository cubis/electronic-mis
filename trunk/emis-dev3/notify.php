<?php
require_once('configREST.php');    
require_once('bootstrapREST.php');

/*
plan:
    - authenticate requestor
    - determin requestType - {notify, logout}
    - notify
        - get list of users where appointment < 24 hours and wants notify=true
            -required info {apointmentDate, Time, Doctor}
        - send notification e-mail, on success set notify=false
            -succesful notifaction makes action log entry
*/

    echo "<p>starting</p>";
    echo "<p>method = ".$_GET['mail']."</p>";
   
    switch( $_GET['method']){
        case "talk":
            print "<p> Hello! </p>\n";
            break;
        
        case "sendReminders":
                       
            break;
         
        default:
            print "<p> this is a generic response</p>";   
    } 
    


    function sendMail($address, $date, $time){

        echo "<p>user is: ".$_GET['u']."</p>";
        
        $to = $address;
        $subject = "Test mail from emis";
        $message = "this should work, Hello! This is a simple email message.\n no action required!";
        $from = "cpe-67-10-181-224.satx.res.rr.com";
        $headers = "From:" . $from;

        if( mail($to,$subject,$message,$headers)){
            echo "<p>Mail Sent. to $to</p>";
        }else
            echo "<p>error occured in mail() function for $to</p>";
        }

?>
