<?php

//this is under upload2

require_once('../configREST.php');     //sql connection information
require_once('../bootstrapREST.php');  //link information
/*
$uploaddir = './uploads/'; 
$file = $uploaddir . basename($_FILES['uploadfile']['name']); 
 
if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
  echo "success"; 
} else {
	echo "error";
}
*/
$fname = $_FILES['uploadfile']['name'];
$tmpname = $_FILES['uploadfile']['tmp_name'];
$fsize = $_FILES['uploadfile']['size'];
$ftype = $_FILES['uploadfile']['type'];
//$fname = addslashes($fname);
//$apNum = $_GET['ID'];
//test the type
$fp = fopen($tmpname,'rb');
$content = fread($fp,filesize($tmpname));
$content = addslashes($content);
//$fclose($fp);

if(!get_magic_quotes_gpc()){
    $fname = addslashes($fname);
}

//includes should be included at top

//$qry = "INSERT INTO Files(Name, Size, Type, Content, FK_ApptID)VALUES('$fname','$fsize','$ftype','$content','$apNum')";
//$tempqry = "INSERT INTO Files(Name, Size, Type, Content)VALUES('$fname','$fsize','$ftype','01010101010101011101010101010101010')";
$qryX = "INSERT INTO Admin(FK_member_id)VALUES('546')";
//mysql_query($qry);
$success = mysql_query($qryX);

//echo $tempqry. "<br />";
if(!success)
{
    echo mysql_error();
}
    echo $success . "<br />";

//echo "<br> File: $fname uploaded</br>";

?>