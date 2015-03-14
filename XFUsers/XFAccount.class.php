<?php
//XFAccount
//Michael Son(michaelson@nate.com)
//2013JUL31
//2013OCT30 rename the class.

//require_once

//class
class XFAccount extends XFObject {
	var $no, $status, $email, $password, $code, $etc;
	
	function XFAccount() {
		/*Autosignin
		if(is_null($_COOKIE['users']['accounts']['code'])) {
			
		}
		*/
	}
	
	function signin() {
		if($this->email==NULL&&$this->password==NULL) {
			return false;
		} else {
			$return = $this->matchupID();
			if(!$return) {
				$return = false;
				//return "{XFUsers_WrongID}";
			} else {
				$return = $this->matchupPassword();
				if(!$return) {
					$return = false;
					//return "{XFUsers_WrongPassword}";
				} else {
					$return = $this->matchupActivation();
					if(!$return) {
						$return = false;
						//return "{XFUsers_NotActivated}";
					} else {
						$_SESSION['xfusers']['accounts'][] = $return[0];
					}
				}
			}
		}
		return $return;
	}
	
	function isSignedIn() {
		$return = false;
		if($this->no==NULL) {
		} else {
			foreach($_SESSION['xfusers']['accounts'] as $xfUser) {
				if($this->no == $xfUser['no']) {
					$return = true;
					break;
				}
			}
		}
		return $return;
	}
	
	function signup() {
		if($this->status==NULL) {
			$this->status = 0;
		}
		$database = new XFMySQL;
		$query = "INSERT INTO `xf_xfusers_accounts` (
				`status` ,
				`email` ,
				`password` ,
				`etc`
			)
			VALUES (
				'".addslashes($this->status)."', '".addslashes($this->email)."', PASSWORD('".addslashes($this->password)."') , '".addslashes($this->etc)."'
			);";
		$database->runQuery($query);
	}
	function signout($email = NULL) {
		if(is_null($email)) {
			unset($_SESSION['xfusers']['accounts']);
		} else {
			$counter = count($_SESSION['xfusers']['accounts']);
			for($i=0; $i<$counter; $i++) {
				if($_SESSION['xfusers']['accounts'][$i]['email'] == $email) {
					unset($_SESSION['xfusers']['accounts'][$i]);
				}
			}
		}
	}
	
	//Activation, Deactivation
	function activate($code) {
		if(!is_array($code)) {
			$codes[] = $code;
		}
		foreach($codes as $code) {
			$decodes[] = XFAccount::decodeActivationCode($code);
		}
		foreach($decodes as $decode) {
			$database = new XFMySQL;
			$query = "UPDATE `xf_xfusers_accounts` SET `status` = '2' WHERE `no` = ".$decode['no']." AND `email` = '".$decode['email']."';";
			$database->runQuery($query);
			$database->runQuery("SELECT * FROM `xf_xfusers_accounts` WHERE `no`='".$decode['no']."' AND `email`='".$decode['email']."' AND `status` > 0;");
			if($database->counter != 1) {
				return false;
			}
			return $database->parseResult();
		}
	}
	function deactivate() {
		$database = new XFMySQL;
		$database->runQuery("UPDATE `xf_xfusers_accounts` SET `status` = '0' WHERE `no` = ".$this->no);
	}
	function encodeActivationCode($no = NULL) {
		$database = new XFMySQL;
		if(is_null($no)) {
			$no = $this->no;
		}
		$database->runQuery("SELECT * FROM `xf_xfusers_accounts` WHERE `no`='".$no."'");
		if($database->counter != 1) {
			return false;
		}
		$results = $database->parseResult();
			
		$return = base64_encode("<row><column name='no'>".$results[0]['no']."</column><column name='email'>".$results[0]['email']."</column></row>");
			
		return $return;
	}
	function decodeActivationCode($code) {
		$code = base64_decode($code);
			
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $code, $struct, $index);
		xml_parser_free($parser);
			
		foreach($struct as $key => $value) {
			if($value['tag']=="COLUMN"&&$value['type']=="complete"&&$value['attributes']['NAME']=='no') {
				$return['no'] = $value['value'];
			} else if($value['tag']=="COLUMN"&&$value['type']=="complete"&&$value['attributes']['NAME']=='email') {
				$return['email'] = $value['value'];
			}
		}
			
		return $return;
	}
	
	//Match Up
	function matchupID() {
		$database = new XFMySQL;
		$database->runQuery("SELECT * FROM `xf_xfusers_accounts` WHERE `id`='".$this->id."';");
		if($database->counter != 1) {
			return false;
		}
		return $database->parseResult();
	}
	function matchupPassword() {
		$database = new XFMySQL;
		$database->runQuery("SELECT * FROM `xf_xfusers_accounts` WHERE `id`='".$this->id."' AND `password`=password('".$this->password."');");
		if($database->counter != 1) {
			return false;
		}
		return $database->parseResult();
	}
	function matchupActivation() {
		$database = new XFMySQL;
		$database->runQuery("SELECT * FROM `xf_xfusers_accounts` WHERE `id`='".$this->id."' AND `password`=password('".$this->password."') AND `status` > 0;");
		if($database->counter != 1) {
			return false;
		}
		return $database->parseResult();
	}
	
	//Format
	function formatDB() {
		$database = new XFMySQL;
		$database->runQuery("DESCRIBE `xf_xfusers_accounts`;");
		if($database->result) {
			$database->runQuery("DROP TABLE `xf_xfusers_accounts`");
		}
		$query = "CREATE TABLE `xf_xfusers_accounts` (
				`no` INT( 255 ) NOT NULL AUTO_INCREMENT ,
				`status` INT( 1 ) NOT NULL DEFAULT '0',
				`id` VARCHAR( 255 ) NOT NULL ,
				`password` VARCHAR( 41 ) NOT NULL ,
				`etc` TEXT NOT NULL ,
				PRIMARY KEY ( `no` )
			) COMMENT = 'Users of xFacility'";
		$database->runQuery($query);
	}
}
?>