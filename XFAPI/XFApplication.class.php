<?php
//XFApplication Class
//Michael Son(michaelson@nate.com)
//2013SEP28

//Require_once

//Class
class XFApplication extends XFObject {
	var $name;
	var $icon, $menu, $view;
	
	function show() {
		echo "<div class='$this->name'>\n";
		echo "<h2><img src='$icon[x64]' alt='icon' />$name</h2>";
		echo XFString::tab(1, $menu);
		echo "<h3>$view[0]['title']</h3>";
		echo $view[0]['contents'];
		echo "</div>\n";
	}
}
?>