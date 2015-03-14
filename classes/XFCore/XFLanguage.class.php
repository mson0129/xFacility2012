<?php
//XFCore > XFLanguage - Get lists of languages(a Server and a Client) & Choose a language to show page.
//Michael Son(michaelson@nate.com)
//2013OCT30

//require_once

//class
class XFLanguage extends XFObject {
	var $lang, $serverLangs, $clientLangs, $customLangs;
	//String $lang = "en-US";
	//Array $serverLangs, $clientLangs, $customLangs = array("en-US", "ko-KR");
	
	function XFLanguage($customLangs = NULL) {
		$this->cumstomLangs = $customLangs;
		$this->getClientLangs();
		$this->getServerLangs();
		$this->selectLang();
	}
	
	function getClientLangs() {
		$langsWithWeight = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		foreach($langsWithWeight as $value) {
			$return[] = explode(";", $value);
		}
		unset($value);
		foreach($return as $value) {
			$this->clientLangs[] = trim(strtolower($value));
		}
		return $this->clientLangs;
	}
	
	function getServerLangs() {
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/xfacility/configs/languages.php")) {
			$file = new XFIOFile($_SERVER['DOCUMENT_ROOT']."/xfacility/configs/languages.php");
			$return = explode("\n", $file->readFile());
			unset($file);
		} else {
			$return = array("en-US");
		}
		foreach($return as $value) {
			$this->serverLangs[] = trim(strtolower($value));
		}
		return $this->serverLangs;
	}
	
	function selectLang() {
		if(is_null($this->customLangs)) {
			$supportLangs = $this->serverLangs;
		} else {
			$supportLangs = $this->customLangs;
		}
		
		foreach($this->clientLangs as $clientLang) {
			foreach($supportLangs as $supportLang) {
				if($clientLang == $supportLang) {
					$this->lang = $supportLang;
					return $this->lang;
				}
			}
		}
		return $serverLangs[0];
	}
}
?>