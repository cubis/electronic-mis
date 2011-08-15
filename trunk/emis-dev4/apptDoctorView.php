<?php
require_once("auth.php");
require_once("bootstrap.php");
restrictAccess (0011);
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Electronic Medical Information System - User Profile</title>
    <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
</head>

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
	<script type="text/javascript">
		function submitform()
		{
			document.forms["loginForm"].submit();
		}
	</script>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/horizontal_logo.png" ALT="just another logo"/></div>
            <div class="welcome_text">
                <h1>Welcome,
                <?php
                    echo $_SESSION['SESS_FIRST_NAME']; 
                ?></h1>
            </div>
        </div>
        <div class="contentwrap">
            <div class="navigation">
                <div class="nav_content">
					<?php
                    	include_once "generateNav.php"; // This will generate a navigation menu according to the user's role.
					?>
                </div>
            </div>
            <div class="page_display">
                <div class="page_title">Doctor Appointment</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
        <!--Create table to display patients that the doctor is not attached to -->
        <table>
            <tr>
                <th>Visit</th>
                <th>Last Name</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>

            </tr>

<?php
global $currentPath;
$url = $currentPath . "apptViewREST.php?u=" . urlencode($_SESSION['SESS_USERNAME']) . "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);

//print("OUTPUT = " . $output);


$parser = xml_parser_create();
//modify
xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
//echo $output;
$numRows = $wsResponse[$wsIndices['APPTCOUNT'][0]]['value'];
//echo $numrows;
$currRow = 0;
//echo $currRow;
while ($currRow < $numRows) {
    //$doc = $wsResponse[$wsIndices['FKDOCTORID'][$currRow]]['value'];
    //$type = $wsResponse[$wsIndices['TYPE'][$currRow]]['value'];
    //if ($_SESSION['SESS_PERSONAL_ID'] == $doc && $type == 1) {
    echo "<tr>";
    $ID = urlencode($wsResponse[$wsIndices['APPTID'][$currRow]]['value']);
    echo "<td><a href='visit.php?ID=$ID'>Visit</a></td>";
    echo "<td>", $wsResponse[$wsIndices['PATNAME'][$currRow]]['value'], "</td>";
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
<!-- END OF PAGE CONTENT -->
                </div>
            </div>
        </div>
        <div class="footer">
        	<p>Electronic Medical Information System. Copyright &copy; 2011 Team B. The University of Texas at San Antonio.</p>
        </div>
	</div>
</html>


