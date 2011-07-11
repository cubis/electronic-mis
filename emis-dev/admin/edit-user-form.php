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
                $connection = @mysql_connect("devdb.fulgentcorp.com","495311team2user","680c12D5!gP592xViF") or die(mysql_error());
                $database = @mysql_select_db("cs49532011team2", $connection) or die(mysql_error());
                $table_name = "Users";
                $sql = "SELECT * FROM $table_name WHERE PK_member_id = '$_GET[ID]'";
                $result = @mysql_query($sql,$connection) or die(mysql_error());
                while ($row = mysql_fetch_array($result))
                {
                    $ID = $row['PK_member_id'];
                    $user = $row['UserName'];
 		    $f_name = $row['FirstName'];
                    $l_name = $row['LastName'];
                    $sex = $row['Sex'];
                    $email = $row['Email'];
                    $birthday = $row['Birthday'];
                    $phone = $row['PhoneNumber'];
                    $ssn = $row['SSN'];
                    $type = $row['Type'];
                    $need = $row['NeedApproval'];
//                  $address = $row['Address'];
//                  $policy = $row['Policy'];


                }
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
                    <td><div class="dashed_line"></div>
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

