<?php
//headers for session
require_once('auth.php');
require_once('config.php');
require_once('bootstrap.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System - Password Reset</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <center><h1 style="color: white; margin-top: 50px;">Password Reset</h1></center>
        <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System" />
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                            document.forms["loginForm"].submit();
                        }
                    </script>

                    <form id="loginForm" name="loginForm" action="change_pass.php" method="post">
                        <center><p>Welcome to the Electronic Medical Information System (EMIS). You may reset your password using the form below.</p></center>
                        <div class="dashed_line"></div>
                        <table  align="center" >
                            <tr><td><label><strong>Current Password</strong></label></td></tr><tr><td><input type="password" name="oldpass" class="textfield" maxlength="20" /></td></tr>
                            <tr><td><label><strong>New Password</strong></label></td></tr><tr><td><input type="password" name="newpass1"  class="textfield" maxlength="20" /></td></tr>
                            <tr><td><label><strong>Confirm New Password</strong></label></td></tr><tr><td><input type="password" name="newpass2" class="textfield"maxlength="20" /></td></tr>
                            <tr><td></td></tr><tr><td><a class="black_button" href="javascript: submitform()"><span>Reset Password</span></a></td></tr>
                        </table>
                    </form>
                    <center>  <p style="color: red;">
                            <?php
                            if (isset($_POST['oldpass'])) {

                                if ($_POST['newpass1'] == $_POST['newpass2']) {
                                    $qry = "SELECT * FROM Users WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='" . md5($_POST['oldpass']) . "'";
                                    $result = mysql_query($qry);

                                    $oldpass = $_POST['oldpass'];
                                    $newpass1 = $_POST['newpass1'];
                                    $newpass2 = $_POST['newpass2'];

                                    //Check whether the query was successful or not
                                    if ($result) {
                                        if (mysql_num_rows($result) == 1) {

                                            //problems with new password
                                            if ($oldpass == $newpass1) {
                                                $errmsg_arr[] = 'New and old passwords must be different';
                                                echo "<p style=\"color: red;\">Error: New and old passwords must be different.</p>";
                                                $errflag = true;
                                            }
                                            if (!ctype_alnum($newpass1)) {
                                                $errmsg_arr[] = 'New password should be numbers & Digits only';
                                                echo "<p style=\"color: red;\">Error: New password should be numbers & Digits only.</p>";
                                                $errflag = true;
                                            }
                                            if (strlen($newpass1) < 7) {
                                                $errmsg_arr[] = 'New password must be at least 7 chars';
                                                echo "<p style=\"color: red;\">Error: New password must be at least 7 chars.</p>";
                                                $errflag = true;
                                            }
                                            if (strlen($newpass1) > 20) {
                                                $errmsg_arr[] = 'New password must be at most 20 chars';
                                                echo "<p style=\"color: red;\">Error: New password must be at most 20 chars.</p>";
                                                $errflag = true;
                                            }
                                            if (!preg_match('`[A-Z]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one upper case';
                                                echo "<p style=\"color: red;\">Error: New password must contain at least one upper case.</p>";
                                                $errflag = true;
                                            }
                                            if (!preg_match('`[a-z]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one lower case';
                                                echo "<p style=\"color: red;\">Error: New password must contain at least one lower case.</p>";
                                                $errflag = true;
                                            }
                                            if (!preg_match('`[0-9]`', $newpass1)) {
                                                $errmsg_arr[] = 'New password must contain at least one digit';
                                                echo "<p style=\"color: red;\">Error: New password must contain at least one digit.</p>";
                                                $errflag = true;
                                            }

                                            if ($errflag == true) {
                                                $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
                                                //session_write_close();
                                                //header("location: change_pass.php"); 
                                                exit();
                                            }

                                            //Login Successful
                                            echo "<p style=\"color: red;\">Your password has been reset!</p>";
                                            $updateQry = "UPDATE Users SET Password='" . md5($_POST['newpass1']) . "' WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='" . md5($_POST['oldpass']) . "'";
                                            mysql_query($updateQry) or die(mysql_error());
                                            exit();
                                        } else {
                                            //Login failed or old password is wrong
                                            $errmsg_arr[] = 'Old password is wrong';
                                            $errflag = true;
                                            echo "<p style=\"color: red;\">Error: Old password is wrong.</p>";
                                            echo "<p style=\"color: red;\">Fail!</p>";
                                            $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
                                            exit();
                                        }
                                    } else {
                                        echo "<p style=\"color: red;\">Result was empty!</p>";
                                    }
                                } else {
                                    $errmsg_arr[] = 'New Passwords do not match';
                                    $errflag = true;
                                    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
                                    echo "<p style=\"color: red;\">Error: New Passwords do not match.</p>";
                                }
                            }
                            ?>
                    </center>
                </div>
            </div>
        </div>
    </body>
</html>