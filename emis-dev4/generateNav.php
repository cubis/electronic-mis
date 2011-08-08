<?php if ($_SESSION['SESS_NEED_APPROVAL'] == 1): // Logged in user is waiting approval ?>
	<div class="nav_title">Pending Approval</div>
    <div class="navlist">
    	<p>Your account is pending administrative approval.</p>
    </div>
<?php elseif ($_SESSION['SESS_TYPE'] >= 400): // Logged in user is an Admin ?>
	<div class="nav_title">Administrator</div>
	<div class="navlist"><a href="admin/editMemberListView.php">View/Edit Members</a></div>
	<div class="navlist"><a href="admin/approveRequestsView.php">View/Approve/Deny Member Request</a></div>
<?php elseif ($_SESSION['SESS_TYPE'] == 300): // Logged in user is a doctor ?>
	<div class="nav_title">Doctor</div>
	<div class="navlist"><a href="#">View/Cancel Upcoming Appointments</a></div>
	<div class="navlist"><a href="#">View/Edit Availability</a></div>
	<div class="navlist"><a href="doctorAddRemovePatientView.php">Add/Remove Patients</a></div> 
    <div class="navlist"><a href="editMemberView.php?u=<?php echo $_SESSION['SESS_USERNAME'] ?>">Edit Profile</a></div>
	<div class="navlist"><a href="apptDoctorView.php">Appointments</a></div>
<?php elseif ($_SESSION['SESS_TYPE'] == 200): // Logged in user is a nurse ?>
	<div class="nav_title">Nurse</div>
	<div class="navlist"><a href="editMemberView.php">Edit Profile</a></div>
<?php elseif ($_SESSION['SESS_TYPE'] == 1): // Logged in user is a patient ?>
	<div class="nav_title">Patient</div>
	<div class="navlist"><a href="appointment.php?u=<?php echo $_SESSION['SESS_USERNAME'] ?>">Set Up Appointments</a></div>
	<div class="navlist"><a href="editMemberView.php?u=<?php echo $_SESSION['SESS_USERNAME'] ?>">Edit Profile Information</a></div>
	<div class="navlist"><a href="patientInfoView.php">View Medical Information</a></div>
    <div class="navlist"><a href="apptPatientView.php">View Appointments</a></div>
<?php else: ?>
	<div class="nav_title">Access Denied</div>
	<div class="navlist"><a href="accessDeniedView.php">Home</a></div>
    <div class="navlist"><a href="logout.php">Logout</a></div>
<?php endif; ?>
	<div class="navlist"><a href="changePassView.php">Change Password</a></div>
    <div class="navlist"><a href="http://localhost/emis/emis-dev4/logoutExec.php">Logout</a></div>