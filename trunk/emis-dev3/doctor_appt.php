<html>
    <body>
        <h1>Doctor - Appointments</h1>
                <!--Create table to display patients that the doctor is not attached to -->
                <table>
                        <tr>
                                <th>Visit</th>
                                <th>Patient First Name</th>
								<th>Patient Last Name</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Time</th>
								<th>Status</th>
                        </tr>
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
        $id = $row['PK_DoctorID'];
		
		//Get appointments w/docID
		$appts = "SELECT * FROM Appointment WHERE FK_DoctorID = '".$id."';";
		
        //$qry="SELECT * FROM Patient;";
        $result1=mysql_query($appts);
?>


                        <?php
                                while ($row1 = mysql_fetch_assoc($appts))
                                {
                                    $qry2="SELECT * FROM Users WHERE PK_member_id = ".$row1['FK_PatientID'].";";
									$result2=mysql_query($qry2);
                                    $row2 = mysql_fetch_assoc($result2);
									echo "<tr>";
									$ID = $row1['PK_AppID'];
									echo "<td><a href='visit.php?ID=$ID'>Visit</a></td>\n";
									echo "<td>",$row2['FirstName'],"</td>\n";
									echo "<td>",$row2['LastName'],"</td>\n";
									echo "<td>",$row1['Reason'],"</td>\n";
									echo "<td>",$row1['Date'],"</td>\n";
									echo "<td>",$row1['Time'],"</td>\n";
									echo "<td>",$row1['Status'],"</td>\n";
									echo "</tr>";
								}
                                
                        ?>
                </table>
                
        </body>
</html>
