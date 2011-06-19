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
<title>Change Password</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <h1  align="center" >Change Password</h1>
    <form action="change_pass.php" method="post">
        <table  align="center" >
        <tr><td>Current Password: </td><td><input type="password" name="oldpass" size="10" maxlength="20" /></td></tr>
        <tr><td>New Password: </td><td><input type="password" name="newpass1" size="10" maxlength="20" /></td></tr>
        <tr><td>Confirm New Password: </td><td><input type="password" name="newpass2" size="10" maxlength="20" /></td></tr>
        <tr><td></td><td><input type="submit" name="Submit" value="Change Password" /></td></tr>
        </table>
    </form>

    <?php

        if(isset($_POST['oldpass']) ){
            if($_POST['newpass1'] == $_POST['newpass2']){
                $qry= "SELECT * FROM Users WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
                $result=mysql_query($qry);

                //Check whether the query was successful or not
                if($result) {
                    if(mysql_num_rows($result) == 1) {
                        //Login Successful

                        echo "<p>Password changed!</p>";
                        $updateQry = "UPDATE Users SET Password='". md5($_POST['newpass1']) ."' WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
                        mysql_query($updateQry) or die(mysql_error());
                        exit();
                    }
                    else {
                        //Login failed
                        echo "<p>FAIL!!</p>";
                        header("location: login-failed.php");
                        exit();
                    }
                }
                else{
                    echo "<p>Result was empty!</p>";
                }
            }
            else{
                echo "<p>ERROR: New Passwords do not match</p>";
            }
        }


    ?>
</body>
</html>