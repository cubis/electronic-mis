<?php
	require_once('auth.php');
	require_once('config.php');
	require_once('bootstrap.php');
	//session_start();
	$did = $row['PK_member_id'];
	$pid = $_GET['ID'];
	//$qry="SELECT * FROM Patients";
	//$result1=mysql_query($qry);
	$upquer = "UPDATE Patient SET FK_DoctorID = 'NULL' WHERE PK_PatientID = '".$pid."'";
	
	mysql_query($upquer)
?>

<html>
	<h1>Patient Removed</h1>
		<body>
			<td><a href='member-profile.php'>Home</a></td>
		</body>
</html>