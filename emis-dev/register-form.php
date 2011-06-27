<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System (EMIS) - Registration</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body bgcolor="#aaaaff">
        <center><h1 style="color: white; margin-top: 50px;">Registration</h1></center>
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
                    <form id="loginForm" name="loginForm" method="post" action="register-exec.php">
                        <center><p><b>Welcome to the Electronic Medical Information System. Login or register using the form below.</b></p></center>
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
                        <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
                            <tr>
                                <th>First Name </th>
                                <td><input name="fname" type="text" class="textfield" id="fname" /></td>
                            </tr>

                            <tr>
                                <th>Last Name </th>
                                <td><input name="lname" type="text" class="textfield" id="lname" /></td>
                            </tr>
                            
                            <tr>
                                <th>Birthdate (MMDDYYYY) </th>
                                <td><input name="bday" type="date" class="textfield" id="bday" /></td>
                            </tr>

                            <tr>
                                <th>E-Mail </th>
                                <td><input name="email" type="text" class="textfield" id="email" /></td>
                            </tr>

                            <tr>
                                <th>SSN: </th>
                                <td><input name="ssn" type="text" class="textfield" id="ssn" /></td>
                            </tr>

                            <tr>
                                <th width="124">Username</th>
                                <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
                            </tr>
                            <tr>
                                <th>Password</th>
                                <td><input name="password" type="password" class="textfield" id="password" /></td>
                            </tr>
                            <tr>
                                <th>Confirm Password </th>
                                <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>
                                    <select name="type">
                                        <option value="patient">Patient</option>
                                        <option value="nurse">Nurse</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><a class="black_button" href="javascript: submitform()"><span>Register</span></a></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
