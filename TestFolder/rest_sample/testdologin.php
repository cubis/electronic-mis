<?php
$login = $_POST['login'];
$pw = $_POST['pw'];

$request = "http://127.0.0.1/~cookie/TestFolder/rest_sample/restauthentication.php?login=" . urlencode($login) . "&pw=" . urlencode($pw);
print("URL: $request <br />\n");

$ch = curl_init($request);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_HEADER, false); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch); //send URL Request to RESTServer... returns string
curl_close($ch); //string from server has been returned <XML> closethe channel

print("REST output = " . $output); //print out string
print("<hr />\n");
print("Click <a href=\"http://127.0.0.1/~cookie/TestFolder/rest_sample/testlogin.php\">here</a> to return.\n");
?>

/*
Starting implimentations of REST see diff files for changes. 
TODO:  test that XML is correctly created  <result> 1 </result or   <result> 0</result>    
the XML also needs to be parsed form $output in the login-exe.php file, the parsed XML should be stored in $result  where it is tested like normal.  if result == '1' then a session will be created. else login failed

made a chance to password formt he post... since not https post is plain text :(  minimal security i md5 hash the password before sending it to the authenticator. this will take some load off the function since it will be comparing to an already hashed password on the sql database. */
