<<<<<<< .mine
<?php
	require_once('auth.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Electronic Medical Information System - User Profile</title>
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
            <div class="logo"><img src="img/horizontal_logo.png" /></div>
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
                    	include_once "generate_nav.php"; // This will generate a navigation menu according to the user's role.
					?>
                </div>
            </div>
            <div class="page_display">
                <div class="page_title">Profile</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
                	<p>This is where the page content goes.</p>
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
=======
<?php
require_once('auth.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>My Profile</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">My Profile</h1></center>
            <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
<?php if ($_SESSION['SESS_NEED_APPROVAL'] == 1): // Logged in user is waiting approval ?>
    <center>
        <p><b>You are a nobody!</b></p>
        <p><b>You can not perform any actions, because is waiting for admin approval.</b></p>
    </center>
<?php elseif ($_SESSION['SESS_TYPE'] >= 400): // Logged in user is an Admin ?>
    <center>
        <p><b>You Are An Admin</b></p>
        <div class="dashed_line"></div>
        <p><b>Your Menu Options</b></p>
        <a href="admin/editMembersView.php">View/Edit Members</a>
        <br> 
        <a href="admin/approveRequestsView.php">View/Approve/Deny Member Request</a>
        <br>
    </center>
<?php elseif ($_SESSION['SESS_TYPE'] == 300): // Logged in user is a doctor ?>
    <center>
        <p><b>You are a doctor!</b></p>
        <div class="dashed_line"></div>
        <label><strong>User Menu</strong></label>
        <br>
        <a href="#">View/Cancel Upcoming Appointments</a> 
        <br>
        <a href="#">View/Edit Availability</a> 
        <br>
        <a href="doctorAddRemovePatientView.php">Add/Remove Patients</a> 
        <br>
        <a href="editMemberView.php">Edit Profile</a> 
        <br>
        <a href="doctor_appt.php">Appointments</a> 
    </center>
<?php elseif ($_SESSION['SESS_TYPE'] == 200): // Logged in user is a nurse ?>
    <center>
        <p><b>You are a nurse!</b></p>
        <div class="dashed_line"></div>
        <label><strong>User Menu</strong></Label>
        <br>
        <a href="editMemberView.php">Edit Profile</a> 
    </center>
<?php elseif ($_SESSION['SESS_TYPE'] == 1): // Logged in user is a patient ?>
    <center><p><b>You Are A Patient</b></p></center>
    <div class="dashed_line"></div>
    <center>
        <label><strong>User Menu</strong></label>
        <br>
        <a href="appointment.php">Set Up Appointments</a> <br>
        <a href="editMemberView.php">Edit Profile Information</a><br>
        <a href="patientInfoView.php">View Medical Information</a>
    </center>
<?php else: ?>
    I don't know who you are<br />
    <a href="accessDeniedView.php">Home</a> | <a href="logout.php">Logout</a>
<?php endif; ?>
    <div class="dashed_line"></div>
    <center>
        <label><strong>Your Account Settings</strong></label>
        <br>
        <a href="changePassView.php">Change Password</a>
        <br>
        <a class="black_button" href="logoutExec.php"><span>Logout</span></a>
    </center>
</body>
</html>
>>>>>>> .r386
