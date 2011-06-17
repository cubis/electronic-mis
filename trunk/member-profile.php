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

<?php if($_SESSION['SESS_TYPE'] >= 400): // Logged in user is an Admin ?>
<a href="member-index.php">Home</a> |
<a href="#">View/Edit Members</a> |
<a href="#">View/Approve/Deny Member Request</a> |
<a href="#">Edit Profile</a> |
<a href="#">Change Password</a> |
<a href="logout.php">Logout</a>

<?php elseif($_SESSION['SESS_TYPE'] == 300): // Logged in user is a doctor ?>
You are a doctor!<br />
<a href="member-index.php">Home</a> |
<a href="#">View/Cancel Upcoming Appointments</a> |
<a href="#">View/Edit Availability</a> |
<a href="#">View Patients</a> |
<a href="#">Edit Profile</a> |
<a href="#">Change Password</a> |
<a href="logout.php">Logout</a>

<?php elseif($_SESSION['SESS_TYPE'] == 200): // Logged in user is a nurse ?>
You are a nurse!<br />
<a href="member-index.php">Home</a> |
<a href="#">Edit Profile</a> |
<a href="#">Change Password</a> |
<a href="logout.php">Logout</a>

<?php elseif($_SESSION['SESS_TYPE'] == 1): // Logged in user is a patient ?>
You are a patient!<br />
<a href="member-index.php">Home</a> |
<a href="#">Set Up Appointment</a> |
<a href="logout.php">Logout</a>

<?php else: ?>
I don't know what the fuck you are<br />
<a href="member-index.php">Home</a> | <a href="logout.php">Logout</a>

<?php endif; ?>

<p>This is another secure page. </p>
</body>
</html>
