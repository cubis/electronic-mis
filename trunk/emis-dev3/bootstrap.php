<?php


    session_start();


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