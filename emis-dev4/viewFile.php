<?php

require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');

$id = $_GET['aid'];
$prep = $db->prepare('SELECT Content FROM Files WHERE FK_ApptID = ?');
if ($prep->execute(array($id))) {
 $content = $prep->fetch();
 $content = $content[0];
 header("Content-type: image/jpeg"); // or whatever
 print $content;
} else {
 echo $prep->errorInfo();
}

?>
