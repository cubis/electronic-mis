<?php
	require_once('auth.php');
	require_once('config.php');
	require_once('bootstrap.php');
	//session_start();
	//get doctor id from member---$_SESSION['PK_member_id']
	$docid = "SELECT * FROM Doctor WHERE FK_member_id = '".$_SESSION['SESS_MEMBER_ID']."';";
        $result = mysql_query($docid)or die(mysql_error());
        //echo "<hr>";
        $row = mysql_fetch_assoc($result) /*or die(mysql_error())*/;
        $did = $row['PK_DoctorID'];
	$pid = $_GET['ID'];
	//$qry="SELECT * FROM Patients";
	//$result1=mysql_query($qry);
	$upquer = "UPDATE Patient SET FK_DoctorID = '".$did. "' WHERE PK_PatientID = '".$pid."'";
	
	mysql_query($upquer);
?>

<html>
	<h1>Patient_added</h1>
		<body>
			<td><a href='member-profile.php'>Home</a></td>
		</body>
</html>