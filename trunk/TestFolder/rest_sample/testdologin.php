<?php
$login = $_POST['login'];
$pw = $_POST['pw'];

$request = "http://127.0.0.1:5007/rest_sample/rest_sample/restauthentication.php?login=" . urlencode($login) . "&pw=" . urlencode($pw);
print("URL: $request <br />\n");

$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);

print("REST output = " . $output);
print("<hr />\n");
print("Click <a href=\"/testlogin.php\">here</a> to return.\n");
?>