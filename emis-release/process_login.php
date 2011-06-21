
$host = 'localhost'; 
$user = 'root';
$pass = ''; 
$database = 'test'; 
$table = 'Users';

mysql_connect($host, $user, $pass);
mysql_select_db($database);

$username = mysql_real_escape_string($_POST['username']);
$password = hash('sha512', $_POST['password']);

$result = mysql_query("SELECT * FROM $table WHERE username = '$username' AND password = '$password'
");

if(mysql_num_rows($result))
{
// Login
session_start();
$_SESSION['username'] = htmlspecialchars($username); // htmlspecialchars() sanitises XSS
}
else
{
// Invalid username/password
echo '<p><strong>Error:</strong> Invalid username or password.</p>';
}

exit;
