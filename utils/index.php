<?php
//UtilityLauncher
//Michael Son(michaelson@nate.com)
//2013.Jul.15.

//Session_start
session_start();

//Require_once
require_once ($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFObject.class.php');

$dir = new XFIODirectory("/xfacility/utils/");
foreach($dir->browseDirectory() as $value) {
	if(substr($value, -6)!=".xfapp") {
		continue;
	} else {
		$body .= "<a href='".$value."'>".substr($value, 0, -6)."</a><br />\n";
	}
}

$file = new XFIOFile("/xfacility/utils/view/layout.htm");
echo str_replace("[=body]", $body, $file->readFile());
?>