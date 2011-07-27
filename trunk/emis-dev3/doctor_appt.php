<html>
    <h1> test</h1>
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
            $numRows = $wsResponse[$wsIndices['APPTCOUNT'][0]]['value'];
            $currRow = 0;

            while ($currRow < $numRows) {
                //$doc = $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'];
                //$type = $wsResponse[$wsIndices['TYPE'][$currRow]]['value'];

                //if ($_SESSION['SESS_PERSONAL_ID'] == $doc && $type == 1) {
                    echo "<tr>";
                    $ID = urlencode($wsResponse[$wsIndices['APPTID'][$currRow]]['value']);
                    echo "<td><a href='visit.php?ID=$ID'>Visit</a></td>";
                    echo "<td>", $wsResponse[$wsIndices['PATID'][$currRow]]['PATID'], "</td>";
                    //echo "<td>", $wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['REASON'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['DATE'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['TIME'][$currRow]]['value'], "</td>";
                    echo "<td>", $wsResponse[$wsIndices['STATUS'][$currRow]]['value'], "</td>";
                    echo "</tr>";
                //}
                $currRow++;
            }
            $currRow = 0;
            
            ?>

        </table>
        <p>test2</p>
    </body>
</html>


