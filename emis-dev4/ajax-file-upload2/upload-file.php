<?php

//this is under upload2

require_once('../configREST.php');     //sql connection information
require_once('../bootstrapREST.php');  //link information

$uploaddir = './uploads/'; 
$file = $uploaddir . basename($_FILES['uploadfile']['name']); 
 
if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
  echo "success"; 
} else {
	echo "error";
}

$fname = $_FILES['uploadfile']['name'];
$tmpname = $_FILES['uploadfile']['tmp_name'];
$fsize = $_FILES['uploadfile']['size'];
$ftype = $_FILES['uploadfile']['type'];
//$apNum = $_GET['ID'];
//test the type
$fp = fopen($fname,'rb');
//$content = fread($fp,filesize($fname));
//$content = addslashes($content);

$name = $fname;
$size = $fsize;
$type = $ftype;
//$content = "011010";
//$appid = 106;

$content = file_get_contents($fname);

$qry = $db->prepare('INSERT INTO Files (Name, Size, Type, Content, FK_ApptID) VALUES (?, ?, ?, ?, ?)');
$qryStatus = $qry->execute(array($name, $size, $type, $content, $appid));

?>

<script language=javascript>
var jsvar;

jsvar = <?php echo $content;?>
</script> 

<?php 
if(!$qryStatus) echo "error";
else echo "success";

//$fclose($fp);

/*if(!get_magic_quotes_gpc()){
    $fname = addslashes($fname);
}*/

//includes should be included at top

//$qry = "INSERT INTO Files(Name, Size, Type, Content, FK_ApptID)VALUES('$fname','$fsize','$ftype','$content','$apNum')";
//$tempqry = "INSERT INTO Files(Name, Size, Type, Content)VALUES('$fname','$fsize','$ftype','$content')";
//mysql_query($qry);
//$success = mysql_query($tempqry);

//echo $tempqry. "<br />";
    //echo $success . "<br />";

//echo "<br> File: $fname uploaded</br>";

?>