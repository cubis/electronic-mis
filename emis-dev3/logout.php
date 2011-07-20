<?php
//Start session
session_start();

//HHHHEEEEEEEEEERRRRRRRRRRREEEEEEE
require_once('/configREST.php');     //sql connection information
require_once('/bootstrapREST.php');
logToDB("Logout", true, $_SESSION['SESS_MEMBER_ID']);

//TOOO HHEEEEEEEEEEERRRRRRRRRRRRR
unset($_SESSION['SESS_MEMBER_ID']);
unset($_SESSION['SESS_FIRST_NAME']);
unset($_SESSION['SESS_LAST_NAME']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Logged Out</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">Logged Out</h1></center>
            <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
        <h4 align="center" class="err">You have been logged out.</h4>
        <p align="center"><b>Click here to </b><a href="index.php">Login</a></p>
    </body>
</html>
