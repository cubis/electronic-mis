

<?php
require_once('auth.php');
require_once('bootstrap.php');
restrictAccess('0001');
$userName = $_SESSION['SESS_USERNAME'];

global $currentPath;
$request = $currentPath . "viewLogREST.php?";
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
$numRows = $wsResponse[$wsIndices['LOGCOUNT'][0]]['value'];
for($x = 0 ; $x < $numRows; $x++) { // For each appointment, add to $appointments
  $logid = $wsResponse[$wsIndices['LOGID'][$x]]['value'];
  $sourceip = $wsResponse[$wsIndices['SOURCEIP'][$x]]['value'];
  $type = $wsResponse[$wsIndices['TYPE'][$x]]['value'];
  $purgeDate = $wsResponse[$wsIndices['PURGEDATE'][$x]]['value'];
  $fkmemberID = $wsResponse[$wsIndices['FK_MEMBER_ID'][$x]]['value'];
  $timeStamp = $wsResponse[$wsIndices['TIMESTAMP'][$x]]['value'];
  // $username = $wsResponse[$wsIndices['USERNAME'][$x]]['value'];
  // Username is always null. Don't really need it.
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
    <div class="container">
        <div class="header">
            <div class="logo"><img src="img/logo.png" /></div>
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
                <div class="page_title">Patients</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
			<center><p><b>Welcome to the Electronic Medical Information System. You can view and edit patient medical information below.</b></p></center>
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
            <tr>
            <td>Log ID</td>
            <td>Source IP</td>
            <td>Purge Date</td>
            <td>Member ID</td>
			<td>Time Stamp</td>
            </tr>
            <tr>
            <?php /* php goes here */ ?>
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
