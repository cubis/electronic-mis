<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Electronic Medical Information System (EMIS)</title>
    <link type="text/css" rel="stylesheet" href="css/styles.css">
      <?php require('calendar/tc_calendar.php');?>
</head>

<body OnLoad="document.login_Form.login.focus();">
	<div id="container">
        <div id="header">
            <div id="logo"><a href="index.php"><img src="img/logo.png" alt="Electronic Medical Information System" /></a></div>
       		<div id="loginbox">
				<form id="loginForm" name="login_Form" method="post" action="loginExec.php">
					<table>
                    	<tr>
                    		<td><label>Username</label></td>
							<td><label>Password</label></td>
							<td>&nbsp;</td>
                    	</tr>
                    	<tr>
                    		<td><input name="login" type="text" class="loginfield" id="login" /></td>
                    		<td><input name="password" type="password" class="loginfield" id="password" /></td>
                            <td><input type="submit" value="Log In" style="width: auto; font-weight: bold; font-size: 9px;" /></td>
                    	</tr>
                        <tr>
                        	<td>
                            	<input type="checkbox" checked="1" name="persistent" id="persistent" value="1" style="width: auto;">
                            	<label for="persistent" style="top: -2px; vertical-align: middle;">Keep me logged in</label>
                            </td>
                        	<td><label><a href="#">Forgot your password?</a></label></td>
                        	<td>&nbsp;</td>
						</tr>
                    </table>
				</form>
			</div>
		</div>
    	<div id="contentwrap">
        	<div id="infobox">
            	<h2>A core medical system that can be contributed to and shared by primary and secondary healthcare professionals and patients.</h2>
                <img src="img/infopic.png" />
            </div>
            <div id="registerbox">
            	<h2>Sign Up</h2>
                <p class="h2detail">Consolidate your records and make life easy.</p>
                <form id="registerForm" name="registerForm" method="post" action="registerExec.php">
                        <table border="0" cellspacing="0">
                            <tr>
                                <th><label>First Name:</label></th>
                                <td><input name="fname" type="text" class="textfield" id="fname" /></td>
                            </tr>
                            <tr>
                                <th><label>Last Name:</label></th>
                                <td><input name="lname" type="text" class="textfield" id="lname" /></td>
                            </tr>
                            <tr>
				<th><label>Birthdate:</label></th>
                                <td>
				  <?php
				    // Request selected language
				    $hl = (isset($_POST["hl"])) ? $_POST["hl"] : false;
				    if(!defined("L_LANG") || L_LANG == "L_LANG")
				    {
				    if($hl) define("L_LANG", $hl);
				    else define("L_LANG", "en_GB");
				    }
				    $myCalendar = new tc_calendar("bday", true, false);
				    $myCalendar->setIcon("calendar/images/iconCalendar.gif");
				    $myCalendar->setDate(date('d'), date('m'), date('Y'));
				    $myCalendar->setPath("calendar/");
				    $myCalendar->setAlignment('left', 'bottom');
				    $myCalendar->setYearInterval(1900, 2015);
				    $myCalendar->setDateFormat('j F Y');
				    $myCalendar->writeScript();
				  ?>
				</td>
                            </tr>
                            <tr>
                                <th><label>Email:</label></th>
                                <td><input name="email" type="text" class="textfield" id="email" /></td>
                            </tr>
                            <tr>
                                <th><label>Social Security:</label></th>
                                <td><input name="ssn" type="text" class="textfield" id="ssn" /></td>
                            </tr>
                            <tr>
                                <th><label>Username:</label></th>
                                <td><input name="login" type="text" class="textfield" id="login" /></td>
                            </tr>
                            <tr>
                                <th><label>Password:</label></th>
                                <td><input name="password" type="password" class="textfield" id="password" /></td>
                            </tr>
                            <tr>
                                <th><label>Confirm Password:</label></th>
                                <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
                            </tr>
                            <tr>
                                <th><label>Account Type:</label></th>
                                <td>
                                    <select name="type">
                                        <option value="patient">Patient</option>
                                        <option value="nurse">Nurse</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <input type="submit" value="Register" style="width: auto; font-weight: normal; font-size: 16px; float: right;" />
                                </td>
                            </tr>
                        </table>
                    </form>
            </div>
        </div>
        <div id="footer">
        	<div class="footerbox" style="width: 250px;">
            	<h2>Our Team</h2>
                <p>Basil Sattler<br />
                Juan Perez<br />
                Daniel Curran<br/>
                William Mills<br />
                Mu Li<br />
                Lawrence Gadban<br />
                Scott Amble<br />
                Bradley Gillard<br />
                Cameron Lopez<br />
                David Gorena</p>
            </div>
			<div class="footerbox" style="margin-right: 50px; margin-left: 50px;">
            	<h2>About Our System</h2>
                <p>The Electronic Medical Information System (EMIS) provides open access to health professionals and patients.
                The advantage of an open medical system is that it puts vital information at the fingertips of those who need
                it most, when they need it most, through secure data sharing.</p>
            </div>
            <div class="footerbox" style="float: right; margin-left: 10px;">
            	<center><h2>The University of Texas<br />at San Antonio</h2>
                <img src="img/utsalogo.png" alt="The University of Texas at San Antonio" />
                <p style="font-size: 11px; margin-top: 0px;">#whippedintoshape</p></center>
            </div>
        </div>
	</div>
</body>
</html>
