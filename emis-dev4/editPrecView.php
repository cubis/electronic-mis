
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

        <form action="editPrecExec.php" method="post">

            <?php
	    
	if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
		echo '<ul class="err">';
		foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>', $msg, '</li>';
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
	$precIsSet = isset($_GET['prec']);
	
	
	if($precIsSet){
		$apptReq = $currentPath . "medViewREST.php?";
		$apptReq .= "u=" . urlencode($_SESSION['SESS_USERNAME']);
		$apptReq .= "&key=" . urlencode($_SESSION['SESS_AUTH_KEY']);
		$apptReq .= "&prec=" . urlencode($_GET['prec']);
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
		
		
	}
	    
	    
	    
?>

            <br />

            <p>
		Precondition Description:
		<br />
		<!-- Textbox -->
		<?php
			echo '<textarea name="desc" cols="40" rows="5">';
			if($precIsSet){
				echo $wsResponseAppt[$wsIndicesAppt['CONDITIONDESC'][0]]['value'];
			} else {
				echo "Please limit your response to 2000 characters.";		
			}
			echo '</textarea>';
			
			
			echo '<input type="hidden" name="prec" visible=false value="' . $_GET['prec'] . '">';
			echo '<input type="hidden" name="pat" visible=false value="' . $_GET['pat'] . '">';
		
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
