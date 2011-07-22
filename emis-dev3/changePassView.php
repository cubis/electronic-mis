<?php
session_start();
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
                        document.forms["changePassForm"].submit();
                        }
                    </script>

                    <form id="changePassForm" name="changePassForm" action="changePassExec.php" method="post">
			<center><p><b>Welcome to the Electronic Medical Information System. You may reset your password using the form below.</b></p></center>
			<?php
			if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
				echo '<ul class="err">';
				foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
					echo '<li>', $msg, '</li>';
				}
				echo '</ul>';
				unset($_SESSION['ERRMSG_ARR']);
                        }
                        ?>
			<div class="dashed_line"></div>
			<table  align="center" >
				<tr><td><label><strong>Current Password</strong></label></td></tr>
				<tr><td><input type="password" name="oldpass" class="textfield" maxlength="20" /></td></tr>
				<tr><td><label><strong>New Password</strong></label></td></tr>
				<tr><td><input type="password" name="newpass1"  class="textfield" maxlength="20" /></td></tr>
				<tr><td><label><strong>Confirm New Password</strong></label></td></tr>
				<tr><td><input type="password" name="newpass2" class="textfield"maxlength="20" /></td></tr>
				<tr><td><a class="black_button" href="javascript: submitform()"><span>Reset Password</span></a></td></tr>
				<tr><td><a class="black_button" href="memberProfileView.php"><span>Go Back to Member Profile</span></a></td></tr>
			</table>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
