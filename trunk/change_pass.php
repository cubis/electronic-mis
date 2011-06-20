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
                        if(isset($_POST['oldpass']) ){
                            if($_POST['newpass1'] == $_POST['newpass2']){
                                $qry= "SELECT * FROM Users WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
                                $result=mysql_query($qry);
                
                                //Check whether the query was successful or not
                                if($result) {
                                    if(mysql_num_rows($result) == 1) {
                                        //Login Successful
                
                                        echo "<p style=\"color: red;\">Your password has been reset!</p>";
                                        $updateQry = "UPDATE Users SET Password='". md5($_POST['newpass1']) ."' WHERE UserName='{$_SESSION['SESS_USERNAME']}' AND Password='".md5($_POST['oldpass'])."'";
                                        mysql_query($updateQry) or die(mysql_error());
                                        exit();
                                    }
                                    else {
                                        //Login failed
                                        echo "<p style=\"color: red;\">Fail!</p>";
                                        header("location: login-failed.php");
                                        exit();
                                    }
                                }
                                else{
                                    echo "<p style=\"color: red;\">Result was empty!</p>";
                                }
                            }
                            else{
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