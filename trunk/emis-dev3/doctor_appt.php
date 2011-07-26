<html>
    <h1> test</h1>
    <?php
    /*
    if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
        echo '<ul class="err">';
        foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
            echo '<li>', $msg, '</li>';
        }
        echo '</ul>';
        unset($_SESSION['ERRMSG_ARR']);
     * 
     */
    }
    ?>
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
            $url = "http://localhost/emis/emis-dev3/visitREST.php?u=" . urlencode($_SESSION['SESS_USERNAME']) . "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);

            $parser = xml_parser_create();
            //modify
            xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
            $numRows = $wsResponse[$wsIndices['PATIENTCOUNT'][0]]['value'];
            $currRow = 0;

            while ($currRow < $numRows) {
                $doc = $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'];
                $type = $wsResponse[$wsIndices['TYPE'][$currRow]]['value'];

                if ($_SESSION['SESS_PERSONAL_ID'] == $doc && $type == 1) {
                    echo "<tr>";
                    $ID = urlencode($wsResponse[$wsIndices['APPTID'][$currRow]]['value']);
                    echo "<td><a href='visit.php?ID=$ID'>Visit</a></td>";
                    $ID = urlencode($wsResponse[$wsIndices['PATIENTID'][$currRow]]['value']);
                    echo "<td>", $wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['REASON'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['DATE'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['TIME'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['STATUS'][$currRow]]['value'], "</td>";
                    echo "</tr>";
                }
                $currRow++;
            }
            $currRow = 0;
            ?>

        </table>

    </body>
</html>
<?php
/*
  require_once('auth.php');
  require_once('config.php');
  require_once('bootstrap.php');

  //session_start();
  //get doctor id from member---$_SESSION['PK_member_id']
  $docid = "SELECT * FROM Doctor WHERE FK_member_id = '" . $_SESSION['SESS_MEMBER_ID'] . "';";
  $result = mysql_query($docid) or die(mysql_error());
  //echo "<hr>";
  $row = mysql_fetch_assoc($result) /* or die(mysql_error()) ;
  $id = $row['PK_DoctorID'];

  //Get appointments w/docID
  $appts = "SELECT * FROM Appointment WHERE FK_DoctorID = '" . $id . "';";

  //$qry="SELECT * FROM Patient;";
  $result1 = mysql_query($appts);
  echo "test" . $result1;
 * 
 */
?>


<?php
/*
  while ($row1 = mysql_fetch_assoc($appts)) {
  //$qry2 = "SELECT * FROM Users WHERE PK_member_id = " . $row1['FK_PatientID'] . ";";
  //$result2 = mysql_query($qry2);
  //$row2 = mysql_fetch_assoc($result2);
  echo "<tr>";
  $ID = $row1['PK_AppID'];
  echo "<td><a href='visit.php?ID=$ID'>Visit</a></td>";
  echo "<td>", $row2['FirstName'], "</td>";
  echo "<td>", $row2['LastName'], "</td>";
  echo "<td>", $row1['Reason'], "</td>";
  echo "<td>", $row1['Date'], "</td>";
  echo "<td>", $row1['Time'], "</td>";
  echo "<td>", $row1['Status'], "</td>";
  echo "</tr>";
 * 
 */
}
?>
 