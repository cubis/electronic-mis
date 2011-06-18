<?php
        require_once('../auth.php');
		require_once('../config.php');
		require_once('../bootstrap.php');
		
		$qry="SELECT * FROM Users WHERE Type='0'";
		$result=mysql_query($qry);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Administrator - Approve Member Requests</title>
<link href="../loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Admin</h1>
<hr />
<br />
<a href="../member-profile.php">Return to Profile</a>

<table border="1">
<tr>
<td>First Name</td><td>Last Name</td><td>Sex</td><td>Username</td>
<td>Email</td><td>Birthday</td><td>Phone Number</td><td>SSN</td><td>Approval</td>
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
	echo "<td><a href='#'>Edit</a></td>\n";
	echo "</tr>\n";
}
?>


</table>

</body>
</html>