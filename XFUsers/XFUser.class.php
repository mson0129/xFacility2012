<?php
//XFUser
//Michael Son(michaelson@nate.com)
//2013OCT30

//require_once

//class
class XFUser extends XFObject {
	var $no, $status, $id, $etc;
	
	function XFUser($no = NULL) {
		if(!is_null($no)) {
			$this->getUser($no);
		}
	}
	
	function create() {
		
	}
	
	function getUser($no) {
		$db = new XFMySQL();
		$db->query = "";
		$db->runQuery();
	}
	
	function getUserProfile($lang = NULL) {
		if(is_null($lang)) {
			$xfLang = new XFLanguage();
			$lang = $xfLang->lang;
		}
		$db = new XFMySQL();
		$db->query = "SELECT * FROM `xf_xfusers_profiles` WHERE `usersNo` = '$this->no' AND `lang` = '$lang';";
		$db->runQuery();
		//table에서 lang목록만 가져옴.
		$xfLang->customLang = $table->getColumnValues("lang"); //테이블의 lang목록을 customLang으로 해서 랭귀지 셀렉트
		unset($db);
	}
	
	function modify() {
		$db = new XFMySQL();
		$db->query = "UPDATE FROM `xf_xfusers_users` SET `status`='$this->status', `id`='$this->id', `etc`='$this->etc' WHERE `no` = '".$this->no."';";
		$db->runQuery();
		unset($db);
	}
	
	function delete() {
		$db = new XFMySQL();
		$db->query = "DELETE FROM `xf_xfusers_users` WHERE `no`='".$this->no."';";
		$db->runQuery();
		unset($db);
	}
}
?>