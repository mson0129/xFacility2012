<?php
//Jarvis
//Michael Son(michaelson@nate.com)
//2013.Jul.15.

//Session_start
session_start();

//Require_once
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFObject.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/utils/jarvis.xfapp/classes/UIJarvis.class.php');

$jarvis = new UIJarvis();
echo $jarvis->show(); 
?>