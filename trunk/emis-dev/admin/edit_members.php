<?php
        require_once('../auth.php');
	require_once('../config.php');
	require_once('../bootstrap.php');
        session_start();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Administrator Profile</title>
        <link href="../css/styles.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <center><h1 style="color: white; margin-top: 50px;">Admin Editing</h1></center>
            <div style="width: 630px; margin-left: auto; margin-right: auto;">
                <center>
                    <img src="../img/logo.png" alt="Electronic Medical Information System">
                </center>
                <div>
                    <script type="text/javascript">
                        function submitform()
                        {
                        document.forms["loginForm"].submit();
                        }
                    </script>
        <center><p><h2>Admin Edit User</h2></p>
        <input name="login" type="text" class="textfield" id="login" value="Username" /><BR>
        <p><b>- OR -</b></p>
        <p>
            <input name="login" type="text" class="textfield" id="login" value="First Name" /> <BR>
            <input name="login" type="text" class="textfield" id="login" size="1" maxlength="1" value="MI" /><BR>
            <input name="login" type="text" class="textfield" id="login" value="Last Name" /><BR>
            <a class="black_button" style="margin-right: 275px;"href='../admin/edit-user-form.php'+ hidden='?ID=<?$ID?>'><span>Edit User</span></a>
        </p> 
        </center>
        <br>
        <table border="1">
            <tr>
              <td>First Name</td>
              <td>Last Name</td>
              <td>Sex</td>
              <td>Username</td>
              <td>Email</td>
              <td>Birthday</td>
              <td>Phone Number</td>
              <td>SSN</td>
              <td>Type</td>
              <td>Edit</td>
            </tr>
<?php


$user = $_SESSION['SESS_USERNAME'];
$key = $_SESSION['SESS_AUTH_KEY'];
$request = "http://localhost/emis/emis-dev/getMemberInfoREST.php?u=".urlencode($user)."&key=".urlencode($key);
	
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
	//print("name = ".$wsResponse[$wsIndices['FIRSTNAME'][0]]['value']."\n");

	
	$numrows = $wsResponse[$wsIndices['COUNT'][0]]['value'];
	$currRow = 0;

while ($currRow < $numrows)
{
	echo "<tr>\n";
        $ID = $wsResponse[$wsIndices['ID'][$currRow]]['value'];
	echo "<td>",$wsResponse[$wsIndices['FIRSTNAME'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['LASTNAME'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['SEX'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['USERNAME'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['EMAIL'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['BIRTHDAY'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['PHONENUMBER'][$currRow]]['value'],"</td>\n";
	echo "<td>",$wsResponse[$wsIndices['SSN'][$currRow]]['value'],"</td>\n";
        echo "<td>",$wsResponse[$wsIndices['TYPE'][$currRow]]['value'],"</td>\n";
        echo "<td><a href='../admin/edit-user-form.php?ID=$ID'>Edit</a></td>\n";
	echo "</tr>\n";
	$currRow += 1;
}

?>
        </table>
        <a class="black_button" style="margin-right: 295px;"href='../member-profile.php'><span>Back</span></a>
     </body>
</html>
