<?php
//XFRelations
//Michael Son(michaelson@nate.com)
//2013AUG01

//Require_once

//Class
class XFRelations extends XFObject {
	var $no;
	var $status;
	var $fromApp;
	var $fromTable;
	var $fromNo;
	var $toApp;
	var $toTable;
	var $toNo;
	var $etc;
	
	function create() {
		$db = new XFMySQL();
		$db->query = "INSERT INTO `xf_xfcore_relations` (`status`, `fromApp`, `fromTable`, `fromNo`, `toApp`, `toTable`, `toNo`, `etc`) VALUES (".$this->status.",".$this->fromApp.",".$this->fromTable.",".$this->fromNo.",".$this->toApp.",".$this->toTable.",".$this->toNo.",".$this->etc.");";
		$db->runQuery();
		
		return $return;
	}
	
	function modify() {
		$db = new XFMySQL();
		$update = NULL;
		
		if(!is_null($this->status))
			$update .= "`status` = '".$this->status."'";
		if(!is_null($this->fromApp)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`fromApp` = '".$this->fromApp."'";
		}
		if(!is_null($this->fromTable)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`fromTable` = '".$this->fromTable."'";
		}
		if(!is_null($this->fromNo)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`fromNo` = '".$this->fromNo."'";
		}
		if(!is_null($this->toApp)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`toApp` = '".$this->toApp."'";
		}
		if(!is_null($this->toTable)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`toTable` = '".$this->toTable."'";
		}
		if(!is_null($this->toNo)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`toNo` = '".$this->toNo."'";
		}
		if(!is_null($this->etc)) {
			if(!is_null($update))
				$update .= ", ";
			$update .= "`etc` = '".$this->etc."'";
		}
		
		$db->query = "UPDATE ".$update." WHERE `no` = '.$this->no.'";
		$db->runQuery();
		
		return $return;
	}
	
	function delete($no = NULL) {
		if(is_null($no)) {
			if(!is_null($this->no)) {
				$no = $this->no;
				$db = new XFMySQL();
				$db->query = "DELETE FROM `xf_xfcore_relations` WHERE `no` = '$no';";
				$db->runQuery();
			} else {
				//NOTHING TO DELETE
			}
		}
		
		return $return;
	}
}
?>