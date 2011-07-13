<?php
	require_once('config.php');
	require_once('bootstrap.php');
    
	$doctorMemberID = $_POST['doctorID'];
	$qry="SELECT PK_DoctorID FROM Doctor WHERE FK_member_id='" . $doctorMemberID . "'";
	$result = mysql_query($qry);
	$row = mysql_fetch_assoc($result);
	$doctorPK = $row["PK_DoctorID"];
	
	$fields = array
	(
		'u' => urlencode($_SESSION['SESS_USERNAME']),
		'key' => urlencode($_SESSION['SESS_AUTH_KEY']),
		'patientID' => urlencode($_POST['patientID']),
		'doctorID' => urlencode($doctorPK),
		'doctorMemberID' => urlencode($doctorMemberID)
	);
    
	foreach($fields as $key=>$value)
		$field_string .= $key.'='.$value.'&';
	
	rtrim($field_string, '&');
	
	$url = "http://localhost/emis/emis-dev/addPatientREST.php?u=" . urlencode($user) . "&key=" . urlencode($key);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 4);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);    
	curl_close($ch);
   
	
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
	
	$result = $wsResponse[$wsIndices['RESULT'][0]]['value'];
	$msg = $wsResponse[$wsIndices['MESSAGE'][0]]['value'];
	$id = $_POST['ID'];
	header("location: member-profile.php");
	print("$result<br />\n");
	print("$msg\n");
	exit();
?>
