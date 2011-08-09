

<?php
require_once('auth.php');
require_once('bootstrap.php');

$userName = $_SESSION['SESS_USERNAME'];

global $currentPath;
$request = $currentPath . "apptViewREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel



if( $RESToutput == ''){
  die("CONNECTION ERROR");
}

//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
xml_parser_free($p);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
if ($errNum != 0) {
	$ct = 0;
	while($ct < $errNum){
		$err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
		$ct++;
	}
	$_SESSION['ERRMSG_ARR'] = $err_msg_arr;
}
$numRows = $wsResponse[$wsIndices['APPTCOUNT'][0]]['value'];
$appointments = array();
for($x = 0 ; $x < $numRows; $x++) { // For each appointment, add to $appointments
  $aid = $wsResponse[$wsIndices['APPTID'][$x]]['value'];
  $adoctor = $wsResponse[$wsIndices['DOCNAME'][$x]]['value'];
  $atime = $wsResponse[$wsIndices['REASON'][$x]]['value'];
  $adate = $wsResponse[$wsIndices['DATE'][$x]]['value'];
  $areason = $wsResponse[$wsIndices['TIME'][$x]]['value'];
  $aremind = $wsResponse[$wsIndices['REMIND'][$x]]['value'];
  $status = $wsResponse[$wsIndices['STATUS'][$x]]['value'];
  $patient = $wsResponse[$wsIndices['PATLASTNAME'][$x]]['value'];
  $appointments[$x] = array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System - Appointment View</title>
        <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
    </head>

      <body>
      <?php
 	
      ?>
	<script type="text/javascript">
		function submitform()
		{
			document.forms["loginForm"].submit();
		}
	</script>
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/horizontal_logo.png" /></div>
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
                <div class="page_title">Profile</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
			<center><p><b>Welcome to the Electronic Medical Information System. You can view your appointments below.</b></p></center>
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
			<div class="dashed_line"></div>
			<table>
			<tr><td><label><strong></strong></label></td></tr>
			<tr>
			  <td><strong>Date:</strong></td>
			  <td><strong>Time:</strong></td>
			  <td><strong>Status:</strong></td>
			  <?php
				if($_SESSION['SESS_TYPE']==400){
					echo "<td><strong>Doctor:</strong></td>";
					echo "<td><strong>Patient:</strong></td>";
				} else if($_SESSION['SESS_TYPE']==300){
					echo "<td><strong>Patient:</strong></td>";
				} else if($_SESSION['SESS_TYPE']==1){
					echo "<td><strong>Doctor:</strong></td>";
				}
			  ?>
			  <td><strong>Doctor:</strong></td>
			  <td><strong>Reason:</strong></td>
			</tr>
			<?php
			  foreach($appointments as &$app) {
			  
			  //appoint array  array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient)
			    echo "<tr>";
			    $date = DateTime::createFromFormat('Y-m-d', $app[1]);
			   //date 
			    echo "<td>" . $date->format('D, M dS, Y') . "</td>";
			    $date = DateTime::createFromFormat('H:i:s', $app[4]);
			    //time
			    echo "<td>" . $date->format('H:ia') . "</td>";
			    //status
			    echo "<td>$app[6]</td>";
			    //doctor
			    if($_SESSION['SESS_TYPE'] == 400 || $_SESSION['SESS_TYPE'] == 1){
				echo "<td>$app[3]</td>";
			    }
			    //patient
			    if($_SESSION['SESS_TYPE'] == 400 || $_SESSION['SESS_TYPE'] == 300){
				echo "<td>$app[7]</td>";
			    }
			    echo "<td>$app[2]</td>";
			    echo "<td><a href='apptEditView.php?aid=$app[0]'>Edit</a></td>";
			    echo "</tr>";
			    }
			?>
			<tr>
				<td><a href='apptEditView.php'>Add Appt</a></td>
			</tr>
			</table>
<!-- END OF PAGE CONTENT -->
                </div>
            </div>
        </div>
        <div class="footer">
        	<p>Electronic Medical Information System. Copyright &copy; 2011 Team B. The University of Texas at San Antonio.</p>
        </div>
	</div>
</body>
</html>
