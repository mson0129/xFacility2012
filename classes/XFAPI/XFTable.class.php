<?php
//XFTable Class
//Michael Son(michaelson@nate.com)
//2013SEP14
//2013SEP28 - Add modValue() & modValueNoSync().

//Require_once


/*
USAGE:
Table Name: `xf_helloworld_testTable`

$testTable = new XFTable("helloworld", "testTable");
*/

//Class
class XFTable extends XFObject {
	//Properties
	var $application, $table, $columns, $tableName, $autoSync;
	
	//Cursor
	var $column, $row;
	
	//Values
	var $values;
	
	function XFTable($application, $table, $columns=NULL) {
		$this->application = $application;
		$this->table = $table;
		$this->tableName = $this->application."_".$this->table;
		$this->setColumns($columns);
		$this->getValues();
		
		/*
		foreach($this->values as $row => $columns) {
			$this->modValueNoSync(array("<a href='".$columns['no']."'>LINK:", "</a>"), "etc", $row);
		}
		*/
		
		//$this->modValue(NULL, "template", NULL, true);
		
		//$this->showTable();
	}
	
	//Columns
	function setColumns($columns=NULL) {
		if(!is_null($columns)) {
			if(is_array($columns)) {
				$this->columns = $columns;
			} else {
				$this->columns = array($columns);
			}
		} else {
			$db = new XFMySQL();
			if($db->findTable($this->tableName)==true) {
				$this->autoSync = true;
				$this->columns = $db->getFieldName($this->tableName);
			}
			unset($db);
		}
	}
	
	function getColumns($column) {
		
	}
	
	function addColumns($column, $order) {
		
	}
	
	function modColumns($oldColumn, $newColumn) {
		
	}
	
	function delColumns($column) {
		
	}
	
	//Values
	function getValues($page=1, $number=100) {
		//Sync
		if($this->autoSync==true) {
			$db = new XFMySQL;
			$db->getTable($this->tableName, $page, $number);
			$this->values = $db->parseResult($this->columns);
		}
		return $this->values;
	}
	
	function getValue($column, $row) {
		return $this->values[$row][$column];
	}
	
	function getIndexValue($column, $no) {
		$db = new XFMySQL;
		$db->getTable($this->tableName, 1, 1, array(array("no"=>$no)));
		$this->values = $db->parseResult($this->columns);
		return $this->values[0][$column];
	}
	
	function modValue($newValue, $column, $row=NULL, $isAll=false) {
		/*
		Modify values of the table.
		
		Example:
			$this->modValue(NULL, "template", NULL, true);
		*/
		$return = $this->modValueNoSync($newValue, $column, $row, $isAll);
		
		//Sync
		if($this->autoSync==true) {
			$db = new XFMySQL;
			$db->modTable($this->tableName, $this->values, $return);
		}
		
		return $return;
	}
	
	function modValueNoSync($newValue, $column, $row=NULL, $isAll=false) {
		/*
		Not syncing with DB, it modifies values of the table to support for links and more.
		
		Example:
			foreach($this->values as $row => $columns) {
				$this->modValueNoSync(array("<a href='".$columns['no']."'>LINK: ", "</a>"), "no", $row);
			}
		*/
		
		if(is_null($row)) {
			for($i=0; $i<count($this->values); $i++) {
				if(!is_null($this->values[$i][$column]||$isAll)) {
					if(is_array($newValue)) {
						$this->values[$i][$column] = $newValue[0].$this->values[$i][$column].$newValue[1];
					} else {
						$this->values[$i][$column] = $newValue;
					}
					$return[] = $i;
				}
			}
		} else {
			if(is_array($newValue)) {
				$this->values[$row][$column] = $newValue[0].$this->values[$row][$column].$newValue[1];
			} else {
				$this->values[$row][$column] = $newValue;
			}
			$return[] = $row;
		}
		
		return $return;
	}
	
	//Table
	function showTable() {
		echo "<h1>".$this->tableName."</h1>\n";
		echo "<table>\n";
		echo "\t<tr>\n";
		foreach($this->columns as $column) {
			echo "\t\t<th>$column</th>\n";
		}
		echo "\t</tr>\n";
		foreach($this->values as $row) {
			echo "\t<tr>\n";
			foreach($row as $value) {
				echo "\t\t<td>$value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";
	}
}
?>