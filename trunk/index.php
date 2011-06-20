<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Welcome to the Electronic Medical Information System</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<center><h1 style="color: white; margin-top: 50px;">Welcome!</h1></center>
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
                <form id="loginForm" name="login_Form" method="post" action="login-exec.php">
                	<center><p>Welcome to the Electronic Medical Information System (EMIS). Login or register using the form below.</p></center>
                    <div class="dashed_line"></div>
                    <div class="form_fields">
                        <label><strong>Username</strong><br /><input name="login" type="text" class="textfield" id="login" /></label><br />
                        <label><strong>Password</strong><br /><input name="password" type="password" class="textfield" id="password" /></label><br />
                    </div>
                    <a class="black_button" style="margin-right: 60px;" href="javascript: submitform()"><span>Login</span></a>
                    <a class="black_button" href="register-form.php"><span>Register</span></a>
                </form>
            </div>
    	</div>
    </div>
</body>
</html>

