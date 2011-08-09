
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

        <form action="apptEditExec.php" method="post">

            <?php
	    
	if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
		echo '<ul class="err">';
		foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>', $msg, '</li>';
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
	$aidIsSet = isset($_GET['aid']);
	
	
	if($aidIsSet){
		$apptReq = $currentPath . "apptViewREST.php?";
		$apptReq .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
		$apptReq .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
		$apptReq .= "&aid=" . urlencode($_GET['aid']);
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
		
		
		$date = $wsResponseAppt[$wsIndicesAppt['DATE'][0]]['value'];
		list($apptYear, $apptMonth, $apptDay) = split('[/.-]', $date);
	//	echo "Month: $apptMonth; Day: $apptDay; Year: $apptYear<br />\n";
		$time = $wsResponseAppt[$wsIndicesAppt['TIME'][0]]['value']; 
		list($apptHour, $apptMin, $apptSec) = split(':', $time);
		
	}
	    
	    
	    
	    
	if($_SESSION['SESS_TYPE'] == 1 || $_SESSION['SESS_TYPE'] == 400){   
		// Displays list of doctors
		echo 'Doctor: ';
		//fetches list from database and formats for dropdown box
		$request = $currentPath . "doctorListREST.php?";
		$request .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
		$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);

		//die($request);
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
		//die($RESToutput);
		//parse return string
		$parser = xml_parser_create();	
		xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);
		echo '<select name="doctor">';
		if($wsResponse[$wsIndices['DOCCOUNT'][0]]['value'] >= 1){
		
			echo '<option value="0">Select Doctor</option>';
			$ct = 0;
			while($ct <  $wsResponse[$wsIndices['DOCCOUNT'][0]]['value'])
			{
				if($aidIsSet && $wsResponse[$wsIndices['LASTNAME'][$ct]]['value'] == $wsResponseAppt[$wsIndicesAppt['DOCNAME'][0]]['value'] ){
					///echo '<option selected="selected" value='. $wsResponse[$wsIndices['DOCID'][$ct]]['value'] . '>' . $wsResponse[$wsIndices['LASTNAME'][$ct]]['value'] . '</option>';
					echo '<option selected="selected" value='. $wsResponse[$wsIndices['DOCID'][$ct]]['value'] . '>'. $wsResponse[$wsIndices['LASTNAME'][$ct]]['value'] . '</option>';
					
				} else {
					echo '<option value='. $wsResponse[$wsIndices['DOCID'][$ct]]['value'] . '>' . $wsResponse[$wsIndices['LASTNAME'][$ct]]['value'] . '</option>';
				}
				$ct++;
			}
		}
		else{
			echo '<option value="NULL">No Doctors Available!</option>';
		}
		echo '</select>';
	}	

	echo "<br />";
	echo "<br />";	
	
	
	if($_SESSION['SESS_TYPE'] == 200 || $_SESSION['SESS_TYPE'] == 300 || $_SESSION['SESS_TYPE'] == 400){   
	
		// Displays list of doctors
		echo 'Patient: ';
		//fetches list from database and formats for dropdown box
		$request = $currentPath . "patientListREST.php?";
		$request .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
		$request .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
		//die($request);
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
		//die($RESToutput);	
		//parse return string
		$parser = xml_parser_create();	
		xml_parse_into_struct($parser, $RESToutput, $wsResponse, $wsIndices);	
		$aidIsSet = isset($_GET['aid']);
		echo '<select name="patient">';
		if($wsResponse[$wsIndices['PATCOUNT'][0]]['value'] >= 1){
			
			echo '<option value="0">Select Patient</option>';
			$ct = 0;
			while($ct <  $wsResponse[$wsIndices['PATCOUNT'][0]]['value'])
			{
				$patid = $wsResponse[$wsIndices['PATID'][$ct]]['value'];
				$last = $wsResponse[$wsIndices['LASTNAME'][$ct]]['value'];
				$first = $wsResponse[$wsIndices['FIRSTNAME'][$ct]]['value'];
				if(  $aidIsSet && $last == $wsResponseAppt[$wsIndicesAppt['PATLASTNAME'][0]]['value'] && $first == $wsResponseAppt[$wsIndicesAppt['PATFIRSTNAME'][0]]['value']   ){
					echo '<option selected="selected" value='. $patid . '>' . $last . ', ' . $first . '</option>';				
				} else {
					echo '<option value='. $patid . '>' . $last . ', ' . $first . '</option>';
				}
				
				$ct++;			
			}
			
		
		}
		
		else{
			echo '<option value="NULL">No Patients Available!</option>';
		}
		echo '</select>';
	}
			
	
	
	
	
	
	
	
	
	
	
	
	echo 'Date: ';
	$days = range(1, 31);
	$years = range(2011, 2061);
	$hours = range(0, 23);
	
	$months = array(1=>"January", 2=>"February", 3=>"March", 4=>"April" , 
				5=>"May", 6=>"June" , 7=>"July", 8=>"August", 9=>"September", 
				10=>"October", 11=>"November", 12=>"December");
				
	
	//Dropdown box for month
	echo '<select name = "month">';
	foreach($months as $monthNum => $monthString){
		$str = '<option ';
		if($aidIsSet && $monthNum == $apptMonth){
			$str .= 'selected="selected" ';
		}
		$str .= 'value="' . $monthNum . '">' . $monthString . '</option>';
		echo $str;
	}
	echo '</select>';
	
	
	//Dropdown box for days
	echo '<select name="day">';
	foreach ($days as $value) {
		$str = "<option ";
		if($aidIsSet && $apptDay == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;
	}
	echo '</select>';
	//Dropdown box for years
	echo '<select name="year">';
	foreach ($years as $value) {
		$str = "<option ";
		if($aidIsSet && $apptYear == $value){
			$str .= "selected='selected' ";
		}
		$str .= "value = \"$value\">$value</option>\n";
		echo $str;//"<option value=\"$value\">$value </option>\n";
	}
	echo '</select>';
	echo '<br /><br />';
	echo 'Time:';
	//Dropdown box for hours
	echo '<textarea name="time" cols="5" rows="1">';
	if($aidIsSet){
		echo "$apptHour:$apptMin";
	} else {
		echo '12:00';
	}
	echo '</textarea>';
?>

            <br />

            <p>
                Reason for visit:
		<br />
		<!-- Textbox -->
		<textarea name="reason" cols="40" rows="5"><?php
			if($aidIsSet){
				echo $wsResponseAppt[$wsIndicesAppt['REASON'][0]]['value'];
			} else {
				echo "Please limit your response to 2000 characters.";		
			}
		?>
		</textarea>
            </p>

            <p>
                <!-- Whether user wants reminders before his appointment -->
                Would you like reminders sent prior to your appointment?
                <br />
	       <?php
	       //DO RADIO BUTTON GARBAGE FOR CANCEL AND REMINDERS
	       echo '<input type = "radio" name="reminder" value="true" ';
	       if($aidIsSet){
			if($wsResponseAppt[$wsIndicesAppt['REMINDER'][0]]['value'] == 1){
				echo 'checked="checked" /> Yes <br />';
				echo '<input type="radio" name="reminder" value="false"';
			} else {
				echo ' /> Yes <br />';
				echo '<input type="radio" name="reminder"  value="false" checked="checked"';
			}
	       } else {
			echo '<input type="radio" name="reminder" value="true" checked="checked" /> Yes<br />';
			echo '<input type="radio" name="reminder" value="false"';
	       }
	       echo '/> No';
	       
	       ?>
            </p>
	<?php
		if($aidIsSet){
			echo '<p>';
			echo 'Appointment Status';
			echo '<br />';
			echo '<input type = "radio" name="status" value="true" ';
			if( strtoupper($wsResponseAppt[$wsIndicesAppt['STATUS'][0]]['value']) == "SCHEDULED"  ){
				echo 'checked="checked" /> Scheduled <br />';
				echo '<input type="radio" name="status" value="false"';
			} else {
				echo ' /> Scheduled <br />';
				echo '<input type="radio" name="status"  value="false" checked="checked"';
			}
			echo '/> Cancelled';
			
			echo '</p>';
			echo '<input type="hidden" name="aid" visible=false value="' . $_GET['aid'] . '">';
		}
		
	?>
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
