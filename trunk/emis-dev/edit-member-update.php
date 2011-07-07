<?php
    //headers for session
    session_start();
    $connection = @mysql_connect("devdb.fulgentcorp.com","495311team2user","680c12D5!gP592xViF") or die(mysql_error());
    $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());
    $table_name = "Users";

    $ID = $_POST['ID'];
    $f_name = $_POST['Firstname'];
    $l_name = $_POST['Lastname'];
    $sex = $_POST['Sex'];
    $email = $_POST['Email'];
    $birthday = $_POST['Birthday'];
    $phone = $_POST['Phonenumber'];
    $ssn = $_POST['SSN'];
//  $address = $_POST['Address'];
//  $policy = $_POST['Policy'];

       $updateQry = "UPDATE $table_name Set FirstName='$f_name',LastName='$l_name',Sex='$sex',Email='$email',Birthday='$birthday',PhoneNumber='$phone',SSN='$ssn' WHERE UserName = '{$_SESSION['SESS_USERNAME']}'";
    mysql_query($updateQry) or die(mysql_error());
    header("location: member-profile.php");
    exit();
?>
~                   
