<?php
require_once('auth.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>My Profile</title>
        <link href="loginmodule.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h1>My Profile </h1>
<p>User Menu</p>
<?php if ($_SESSION['SESS_NEED_APPROVAL'] == 1): // Logged in user is waiting approval ?>
    <p>You are a nobody!</p<br />
    <p>You can not perform any actions as your account is waiting for admin approval.</p>
    <a href="logout.php">Logout</a>
<?php elseif ($_SESSION['SESS_TYPE'] >= 400): // Logged in user is an Admin ?>
    <a href="admin/edit_members.php">View/Edit Members</a> |
    <a href="admin/approve_requests.php">View/Approve/Deny Member Request</a> |
    <a href="#">Edit Profile</a> |
    <a href="logout.php">Logout</a>
<?php elseif ($_SESSION['SESS_TYPE'] == 300): // Logged in user is a doctor ?>
    <p>You are a doctor!</p><br />
    <a href="#">View/Cancel Upcoming Appointments</a> |
    <a href="#">View/Edit Availability</a> |
    <a href="#">View Patients</a> |
    <a href="#">Edit Profile</a> |
    <a href="logout.php">Logout</a>
<?php elseif ($_SESSION['SESS_TYPE'] == 200): // Logged in user is a nurse ?>
    <p>You are a nurse!</p><br />
    <a href="#">Edit Profile</a> |
    <a href="logout.php">Logout</a>
<?php elseif ($_SESSION['SESS_TYPE'] == 1): // Logged in user is a patient ?>
    <p>You are a patient!</p><br />
    <a href="#">Set Up Appointment</a> |
    <a href="logout.php">Logout</a>
<?php elseif ($_SESSION['SESS_TYPE'] == 0): // Logged in user is waiting approval ?>
    <p>You are a nobody!</p><br />
    <p>You can not perform any actions as your account is waiting for admin approval.</p>
    <a href="logout.php">Logout</a>
<?php else: ?>
    I don't know who you are<br />
    <a href="member-index.php">Home</a> | <a href="logout.php">Logout</a>
<?php endif; ?>
    <p>Your Account Settings. </p>
    <a href="change_pass.php">Change Password</a>
</body>
</html>
