# authenticateREST.php #

## Files Included ##
```
require_once('configREST.php');     //sql connection information
require_once('bootstrapREST.php');  //link information
```

## Input ##
```
https://[URL]/Authenticate.php?u=[username]&password=[pass]
```

## Output ##
**XML**
  * result  `[0 or 1]`  if user and pass is correct<br>
<ul><li>key     <code>[hashed key]</code> validate auth was performed on this WS<br>
<br>
<b>Example Output:</b><br>
<pre><code>&lt;?xml version="1.0"?&gt;<br>
      &lt;result&gt;    1       &lt;/result&gt;<br>
      &lt;key&gt;       fb504a91465213203ae7c3866bbf3cf4&lt;/key&gt;<br>
      &lt;userID&gt;    12345   &lt;/userID&gt;<br>
      &lt;AccessType&gt;400     &lt;/type&gt;<br>
</code></pre>
<br>
<h2>Functions</h2>
<b>Function:</b>
<pre><code>function doService()<br>
</code></pre>
<b>Description:</b><br>
Authenticates user, returns error if unsuccessful, and makes note of the result.<br>
<br>
<br>
<b>Function:</b>
<pre><code>function outputXML($errNum, $errMsgArr, $memberInfo)<br>
</code></pre>
<b>Description:</b><br>
Outputs authentication result in XML. Calls<br>
<pre><code>logToDB($actionDescription, $userID, $sentName) (located in bootstrapREST.php)<br>
</code></pre>
to log the result.<br>
<br>
<br></li></ul>

<a href='http://code.google.com/p/electronic-mis/wiki/API'>API</a>