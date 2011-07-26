<?php
session_start();
?>
<html>
    <body>
        <h1>Doctor - Remove Patient</h1>
		<!--Create table to display patients that the doctor is not attached to -->
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
		
		<table>    
		<th></th><th></th><th><center> MY PATIENTS </center></th>
			<tr>
				<td>Remove</td>
				<td>First Name</td>
				<td>Last Name</td>
				<td>Sex</td>
				<td>Email</td>
				<td>Birthday</td>
				<td>Phone Number</td>
				<td>SSN</td>
			</tr>
<?php

	$url = "http://localhost/emis/emis-dev3/viewPatientREST.php?u=" . urlencode($_SESSION['SESS_USERNAME']) . "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']) . "&pat=" . urlencode("all");
    
    
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
   
		$parser = xml_parser_create();
		
		xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
		
	$numRows = $wsResponse[$wsIndices['PATIENTCOUNT'][0]]['value'];
	$currRow = 0;
	
	while ($currRow < $numRows){
		$doc = $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'];
		$type = $wsResponse[$wsIndices['TYPE'][$currRow]]['value'];
		
		if($_SESSION['SESS_PERSONAL_ID'] == $doc && $type == 1){
			echo "<tr>";
			$ID = $wsResponse[$wsIndices['PK_PatientID'][$currRow]]['value'];
			echo "<td><a href='doctorRemovePatientExec.php?ID=$ID&do=remove'>Remove</a></td>\n";
			echo "<td>",$wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['SEX'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['EMAIL'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['BIRTHDAY'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['PHONENUMBER'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['SSN'][$currRow]]['value'],"</td>\n";
			echo "</tr>";
		}
		$currRow++;
	}
	$currRow = 0;
	
?>
		</table>
		
		<table>
		<th></th><th></th><th><center> NOT MY PATIENTS </center></th>
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

	while ($currRow < $numRows){
		$doc = $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'];
		if(!isset( $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'] ) && $wsResponse[$wsIndices['TYPE'][$currRow]]['value'] == 1){
			echo "<tr>";
			$ID = $wsResponse[$wsIndices['PK_PatientID'][$currRow]]['value'];
			echo "<td><a href='doctorRemovePatientExec.php?ID=$ID&do=add'>Add</a></td>\n";
			echo "<td>",$wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['SEX'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['EMAIL'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['BIRTHDAY'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['PHONENUMBER'][$currRow]]['value'],"</td>\n";
			echo "<td>",$wsResponse[$wsIndices['SSN'][$currRow]]['value'],"</td>\n";
			echo "</tr>";
		}
		$currRow++;
	}
	
	

?>
		
		</table>
		
		
		
		
	</body>
</html>