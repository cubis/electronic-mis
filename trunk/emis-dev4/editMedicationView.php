
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
                <div class="page_title">Make Appointment</div>
                <div class="page_content">
                <!-- PAGE CONTENT STARTS HERE -->

        <form action="editMedicationExec.php" method="post">

            <?php
	    
	if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
		echo '<ul class="err">';
		foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>', $msg, '</li>';
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
	$medIsSet = isset($_GET['med']);
	
	
	if($medIsSet){
		$apptReq = $currentPath . "medViewREST.php?";
		$apptReq .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
		$apptReq .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
		$apptReq .= "&med=" . urlencode($_GET['med']);
		//die($request);
		//format and send request
		$ch = curl_init($apptReq);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$RESToutput = curl_exec($ch); //send URL Request to RESTServer... returns string
		curl_close($ch); //string from server has been returned <XML> closethe channel
		
		if( $RESToutput == ''){
			die("CONNECTION ERROR");
		}
		
		//die($RESToutput);
		
		//parse return string
		$parser = xml_parser_create();	
		xml_parse_into_struct($parser, $RESToutput, $wsResponseAppt, $wsIndicesAppt);
		
		
		$startdate = $wsResponseAppt[$wsIndicesAppt['STARTDATE'][0]]['value'];
		list($startYear, $startMonth, $startDay) = split('[/.-]', $startdate);
		$enddate = $wsResponseAppt[$wsIndicesAppt['ENDDATE'][0]]['value'];
		list($endYear, $endMonth, $endDay) = split('[/.-]', $enddate);
		//print($RESToutput);		
	}
	echo '<br />';
	echo 'Start Date: ';
	echo '<br />';
	$days = range(1, 31);
	$years = range(2011, 2061);
	$hours = range(0, 23);
	
	$months = array(1=>"January", 2=>"February", 3=>"March", 4=>"April" , 
				5=>"May", 6=>"June" , 7=>"July", 8=>"August", 9=>"September", 
				10=>"October", 11=>"November", 12=>"December");
	

	
	//Dropdown box for month
	echo '<select name = "startMonth">';
	foreach($months as $monthNum => $monthString){
		$str = '<option ';
		if($medIsSet && $monthNum == $startMonth){
			$str .= 'selected="selected" ';
		}
		$str .= 'value="' . $monthNum . '">' . $monthString . '</option>';
		echo $str;
	}
	echo '</select>';
	
	
	//Dropdown box for days
	echo '<select name="startDay">';
	foreach ($days as $value) {
		$str = "<option ";
		if($medIsSet && $startDay == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;
	}
	echo '</select>';
	//Dropdown box for years
	echo '<select name="startYear">';
	foreach ($years as $value) {
		$str = "<option ";
		if($medIsSet && $startYear == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;//"<option value=\"$value\">$value </option>\n";
	}
	echo '</select>';
	echo '<br />';
	echo 'End Date: ';
	echo '<br />';
	$days = range(1, 31);
	$years = range(2011, 2061);
	$hours = range(0, 23);
	
	$months = array(1=>"January", 2=>"February", 3=>"March", 4=>"April" , 
				5=>"May", 6=>"June" , 7=>"July", 8=>"August", 9=>"September", 
				10=>"October", 11=>"November", 12=>"December");
	
	
	//Dropdown box for month
	echo '<select name = "endMonth">';
	foreach($months as $monthNum => $monthString){
		$str = '<option ';
		if($medIsSet && $monthNum == $endMonth){
			$str .= 'selected="selected" ';
		}
		$str .= 'value="' . $monthNum . '">' . $monthString . '</option>';
		echo $str;
	}
	echo '</select>';
		
	
	//Dropdown box for days
	echo '<select name="endDay">';
	foreach ($days as $value) {
		$str = "<option ";
		if($medIsSet && $endDay == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;
	}
	echo '</select>';
	//Dropdown box for years
	echo '<select name="endYear">';
	foreach ($years as $value) {
		$str = "<option ";
		if($medIsSet && $endYear == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;//"<option value=\"$value\">$value </option>\n";
	}
	echo '</select>';
	echo '<br /><br />';
?>

            <br />

            <p>
		Medication:
		<br />
		<!-- Textbox -->
		<?php
			echo '<textarea name="medication" cols="20" rows="1">';
			if($medIsSet){
				echo $wsResponseAppt[$wsIndicesAppt['MEDICATION'][0]]['value'];
			} else {
				echo "100 Character max";		
			}
			echo '</textarea>';
?>

            <br />

            <p>
		Dosage:
		<br />
		<!-- Textbox -->
		<?php
			echo '<textarea name="dosage" cols="20" rows="1">';
			if($medIsSet){
				echo $wsResponseAppt[$wsIndicesAppt['DOSAGE'][0]]['value'];
			} else {
				echo "100 Character max";		
			}
			echo '</textarea>';
			
			echo '<input type="hidden" name="med" visible=false value="' . $_GET['med'] . '">';
			echo '<input type="hidden" name="pat" visible=false value="' . $_GET['pat'] . '">';
		
	?>
	
	<br />
	<input type="submit" value="Submit" />

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
