<?php
require_once('bootstrap.php');

$userName = $_SESSION['SESS_USERNAME'];

$request = "http://localhost/emis/emis-dev3/visitREST.php?";
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

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
if ($errNum != 0) {
	die("FUCK!" . $wsResponse[$wsIndices['ERROR'][0]]['value']);
}

$aid = $wsResponse[$wsIndices['appointment'][0]]['value'];
$adate = $wsResponse[$wsIndices['date'][0]]['value'];
$atime = $wsResponse[$wsIndices['time'][0]]['value'];
$adoctor = $wsResponse[$wsIndices['doctor'][0]]['value'];
$areason = $wsResponse[$wsIndices['reason'][0]]['value'];
$aremind = $wsResponse[$wsIndices['remind'][0]]['value'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System - Appointment View</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
      <?php
	
      ?>
      <center><h1 style="color: white; margin-top: 50px;">Appointment View</h1></center>
        <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System" />
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["appointmentView"].submit();
                        }
                    </script>
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
			<table  align="center" >
			  <tr><td><label><strong></strong></label></td></tr>
			  <tr>
			    <td><strong>Date:</strong></td>
			    <td>[DATE]</td>
			  </tr>
			  <tr>
			    <td><strong>Time:</strong></td>
			    <td>[TIME]</td>
			  </tr>
			  <tr>
			    <td><strong>Doctor:</strong></td>
			    <td>[DOCTOR]</td>
			  </tr>
			  <tr>
			    <td><strong>Reason:</strong></td>
			    <td>[REASON]</td>
			  </tr>
			</table>
                </div>
            </div>
        </div>
    </body>
</html>
