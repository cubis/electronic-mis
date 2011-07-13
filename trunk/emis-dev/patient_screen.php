<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Viewing Information</title>
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">Viewing <?echo "{$_SESSION['SESS_USERNAME']}";?> Information</h1></center>
            <div style="width: 400px; margin-left: auto; margin-right: auto;">
            <div class="login_box">
                <center>
                    <img src="img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
            <?php
	    
		$user = $_SESSION['SESS_USERNAME'];
		$key = $_SESSION['SESS_AUTH_KEY'];
		$request = "http://localhost/emis/emis-dev/patient_screenREST.php?u=".urlencode($user)."&key=".urlencode($key);
	
	//print("--------------------------------".($_GET['targetType'] == ''));
	//print("URL: $request <br />\n");

	//format and send request
	$ch = curl_init($request);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);    
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch); //send URL Request to RESTServer... returns string
	curl_close($ch); //string from server has been returned <XML> closethe channel
	
	if( $output == ''){
		die("CONNECTION ERROR ");
	}
	
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $output, $wsResponse, $wsIndices);
		//print("OUTPUT = ".$output."\n");
		//print_r($wsResponse);
            
		
			
		    $InsuranceID = $wsResponse[$wsIndices['INSURANCEID'][0]]['value'];
		    $InsuranceGroup = $wsResponse[$wsIndices['INSURANCEGROUP'][0]]['value'];
		    $CoPay = $wsResponse[$wsIndices['COPAY'][0]]['value'];
		    $Start = $wsResponse[$wsIndices['START'][0]]['value'];
		    $End = $wsResponse[$wsIndices['END'][0]]['value'];
		    $PrimaryDoctor = $wsResponse[$wsIndices['DOCTOR'][0]]['value'];
		    
		    
		    $numPrecs =  $wsResponse[$wsIndices['NUMPRECS'][0]]['value'];
		    $ct = 0;
		    $Preconditions = "";
		    while($ct < $numPrecs){
			$Preconditions .= "-".$wsResponse[$wsIndices['PRECONDITION'][$ct]]['value']."\n<br />";		    
			$ct+=1;
		    }
		    
		    
		    $numMeds = $wsResponse[$wsIndices['NUMMEDS'][0]]['value'];
		    $ct = 0;
		    $Medication = "";
		    while($ct < $numMeds){
			$Medication .= "-".$wsResponse[$wsIndices['MEDICATION'][0]]['value']."\n<br />";		    
			$ct+=1;
		    }
           ?>
            <center><table>
                <tr>
                    <td><h3><?php echo "$user Personal Infomation";?></h3></td>
                </tr>
                
                <tr>
                    <td>Insurance ID:</td>
                    <td><b><?php echo "$InsuranceID";?><b></td>
                </tr>
                <tr>
                    <td>Insurance Group:</td>
                    <td><b><?php echo "$InsuranceGroup";?><b></td>
                </tr>
                <tr>
                    <td>Co-Pay:</td>
                    <td><b><?php echo "$CoPay";?><b></td>
                </tr>
                <tr>
                    <td>Coverage Start:</td>
                    <td><b><?php echo "$Start";?><b></td>
                </tr>
                <tr>
                    <td>Coverage Ends:</td>
                    <td><b><?php echo "$End";?><b></td>
                </tr>
                    <td><h3><div class="dashed_line"></div><? echo "$user Medical Information";?></h3></td>
                </tr>
                <tr>    
                    <td>Preconditions:</td>
                    <td><b><?php echo "$Preconditions";?><b></td>
                </tr>
                <tr>
                    <td>Medication:</td>
                    <td><b><?php echo "$Medication";?><b></td>
                </tr>
                <tr>
                    <td>Primary Doctor:</td>
                    <td><b><?php echo "$PrimaryDoctor";?><b></td>
                </tr>
                <tr>
                    <td><div class="dashed_line"></div>
                </tr>
            </table></center>
          <a class="black_button" style="margin-right: 170px;" href="member-profile.php"><span>Back</span></a>

    </body>
</html>
