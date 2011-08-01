<?php
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System - Password Reset</title>
        <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
	<script type="text/javascript">
		function submitform()
		{
			document.forms["loginForm"].submit();
		}
	</script>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="memberProfileView.php"><img src="img/logo.png" /></a></div>
            <div class="welcome_text">
                <h1>Welcome,
                <?php
                    echo $_SESSION['SESS_FIRST_NAME']; 
                ?></h1>
            </div>
        </div>
        <div class="contentwrap">
            <div class="navigation">
                <div class="nav_content">
					<?php
                    	include_once "generateNav.php"; // This will generate a navigation menu according to the user's role.
					?>
                </div>
            </div>
            <div class="page_display">
                <div class="page_title">Change Password</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
                    <form id="changePassForm" name="changePassForm" action="changePassExec.php" method="post">
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
                        <table>
                            <tr><td><label><strong>Current Password</strong></label></td></tr>
                            <tr><td><input type="password" name="oldpass" class="textfield" maxlength="20" /></td></tr>
                            <tr><td><label><strong>New Password</strong></label></td></tr>
                            <tr><td><input type="password" name="newpass1"  class="textfield" maxlength="20" /></td></tr>
                            <tr><td><label><strong>Confirm New Password</strong></label></td></tr>
                            <tr><td><input type="password" name="newpass2" class="textfield"maxlength="20" /></td></tr>
                            <tr><td><a class="black_button" style="margin-right: 20px;" href="memberProfileView.php"><span>Cancel</span></a><a class="black_button" href="javascript: submitform()"><span>Reset Password</span></a></td></tr>
                        </table>
                    </form>
                    <!-- END OF PAGE CONTENT -->
                </div>
            </div>
        </div>
        <div class="footer">
        	<p>Electronic Medical Information System. Copyright &copy; 2011 Team B. The University of Texas at San Antonio.</p>
        </div>
	</div>   
</body>
</html>
