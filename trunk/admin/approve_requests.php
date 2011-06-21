<?php
        require_once('../auth.php');
		require_once('../config.php');
		require_once('../bootstrap.php');
		
		$qry="SELECT * FROM Users WHERE NeedApproval='1'";
		$result=mysql_query($qry);
?>
<?php
if(isset($_POST["submitted"])):

foreach ($_POST as $username => $value)
{
	if(strcmp($value,"approve") == 0)
	{
		$qry = "UPDATE Users SET NeedApproval='0' WHERE UserName='$username'";
		$result=mysql_query($qry);
		if ($result)
			echo "Approved user '$username' <br />\n";
		else
			echo "Error: ",mysql_error($link),"<br />\n";
	}
	elseif(strcmp($value,"deny") == 0)
	{
		echo " Denying user '$key' <br />\n";
	}
	else
	{
		echo "<br />";
	}
}

?>


<?php else: ?>
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
<br />
<br />
<table border="1">
<tr>
<td>First Name</td><td>Last Name</td><td>Sex</td><td>Username</td>
<td>Email</td><td>Birthday</td><td>Phone Number</td><td>SSN</td><td>Type</td><td>Approval</td>
</tr>

<form id="approvalForm" name="approvalForm" method="post" action="approve_requests.php">
<input type="hidden" name="submitted" value="true" />
<?php
$numrows = mysql_num_rows($result);

while ($row = mysql_fetch_assoc($result))
{
	$username = $row['UserName'];
	echo "<tr>\n";
	echo "<td>",$row['FirstName'],"</td>\n";
	echo "<td>",$row['LastName'],"</td>\n";
	echo "<td>",$row['Sex'],"</td>\n";
	echo "<td>",$row['UserName'],"</td>\n";
	echo "<td>",$row['Email'],"</td>\n";
	echo "<td>",$row['Birthday'],"</td>\n";
	echo "<td>",$row['PhoneNumber'],"</td>\n";
	echo "<td>",$row['SSN'],"</td>\n";
	echo "<td>",$row['Type'],"</td>\n";
	echo "<td><input type='radio' name='$username' value='approve' /> Approve <input type='radio' name='$username' value='deny' /> Deny</td>\n";
	echo "</tr>\n";
}
?>


</table>
<br />
<br />
<input type="submit" />


</body>
</html>
<?php endif;
?>