<?php
require_once('auth.php');
require_once('bootstrap.php');

$userName = $_SESSION['SESS_USERNAME'];
$aid = $_GET['aid'];

global $currentPath;
$request = $currentPath . "visitListREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
$request .= "&pat=" . urlencode($aid);

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

//die($RESToutput);

if( $RESToutput == ''){
  die("CONNECTION ERROR");
}


//parse return string
$parser = xml_parser_create();	
xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
xml_parser_free($parser);

$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
if ($errNum != 0) {
	$ct = 0;
	while($ct < $errNum){
		$err_msg_arr[] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
		$ct++;
	}
	$_SESSION['ERRMSG_ARR'] = $err_msg_arr;
}

$visitID = $wsResponse[$wsIndices['VISITID'][0]]['value'];
$appID = $wsResponse[$wsIndices['APPID'][0]]['value'];
$bp = $wsResponse[$wsIndices['BP'][0]]['value'];
$weight = $wsResponse[$wsIndices['WEIGHT'][0]]['value'];
$reason = $wsResponse[$wsIndices['REASON'][0]]['value'];
$diagnosis = $wsResponse[$wsIndices['DIAGNOSIS'][0]]['value'];
$symptoms = $wsResponse[$wsIndices['SYMPTOMS'][0]]['value'];
$medicine = $wsResponse[$wsIndices['MEDICINE'][0]]['value'];
$dosage = $wsResponse[$wsIndices['DOSAGE'][0]]['value'];
$startDate = $wsResponse[$wsIndices['STARTDATE'][0]]['value'];
$endDate = $wsResponse[$wsIndices['ENDDATE'][0]]['value'];
$bill = $wsResponse[$wsIndices['BILL'][0]]['value'];

$RESToutput = null;

$request = $currentPath . "apptViewREST.php?";
$request .= "u=" . urlencode($userName);
$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
$request .= "&aid=" . urlencode($aid);

//format and send request
$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

//die($RESToutput);

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

$apptStatus = $wsResponse[$wsIndices['STATUS'][0]]['value'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Electronic Medical Information System (EMIS) - Visit</title>
        <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
    </head>

<body>
    <div class="container">
        <div class="header">
			<div class="logo"><a href="memberProfileView.php"><img src="img/logo.png" /></a></div>
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
                <div class="page_title">Visitation Record</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->
				<script type="text/javascript">
					function submitform()
					{
						document.forms["visit"].submit();
					}
				</script>
				
<form id="visitForm" name="visitForm" enctype="multipart/form-data" method="post" action="visitExec.php">';
<input type="hidden" name="aid" value="<?php echo $aid; ?>" />

<!--<form id="visitForm" name="visitForm" method="post" action="visitExec.php?ID=<?php //echo $_GET[$ID] ?>">-->
<center><p><b>Fill in the visit information below.</b></p></center>
<?php
//echo "<p>ID=".$apptid."</p>";
?>
<div class="dashed_line"></div>
<table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
	<tr>
		<th>Blood Pressure:</th>
		<td><input name="BP" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Weight</th>
		<td><input name="Weight" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Symptoms</th>
		<td><input name="Symptoms" type="text" class="textfield" /></td>
	</tr>
	
	<tr>
		<th>Reason</th>
		<td><input name="Reason" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Diagnosis</th>
		<td><input name="Diagnosis" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Medicine (If Needed)</th>
		<td><input name="Medicine" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Dosage</th>
		<td><input name="Dosage" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Start Date (MMDDYYYY):</th>
		<td><input name="StartDate" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>End Date (MMDDYYYY):</th>
		<td><input name="EndDate" type="text" class="textfield" /></td>
	</tr>

	<tr>
		<th>Total Bill:</th>
		<td><input name="Bill" type="text" class="textfield" /></td>
	</tr>
	<tr>
		<th>File To Upload</th>
		<th><input name="uploadfile" type="file" id="uploadfile" /></th>
	

	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" /> </td>
<!--<td><a class="black_button" href="javascript: submitform()"><span>Submit</span></a></td>-->
	</tr>
</table>
</form>
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
