<?php
//Database Utility
//Michael Son(michaelson@nate.com)
//Apr.22.2013.

//Session_start
session_start();

//Require_once
require_once ($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFObject.class.php');
?>
<!DOCTYPE>
<html>
	<head>
		<title>Database Utility 1.0 </title>
	</head>
	<body>
		<form target='.' method='post'>
			Kind: <input type='text' name='kind' value='mysql' /><br />
			Server: <input type='text' name='server' placeholder='Server' /><br />
			Database: <input type='text' name='database' placeholder='Database' /><br />
			Username: <input type='text' name='username' placeholder='Username' /><br />
			Password: <input type='text' name='password' placeholder='Password' /><br />
			Prefix: <input type='text' name='prefix' value='xf' /><br />
			<input type='submit' />
		</form>
	</body>
</html>