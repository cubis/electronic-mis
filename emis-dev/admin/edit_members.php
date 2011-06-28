<?php
        require_once('../auth.php');
	require_once('../config.php');
	require_once('../bootstrap.php');
		
	$qry="SELECT * FROM Users";
	$result=mysql_query($qry);
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
            <div style="width: 800px; margin-left: auto; margin-right: auto;">
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
            <a class="black_button" style="margin-right: 360px;"href="../admin/edit-user-form.php"><span>Edit User</span></a>
        </p>
        </center>
        <table border="1">
            <tr>
            <td>First Name</td><td>Last Name</td><td>Sex</td><td>Username</td>
            <td>Email</td><td>Birthday</td><td>Phone Number</td><td>SSN</td><td>Edit</td>
            </tr>
<?php
$numrows = mysql_num_rows($result);

while ($row = mysql_fetch_assoc($result))
{
	echo "<tr>\n";
	echo "<td>",$row['FirstName'],"</td>\n";
	echo "<td>",$row['LastName'],"</td>\n";
	echo "<td>",$row['Sex'],"</td>\n";
	echo "<td>",$row['UserName'],"</td>\n";
	echo "<td>",$row['Email'],"</td>\n";
	echo "<td>",$row['Birthday'],"</td>\n";
	echo "<td>",$row['PhoneNumber'],"</td>\n";
	echo "<td>",$row['SSN'],"</td>\n";
	echo "<td><a href='../admin/edit-user-form.php'>Edit</a></td>\n";
	echo "</tr>\n";
}
?>
        </table>
     </body>
</html>
