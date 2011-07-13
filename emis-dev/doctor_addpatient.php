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
        require_once('auth.php');
        require_once('config.php');
        require_once('bootstrap.php');
    
        //session_start();
		//get doctor id from member---$_SESSION['PK_member_id']
        $qry="SELECT * FROM Patient;";
        $result1=mysql_query($qry);
?>


			<?php
				while ($row1 = mysql_fetch_assoc($result1))
				{
                                    //NULL DOCTOR IS 2 for second itteration
					if($row1['FK_DoctorID'] == 2)
						{
							$qry2="SELECT * FROM Users WHERE PK_member_id = ".$row1['FK_member_id'].";";
							$result2=mysql_query($qry2);
							$row2 = mysql_fetch_assoc($result2);
							echo "<tr>";
							$ID = $row1['PK_PatientID'];
							echo "<td><a href='addpat_exec.php?ID=$ID'>Add</a></td>\n";
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