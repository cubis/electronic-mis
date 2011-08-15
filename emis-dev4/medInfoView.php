
 <?php
 session_start();
require_once('auth.php');
require_once('bootstrap.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Electronic Medical Information System</title>
    <link href="css/logged_in_styles.css" rel="stylesheet" type="text/css" />
</head>
    <body>
	<script type="text/javascript">
		function submitform()
		{
			document.forms["loginForm"].submit();
		}
	</script>
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
                <div class="page_title">Make Appointment</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->

            <?php
	    
	if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
		echo '<ul class="err">';
		foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>', $msg, '</li>';
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
	$patIsSet = isset($_GET['pat']);
	
	if(!$patIsSet){
		die("No patient set");
	}
	$req = $currentPath . "medViewREST.php?";
	$req .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
	$req .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
	$req .= "&pat=" . urlencode($_GET['pat']);
	//die($request);
	//format and send request
	$ch = curl_init($req);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
	curl_close($ch); //string from server has been returned <XML> closethe channel
	
	if( $RESToutput == ''){
		die("CONNECTION ERROR");
	}
	
	//print($RESToutput);
	
	//parse return string
	$parser = xml_parser_create();	
	xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
	
	$errNum = $wsResponse[$wsIndices['ERRNUM'][0]]['value'];
	if($errNum != 0){
		$ct = 0;
		$errMsg = array();
		while($ct < $errNum){
			$errMsg[$ct] = $wsResponse[$wsIndices['ERROR'][$ct]]['value'];
			$ct++;
		}
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		die("ERRORS");
	}
	
	$numRows = $wsResponse[$wsIndices['MEDCOUNT'][0]]['value'];
	$medications = array();
	for($x = 0 ; $x < $numRows; $x++) { // For each medication, add to $medications
		$medID = $wsResponse[$wsIndices['MEDID'][$x]]['value'];
		$medication = $wsResponse[$wsIndices['MEDICATION'][$x]]['value'];
		$dosage = $wsResponse[$wsIndices['DOSAGE'][$x]]['value'];
		$start = $wsResponse[$wsIndices['STARTDATE'][$x]]['value'];
		$end = $wsResponse[$wsIndices['ENDDATE'][$x]]['value'];
		$medications[$x] = array("medid"=>$medID, "medication"=>$medication, "dosage"=>$dosage, "start"=>$start, "end"=>$end );
	}
	
	$numRows = $wsResponse[$wsIndices['PRECCOUNT'][0]]['value'];
	$preconditions = array();
	for($x = 0 ; $x < $numRows; $x++) { // For each appointment, add to $appointments
		$precid = $wsResponse[$wsIndices['PRECID'][$x]]['value'];
		$desc = $wsResponse[$wsIndices['CONDITIONDESC'][$x]]['value'];
		$preconditions[$x] = array("precid"=>$precid, "desc"=>$desc);
	}
	?>
		<div class="dashed_line"></div>
		<table>
			<tr><td><label><strong></strong></label></td></tr>
			<tr>
			  <td width="150"><strong>Medication:</strong></td>
			  <td width="150"><strong>Dosage:</strong></td>
			  <td width="150"><strong>Start Date:</strong></td>
			  <td width="150"><strong>End Date:</strong></td>
			</tr>
		<?php
			  foreach($medications as &$med) {
			  
			  //appoint array  array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient)
			    echo "<tr>";
			    echo "<td>" . $med['medication']. "</td>";
			    echo "<td>" . $med['dosage']. "</td>";
			    echo "<td>" . $med['start']. "</td>";
			    echo "<td>" . $med['end']. "</td>";
			    if($_SESSION['SESS_TYPE'] > 1){
				echo "<td><a href='editMedicationView.php?med=" . $med['medid'] . "&pat=" . $_GET['pat'] . "'>Edit</a></td>";
			    }
			    echo "</tr>";
			    }
		?>
		<tr>
		<?php
		if($_SESSION['SESS_TYPE'] > 1){
			echo "<td><a href='editMedicationView.php?pat=" . $_GET['pat'] . "'>Add Medication</a></td>";
			}
		?>
		</tr>
		</table>
		<br />
		<br />
		
		<div class="dashed_line"></div>
		<table>
			<tr><td><label><strong></strong></label></td></tr>
			<tr>
			  <td width="150"><strong>Precondition ID:</strong></td>
			  <td width="150"><strong>Description:</strong></td>
			</tr>
		<?php
			  foreach($preconditions as &$prec) {
			  
			  //appoint array  array($aid, $adate, $atime, $adoctor, $areason, $aremind, $status, $patient)
			    echo "<tr>";
			    echo "<td>" . $prec['precid']. "</td>";
			    echo "<td>" . $prec['desc']. "</td>";
			    
			    if($_SESSION['SESS_TYPE']> 1){
				echo "<td><a href='editPrecView.php?prec=" . $prec['precid'] . "&pat=" . $_GET['pat'] . "'>Edit</a></td>";
			    } 
			    echo "</tr>";
			    }
		?>
		<tr>
		<?php
		if($_SESSION['SESS_TYPE'] > 1){
			echo "<td><a href='editPrecView.php?pat=" . $_GET['pat'] . "'>Add Precondition</a></td>";
			}
		?>
		</tr>
		</table>
		<br />
		<br />
	

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
