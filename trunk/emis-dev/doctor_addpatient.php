<?php
	//require_once('auth.php');
	require_once('config.php');
	require_once('bootstrap.php');
	?>
<html>
    <body>
        <h1>Doctor - Add Patient</h1>
		<!--Create table to display patients that the doctor is not attached to -->
		<table>
			<tr>
				<td>Add</td>
				<td>First Name</td>
				<td>Last Name</td>
				<td>Sex</td>
				<td>Email</td>
				<td>Birthday</td>
				<td>Phone Number</td>
				<td>SSN</td>
			</tr>
<?php

	//session_start();
	//get doctor id from member---$_SESSION['PK_member_id']
	
	$user = $_SESSION['SESS_USERNAME'];
	$key = $_SESSION['SESS_AUTH_KEY'];
	$request = "http://localhost/emis/emis-dev/getMemberInfoREST.php?u=".urlencode($user)."&key=".urlencode($key);
	//format and send request
	$ch = curl_init($request);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); //send URL Request to RESTServer... returns string
	curl_close($ch); //string from server has been returned <XML> closethe channel

	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
	//print("OUTPUT = ".$output."\n");
	//print("name = ".$wsResponse[$wsIndices['FIRSTNAME'][0]]['value']."\n");
	
	if( $output == ''){
		die("CONNECTION ERROR ");
	}	
	$doctorID = $wsResponse[$wsIndices['ID'][0]]['value'];
	echo "$doctorID";
	
	$qry="SELECT * FROM Patient;";
	$result1=mysql_query($qry);

	while ($row1 = mysql_fetch_assoc($result1))
	{
		if($row1['FK_DoctorID'] == NULL)
		{
			$qry2="SELECT * FROM Users WHERE PK_member_id = ".$row1['FK_member_id'].";";
			$result2=mysql_query($qry2);
			$row2 = mysql_fetch_assoc($result2);
			echo "<tr>";
			$patientID = $row1['PK_PatientID'];
			echo "<td>\n";
			echo "<form name='addPatientForm' method='post' action='addPatientExec.php'>\n";
			echo "<input type='hidden' name='patientID' value='$patientID' />\n";
			echo "<input type='hidden' name='doctorID' value='$doctorID' />\n";
			echo "<input type='submit' value='Add' />";
			echo "</form>";
			echo "</td>\n";
			echo "<td>",$row2['FirstName'],"</td>\n";
			echo "<td>",$row2['LastName'],"</td>\n";
			echo "<td>",$row2['Sex'],"</td>\n";
			echo "<td>",$row2['Email'],"</td>\n";
			echo "<td>",$row2['Birthday'],"</td>\n";
			echo "<td>",$row2['PhoneNumber'],"</td>\n";
			echo "<td>",$row2['SSN'],"</td>\n";
			
			echo "</tr>";
		}
	}

?>

</table>		
</body>
</html>