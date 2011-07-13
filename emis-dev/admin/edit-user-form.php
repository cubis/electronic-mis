<?php
//headers for session
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Admin Editing</title>
        <link type="text/css" rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
        <form id="loginForm" name="login_Form" method="post" action="edit-user-update.php">
        <center><h1 style="color: white; margin-top: 50px;">Admin User's Editing Form</h1></center>
            <div style="width: 500px; margin-left: auto; margin-right: auto;">
                <center>
                    <img src="../img/logo.png" alt="Electronic Medical Information System">
                </center>
                <br>
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
	$request = "http://localhost/emis/emis-dev/admin/edit_membersREST.php?u=".urlencode($user)."&key=".urlencode($key)."&targetType=PK_member_id&target=".urlencode($_GET['ID']);

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
	//print_r($wsResponse."\n");

                    $ID = $wsResponse[$wsIndices['ID'][0]]['value'];
                    $user = $wsResponse[$wsIndices['USERNAME'][0]]['value'];
 		    $f_name = $wsResponse[$wsIndices['FIRSTNAME'][0]]['value'];
                    $l_name = $wsResponse[$wsIndices['LASTNAME'][0]]['value'];
                    $sex = $wsResponse[$wsIndices['SEX'][0]]['value'];
                    $email = $wsResponse[$wsIndices['EMAIL'][0]]['value'];
                    $birthday = $wsResponse[$wsIndices['BIRTHDAY'][0]]['value'];
                    $phone = $wsResponse[$wsIndices['PHONENUMBER'][0]]['value'];
                    $ssn = $wsResponse[$wsIndices['SSN'][0]]['value'];
                    $type = $wsResponse[$wsIndices['TYPE'][0]]['value'];
                    $need = $wsResponse[$wsIndices['NEEDAPPROVAL'][0]]['value'];
//                  $address = $row['Address'];
//                  $policy = $row['Policy'];
					$locked = $row['Locked'];

           ?>
	<center><table>
		<tr>
			<td><h3><?php echo "$user Personal Infomation";?></h3></td>
		</tr>
		<tr>
			<td>First Name:</td>
			<td><input type="text" name="Firstname" value= <?php echo "$f_name";?> /></td>
			<td><INPUT TYPE=hidden NAME="ID" VALUE= <?php echo "$ID";?>></td>
			<td><INPUT TYPE=hidden NAME="Need" VALUE= <?php echo "$need";?>></td>
                </tr>
                <tr>
                    <td>Last Name:</td>
		<td><input type="text" name="Lastname" value = <?php echo "$l_name";?> /></td>
                </tr>
                <tr>
                    <td>Sex:</td>
                    <td><select name="Sex">
                            <option value = "M">Male</option>
                            <option value = "F">Female</option>
                    </select></td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td><input type="text" name="Address" value = <?php echo "$address";?> /></td>
                </tr>
                <tr>
                    <td>Birthday("YYYY-MM-DD"):</td>
                    <td><input type="text" name="Birthday" value = <?php echo "$birthday";?>  /></td>
                </tr>
                <tr>
                    <td>SSN:</td>
                    <td><input type="text" name="SSN" value = <?php echo "$ssn";?>  /></td>
                </tr>
                <tr>
                    <td><h3><div class="dashed_line"></div><?php echo "$user Contact Information";?></h3></tr><tr>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="Email" value = <?php echo "$email";?>  /><br />
                </tr>
                <tr>
                    <td>Phone Number(###-###-####):</td>
                    <td><input type="text" name="Phonenumber" value = <?php echo "$phone";?>  /></td>
                </tr>
                <tr>
                    <td><h3><div class="dashed_line"></div><?php echo "$user Insurance Information";?></h3></td>
                </tr>
                <tr>
                    <td>Insurance ID:</td>
                    <td><input type="text" name="Insurance" value=<?php echo "$policy";?>/></td>
                </tr>
                <tr>
                    <td>Insurance Group:</td>
                    <td><input type="text" name="Group" value=<?php echo "$policy";?>/></td>
                </tr>
                <tr>
                    <td>Co-Pay:</td>
                    <td><input type="text" name="Co" value=<?php echo "$policy";?>/></td>
                </tr>
                <tr>
                    <td>Coverage Start:</td>
                    <td><input type="text" name="CoStart" value=<?php echo "$policy";?>/></td>
                </tr>
                <tr>
                    <td>Coverage Ends:</td>
                    <td><input type="text" name="CoEnd" value=<?php echo "$policy";?>/></td>
                </tr>
                    <td><h3><div class="dashed_line"></div><?php echo "$user Access Level";?></h3></td>
                </tr>
                <tr>
                     <td>Type:</td>
                     <td><input type="text" name="Type" value = <?php echo "$type";?>  /></td>
                </tr>
                <tr>
                    <td><h3><div class="dashed_line"></div><?php echo "$user Account status";?></h3></td>
                </tr>
				<tr>
                     <td>Unlocked:</td>
                     <td>
					 <input type="radio" name="Status" value="unlock" 
						<?php if(!$locked) echo "checked"; ?>  />
					 </td>
				</tr>
				<tr>
					 <td>Locked:</td>
                     <td>
					 <input type="radio" name="Status" value="lock"
						<?php if($locked) echo "checked"; ?>  />
					 </td>
                </tr>
	      <tr>
		<td><?php echo "<p style=\"color: red;\">$_GET[msg]</p>"; ?>  </td>
	      </tr>
            </table></center>
            <a class="black_button" style="margin-right: 70px;" href="javascript: submitform()"><span>Save Changes</span></a>
            <a class="black_button" style="margin-right: 50px;" href="../admin/edit-user-form.php?ID=<?php echo "$ID";?>"><span>Reset Changes</span></a>
            <a class="black_button" style="margin-right: 45px;" href="../admin/edit_members.php"><span>Back</span></a>
	    
        </form>
    </body>
</html>

