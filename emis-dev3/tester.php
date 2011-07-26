<?php
//Start session
session_start();
require_once("configREST.php");
require_once("bootstrapREST.php");


$prep = $db->prepare("SELECT * FROM `Users` WHERE UserName = :id ");


if (!$prep->execute(array(":id" => "DMoney"))) {
   $error = $prep->errorInfo();
   echo "Error: {$error[2]}"; // element 2 has the string text of the error
} else {
   while ($row = $prep->fetch(PDO::FETCH_ASSOC)) { // check the documentation for the other options here
        // do stuff, $row is an associative array, the keys are the field names
	print($row['FirstName']."\n");
   }
}


function doOther($thing){
	return $thing[1];
}


function doService(){
	$thing = array(1=>2);
	$retVal = doOther($thing);

	return $retVal;
}

//$output = doService();

$a = 10;
$b = NULL;
$aprep = $db->prepare("INSERT INTO Type (TypeNumber, TypeName) VALUES(:a, :b);");
$success = $aprep->execute( array(":a"=>$a, ":b"=>$b) );

print($output);

?>