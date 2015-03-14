<?php
//UIJarvis
//Michael Son(michaelson@nate.com)
//Jul.17.2013 - New

//Require_once
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/utils/jarvis.xfapp/classes/UIMenu.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/utils/jarvis.xfapp/classes/UIUser.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/utils/jarvis.xfapp/classes/UINav.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/xfacility/utils/jarvis.xfapp/classes/UICanvas.class.php');

//Class
class UIJarvis {
	var $application;
	var $version;
	var $date;
	var $build;
	
	var $menu;
	var $user;
	var $nav;
	var $canvas;
	
	var $view;
	var $value;
	var $tpl;
	
	function UIJarvis($tpl = "default") {
		//Application
		$this->application = "Jarvis";
		$this->version = "0.0.0";
		$this->date = "2013JUL17";
		$this->build = "000001";
		
		//PHP Info
		if($_GET['jarvis']=="phpinfo") {
			phpinfo();
			exit;
		}
		
		//Classes
		$this->menu = new UIMenu();
		$this->value['UIMenu'] = $this->menu->show();
		$this->user = new UIUser();
		$this->value['UIUser'] = $this->user->show();
		$this->nav = new UINav();
		$this->value['UINav'] = $this->nav->show();
		$this->canvas = new UICanvas();
		$this->value['UICanvas'] = $this->canvas->show();
		
		//View
		if(is_null($_SESSION['xfcore']['users'])) {
			$this->view = "gate";
		} else {
			$this->view = "home";
		}
		
		//tpl
		$this->tpl = $tpl;
	}
	
	function show() {
		$xfFile = new XFIOFile("/xfacility/utils/jarvis.xfapp/classes/UIJarvis.ui/".$this->tpl."/".$this->view.".htm");
		$xfString = new XFString($xfFile->readFile());
		$xfString->replace($this->value);
		return $xfString->string;
	}
}
?>