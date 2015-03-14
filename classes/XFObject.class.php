<?php
//XFObject
//Michael Son(michaelson@nate.com)
//Jun.28.2012.
//May.18.2013. XFTables
//Jul.15.2013. XFIODirectory, XFIOFile, XFCoreUser
//2013OCT29 Classes Auto Loader
//201400000 New Classes Auto Loader
	//magic_quotes_gpc
	//http://stackoverflow.com/questions/13545962/directive-magic-quotes-gpc-is-deprecated-in-php-5-3-and-greater-laravel
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}

	//Shutdown
	function shutdown() {
		//Error
		$error = error_get_last();
		if(is_array($error) && $error['type']==4) {
			echo "Class Error: ".$error['file']."\n"//이 파일을 Exception List에 추가!
				."xFacility Loader will be rebooted in 5 seconds."; 
			echo "<meta http-equiv='refresh' content='5;url=http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."' />";
		} else {
			//print_r($error);
		}
		return true;
	}
	function loader($classPath=NULL) {
		if(is_null($classPath))
			$classPath = $_SERVER['DOCUMENT_ROOT']."/xfacility2012/classes"; 
		$paths[0] = $classPath;
		$j = 1;
		for($i=0; $i<$j; $i++) {
			$handle = opendir($paths[$i]);
			while(false !==($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(!is_dir($paths[$i]."/".$file)) {
						require_once($paths[$i]."/".$file);
						//echo $paths[$i]."/".$file."\n";
					} else {
						$paths[] = $paths[$i]."/".$file;
						$j = count($paths);
					}
				}
			}
		}
		return false;
	}
	
	//Mobile Checker
	function isMobile() {
		//Orignal: http://esajin.kr/291
		global $HTTP_USER_AGENT;
		$MobileArray  = array("iphone","lgtelecom","skt","mobile","samsung","nokia","blackberry","android","android","sony","phone");
	
		$checkCount = 0;
		for($i=0; $i<sizeof($MobileArray); $i++){
			if(preg_match("/$MobileArray[$i]/", strtolower($HTTP_USER_AGENT))){ $checkCount++; break; }
		}
		return ($checkCount >= 1) ? 1 : 0;
	}
	
	register_shutdown_function('shutdown');
	loader();
	
	//Require_once(Auto) - New Version
	//Get Path List
		//Except specific paths
	//Get File List
		//Except specific files
	//And Utility for managing exception list.
		
	//Class
	class XFObject {
		//Variables
		
		//Methods
	}
?>