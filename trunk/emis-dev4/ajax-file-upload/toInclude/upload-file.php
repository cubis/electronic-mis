<?php
$uploaddir = './uploads/'; 
$file = $uploaddir . basename($_FILES['uploadfile']['name']); 
$fname = $_FILES['uploadfile']['name'];
$tmpname = $_FILES['uploadfile']['tmp_name'];
$fsize = $_FILES['uploadfile']['size'];
$ftype = $_FILES['uploadfile']['type'];
$apNum = $_GET['ID'];
//test the type
$fp = fopen($tmpname,'r');
$content = fread($fp,filesize($tmpname));
$content = addslashes($content);
$fclose($fp);

if(!get_magic_quotes_gpc()){
    $fname = addcslashes($fname);
}

//includes should be included at top

$qry = "INSERT INTO Files(Name, Size, Type, Content, FK_ApptID)VALUES('$fname','$fsize','$ftype','$content','$apNum')";
mysql_query($qry);
echo "<br> File: $fname uploaded</br>";

/*
if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
  echo "success"; 
} else {
	echo "error";
}
 * 
 */
?>