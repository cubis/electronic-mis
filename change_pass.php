<?php
    //headers for session
    require_once('auth.php');       
    require_once('config.php');
    require_once('bootstrap.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Change Password</title>
</head>

<body>
    <h1>Change Password</h1>
    <form action="change_pass.php" method="post">
        <table>
        <tr><td>Current Password: </td><td><input type="password" name="oldpass" size="10" maxlength="20" /></td></tr>
        <tr><td>New Password: </td><td><input type="password" name="newpass1" size="10" maxlength="20" /></td></tr>
        <tr><td>Confirm New Password: </td><td><input type="password" name="newpass2" size="10" maxlength="20" /></td></tr>
        <tr><td></td><td><input type="submit" name="Submit" value="Change Password" /></td></tr>
        </table>
    </form>

    <?php
        $old_pass = $_POST['oldpass'];
        $new_pass1 = $_POST['newpass1'];
        $new_pass2 = $_POST['newpass2'];
        echo "<p>Old Password: $old_pass</p>";
        echo "<p>New Password1: $new_pass1</p>";
        echo "<p>New Password2: $new_pass2</p>";
        echo "<p>Session User is: {$_SESSION['SESS_USERNAME']} </p>";

        if(isset($old_pass)){
            $qry= "SELECT * FROM Users WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
            $result=mysql_query($qry);
            echo "<p>qry $qry</p>";
            echo "<p>result: ".mysql_fetch_array($result)."</p>";
            
            //Check whether the query was successful or not
            if($result) {
                if(mysql_num_rows($result) == 1) {
                    //Login Successful
                    
                    echo "<p>Password changed!</p>";
                    $updateQry = "UPDATE Users SET Password='". md5($_POST['newpass1']) ."' WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
                    echo "<p>update qry: $updateQry</p>";
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
    ?>
</body>
</html>