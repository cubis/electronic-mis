# bootstrapREST.php #

## Description ##
This file should be included anytime you want to log an action.

## Functions ##

**Function**
```
if (!isset($_SESSION['SESS_MEMBER_ID']))   // start session if it hasn't been
    session_start();
```
**Description**<br>
Pretty Obvious<br>
<br>
<br>
<b>Function</b>
<pre><code>try {<br>
	$db = new PDO("mysql:dbname=".DB_DATABASE.";<br>
        host=".DB_HOST,DB_USER,DB_PASSWORD);<br>
} catch (PDOException $e) {<br>
        die("Database Connection Failed: " . $e-&gt;getMessage());<br>
}<br>
</code></pre>
<b>Description</b><br>
Tries connecting to the database and returns error if fails.<br>
<br>
<br>
<b>Function</b>
<pre><code>logToDB($actionDescription, $userID, $sentName)<br>
</code></pre>
<b>Description</b><br>
$actionDecription: Result you want to record.<br>
$userID: self-explanatory<br>
$sentName: ???<br>
<br>
<a href='http://code.google.com/p/electronic-mis/wiki/API'>API</a>