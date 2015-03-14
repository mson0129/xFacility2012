<?php
//XFMySQL(XFObject>XFDB>XFMySQL)
//Michael Son(michaelson@nate.com)
//Jul.01.2012.
//2013SEP14 - Correct the error of findTable().
//2013SEP28 - Add a new set of functions - findTable(), getTable(), modTable().

//Require_once

//Class
class XFMySQL extends XFDB {
	//Run
	function XFMySQL($query=NULL) {
		$this->XFDB();
		$this->connect();
		if(!is_null($query))
			$this->runQuery($query);
	}
	
	//Connect, Disconnect
	function connect() {
		$return = mysql_connect($this->server, $this->username, $this->password);
		if($return) {
			//Select database
			mysql_select_db($this->database, $return);
			$this->link = $return;
			return $this->link;
		} else {
			return false;
		}
	}
	function disconnect() {
		//Close a link;
		mysql_close($this->link);
		unset($this->link);
	}
	
	//Table
	function findTable($table) {
		$query = "SHOW TABLES FROM `$this->database` WHERE `Tables_in_$this->database`='".$this->prefix."_".$table."'";
		if($this->link==NULL) {
			$this->connect();
		}
		$result = @mysql_query($query, $this->link);
		
		if($result !== FALSE) {
			return true;
		} else {
			return false;
		}
	}
	function getTable($table, $page, $number, $conditions = NULL) {
		$start = ($page-1)*$number;
		$where = 1;
		if(is_array($conditions)) {
			$where .= " AND (";
			unset($rowWhere);
			foreach($conditions as $row) {
				if(!is_null($rowWhere))
					$rowWhere = " OR ";
				$rowWhere .= "(";
				unset($columnWhere);
				foreach($row as $column => $value) {
					if(!is_null($columnWhere))
						$columnWhere = " AND ";
					$columnWhere .= "`".$column."` LIKE '".$value."'";
				}
				$rowWhere .= $columnWhere.")";
			}
			$where .= $rowWhere.")";
		}
		$this->query = "SELECT * FROM `".$this->prefix."_".$table."` WHERE $where ORDER BY `no` DESC LIMIT $start, $number;";
		return $this->runQuery();
	}
	function modTable($table, $xfArray, $rows=NULL) {
		//Select Table Directly
		$fields = $this->getFieldName($table);
		if(is_array($rows)) {
			sort($rows);
			$counter = 0;
		}
		foreach($xfArray as $row => $columns) {
			if(is_array($rows)&&$rows[$counter]!=$row) {
				continue;
			} else if(is_array($rows)&&$rows[$counter]==$row){
				$counter++;
			}
			unset($column, $set, $where, $values);
			
			if(!is_null($columns['no'])) {
				//Get Etc
				$where = "`no` = '".$columns['no']."'";
				$this->query = "SELECT * FROM `".$this->prefix."_".$table."` WHERE ".$where." LIMIT 1;\n";
				$this->runQuery();
				$tempTable = $this->parseResult();
				$tempEtc = trim($tempTable[0]['etc']);
				if(substr($tempEtc, -1) == ";") {
					$tempEtc = substr($tempEtc, 0, -1);
				}
				$etcs = split(";", $tempEtc);
				foreach($etcs as $etc) {
					list($etcKey, $etcValue) = explode(':', $etc);
					if(is_null(trim($etcKey))||trim($etcKey)=="")
						continue;
					$values['etc'][trim($etcKey)] = trim($etcValue);
				}
					
				//Parsing
				foreach($columns as $column => $value) {
					foreach($fields as $field) {
						if($column == "etc") {
							$tempEtc = trim($value);
							if(substr($tempEtc, -1) == ";") {
								$tempEtc = substr($tempEtc, 0, -1);
							}
							$etcs = split(";", $tempEtc);
							foreach($etcs as $etc) {
								list($etcKey, $etcValue) = explode(':', $etc);
								if(is_null(trim($etcKey))||trim($etcKey)=="")
									continue;
								$after[trim($etcKey)] = trim($etcValue);
							}
							$values['etc'] = array_merge($values['etc'], $after);
							break;
						} else if($column == $field) {
							$values[$column] = $value;
							break;
						} else if($field == "etc") {
							$values['etc'][trim($column)] = trim($value);
						}
					}
				}
				
				unset($temp, $column, $value);
				foreach($values as $field => $value) {
					if($field=="no") {
						continue;
					} else if($field == "etc") {
						unset($column, $temp, $value);
						foreach($values['etc'] as $column => $temp) {
							$value .= $column.":".$temp.";\n";
						}
						trim($value);
					}
					
					if(!is_null($set))
						$set .= ", ";
					if(!is_null($value)&&$value!="") {
						$set .= "`".$field."` = '".$value."'";
					} else {
						$set .= "`".$field."` = NULL";
					}
				}
				
				$this->query = "UPDATE `".$this->prefix."_".$table."` SET $set WHERE $where;";
				$this->runQuery();
			}
		}
	}
	
	//Run
	function runQuery($query=NULL) {
		unset($this->counter, $this->result);
		if($query!=NULL) {
			$this->query = $query;
		} else if($this->query!=NULL) {
			//$this->query = $this->query;
		} else {
			//Nothing to do
			return false;
		}
		if($this->link==NULL) {
			$this->connect();
		}
		$return = @mysql_query($this->query, $this->link);
		$this->disconnect();
		if(!$return) {
			return false;
		}
		$this->result = $return;
		if(strpos(strtolower($this->query),"select")!==false) {
			$this->counter = mysql_num_rows($return);	
		}
		return $this->result;
	}
	function getFieldName($table) {
		unset($this->counter, $this->result);
		$this->query = "SHOW FIELDS FROM `".$this->prefix."_".$table."`";
		$this->runQuery();
		if($this->result) {
			$i=0;
			while ($row = mysql_fetch_array($this->result)) {
				$return[$i] = $row['Field'];
				$i++;
			}
		} else {
			$return = false;
		}
		unset($this->result);
		return $return;
	}
	function parseResult($fields = NULL) {
		/*
		DEVELOPMENT:
			Michael Son(michaelson@nate.com)
			30.May.2010.
		DESCRIPTION:
			Parse a result of query
		CALL:
			parse_result();
		RETURN:
			$array[0]['no'] = 3;
			$array[0]['indicator'] = 1349851283;
			$array[0]['status'] = 1;
			$array[0]['id'] = "root";
			$array[0]['pw'] = "*68F9CD57023F17CBE06EE6365D9B4FEBF3EB3EE4";
			$array[0]['etc'] = "lang=en,ko,jp,ch";
			$array[1]['no'] = 4;
			$array[1]['indicator'] = 1352878344;
			$array[1]['status'] = 1;
			$array[1]['id'] = "administrator";
			$array[1]['pw'] = "*1F7E399139C29C99909A2C7E8C56247043C4FEE1";
			$array[1]['etc'] = "lang=ko,en";
			$array = NULL //Error
		*/
		
		if(!$this->result) {
			return false;
		}
		//If the list of fields are missed,
		if($fields == NULL) {
			$counter = 0;
		} else {
			if(is_array($fields)) {
				$temp = $fields;
				$counter = count($fields) - 1;
			} else {
				//Parse fields by comma
				$temp = split(",", $fields);
				//Estimate times for a loop
				$counter = substr_count($fields, ",");
			}
		}
		for ($i=0; $i<=$counter; $i++) {
			//If the list of fields are missed,
			if($fields == NULL) {
				$field = @mysql_field_name($this->result, $i);
				//If there is no field name,
				if ($field == NULL) {
					//Stop this Loop
					break;
				} else {
					//One more time
					$counter++;	
				}
			} else {
				$field = $temp[$i];
			}
			//Estimate times for a subloop
			$counter2 = mysql_num_rows($this->result);
			for($j=0; $j<$counter2; $j++) {
				$return[$j][$field] = mysql_result($this->result, $j, $field);
			}
		}
		//Return Array
		return $return;
	}
}
?>
