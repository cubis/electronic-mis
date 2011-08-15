<?php


    session_start();

//edit this string to your webroot... ignore this file from updates and commits
$currentPath = "http://67.10.181.224/emis/emis-dev4/";

//Function to sanitize values received from the form. Prevents SQL injection
//NOT TERRIBLY NECESSARY
function clean($str) {
    $str = @trim($str);
    if (get_magic_quotes_gpc ()) {
        $str = stripslashes($str);
    }
    return mysql_real_escape_string($str);
}
?>
