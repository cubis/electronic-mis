<htmL>
<style type="text/css">

td {
	background-color: #EEEEEE;
	font-family: arial, helvetica, sans-serif;
	font-size: 12;

}

table {
	background-color: #aaaaaa;
	border-left-style: solid;
	border: 1;
}
h1{
    font-family: arial, helvetica, sans-serif;
    font-size: 14;
}
</style>
<div style="background-color:#aaaaaa;text-align: center; left:50px; top:10px; right:50px; padding:0;border:0;"><h1>Welcome, Please log-in</h1></div>
<div id="pageBody" style="background-color:#efefef; ">

<?php
	$now = getdate(time());
	$time = mktime(0,0,0, $now['mon'], 1, $now['year']);
	$date = getdate($time);
	$dayTotal = cal_days_in_month(0, $date['mon'], $date['year']);

	
	print '<table><tr><td colspan="7"><strong>' . $date['month'] . '</strong></td></tr>';
	for ($i = 0; $i < 6; $i++) {
		print '<tr>';
		for ($j = 1; $j <= 7; $j++) {
			$dayNum = $j + $i*7 - $date['wday'];

			print '<td';
			if ($dayNum > 0 && $dayNum <= $dayTotal) {
				print ($dayNum == $now['mday']) ? ' style="background: #ccc;">' : '>';
				print $dayNum;
			}
			else {

				print '>';
			}
			print '</td>';
		}
		print '</tr>';
		if ($dayNum >= $dayTotal && $i != 6)
			break;
	}
	print '</table>';
?>

<br/>
<form action="process_login.php" method="post">
<table>
<tr>
<td>Username:</td>
<td> <input type="text" name="username" /></td>
</tr>
<tr>
<td>Password:</td><td><input type="password" name="password" /></td>
</tr>
<tr><td ></td><td>
<input type="submit" value="Login" />
</td>
</tr>
</table>
</form>
</div>





</html>
