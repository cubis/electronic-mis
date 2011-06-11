B Sattler is an ass
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Form</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1> Welcome Please Login</h1>
<p>&nbsp;</p>
<form id="loginForm" name="loginForm" method="post" action="login-exec.php">
<form id="loginForm" name="loginForm" method="post" action="register-form.php">
  <table width="220" border="1" align="center" cellpadding="2" cellspacing="3">
    <tr>
      <td width="112"><b>Login:</b></td>
      <td width="188"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <td><b>Password:</b></td>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
    <table align="center">
      <td><input type="submit" name="Submit" value="Login" />
	  <input type=button onClick="parent.location='http://cs.utsa.edu/~bsattler/register-form.php'" value='Create User'>
	  <input type=button onClick="parent.location='http://cs.utsa.edu/~bsattler/register-form.php'" value='   Change Password  '>
    </tr>
  </table>
  
</form>

</body>
</html>
