<?php
//XFString Class
//Michael Son(michaelson@nate.com)
//2013APR02
//2013MAY18 - Renamed methods shortly & Added correct method.
//2013JUL17 - replace method added.
//2013AUG01 - addTab method added.

//Require_once

//Class
class XFString extends XFObject {
	var $string;
	var $length;
	
	function XFString($string) {
		//$this->string = "HelloWorld";
		//$this->length = 10;
		
		$this->string = $string;
		$this->length = strlen($string);
	}
	
	function show() {
		return $this->string;
	}
	
	function correct($start = NULL, $len = NULL) {
		if(is_null($start)) {
			$start = 0;
		}
		if(is_null($len)) {
			$len = $this->length;
		}
		
		//Correcting $len
		if($start >= 0 && $len > 0 &&$start+$len>$this->length) {
			/*
			$start = 0;
			$len = 11;
			RETURN:
			$len = 10;
			*/
			$len = $this->length - $start;
		} else if($start < 0 && $len > -$start) {
			/*
			$start = -3;
			$len = 4;
			RETURN:
			$len = 3;
			*/
			$len = -$start;
		} else if($start < 0 && $this->length + $start < 0) {
			/*
			$start = -11
			RETURN:
			$start = 0;
			$len = 10;
			*/
			$start = 0;
			$len = $this->length;
		}
			
		//Error
		else if($start < 0 && $len < $start) {
			/*
			$start = -3;
			$len = -4;
			RETURN:
			$start = 0;
			$len = 0;
			*/
			$start = 0;
			$len = 0;
		} else if($start >= 0 && $start>$this->length) {
			/*
			$start = 11;
			RETURN:
			$start = 0;
			$len = 0;
			*/
			$start = 0;
			$len = 0;
		} else if($start >= 0 && $len < 0 && $this->length - $start + $len < 0) {
			/*
			$start = 0;
			$len = -11;
			RETURN:
			$start = 0;
			$len = 0;
			*/
			$start = 0;
			$len = 0;
		}
		
		//Correcting Minus
		if($start < 0)
			$start = $this->length + $start;
		if($len < 0)
			$len = $this->length + $len;
		
		$return[0] = $start;
		$return[1] = $len;
		
		return $return;
	}
	
	function upper($start = NULL, $len=NULL) {
		$temp = $this->correct($start, $len);
		$start = $temp[0];
		$len = $temp[1];
		
		//0, 1 .H.elloWorld
		//0, -9 .H.elloWorld
		//-10, 1 .H.elloWorld
		//-10, -9 .H.elloWorld
		$first = substr($this->string, 0, $start);
		$mid = strtoupper(substr($this->string, $start, $len));
		$last = substr($this->string, $start+$len);
		$this->string = $first.$mid.$last;
		$return = $this->string;
		
		return $return;
	}
	
	function lower($start = NULL, $len = NULL) {
		$temp = $this->correct($start, $len);
		$start = $temp[0];
		$len = $temp[1];
			
		//0, 1 .h.elloWorld
		//0, -9 .h.elloWorld
		//-10, 1 .h.elloWorld
		//-10, -9 .h.elloWorld
		$first = substr($this->string, 0, $start);
		$mid = strtolower(substr($this->string, $start, $len));
		$last = substr($this->string, $start+$len);
		$this->string = $first.$mid.$last;
		$return = $this->string;
			
		return $return;
	}
	
	function insert($newString, $start = NULL, $len = NULL) {
		$temp = $this->correct($start, $len);
		$start = $temp[0];
		$len = $temp[1];
		
		$top = substr($this->string, 0, $start);
		$middle = $newString;
		$bottom = substr($this->string, $start+$len);
		
		$return = $top.$middle.$bottom;
		
		return $return;
	}
	
	function tab($tab = 1, $string=NULL) {
		if(is_null($string)) {
			$this->string = str_replace("\n", "\n".str_repeat("\t", $tab), $this->string);
			$return = $this->string;
		} else {
			$return = str_replace("\n", "\n".str_repeat("\t", $tab), $string);
		}
		return $return;
	}
	
	function replace($xfArray, $wrapStart = "{", $wrapEnd= "}") {
		$return = $this->string;
		foreach($xfArray as $table => $rows) {
			if(!is_array($rows)) {
				//Simple Key-Value
				$key = "<!--{=".$table."}-->";
				$temp = str_replace($key, $rows, $return);
				$key = $wrapStart."=".$table.$wrapEnd;
				$return = str_replace($key, $rows, $temp);
				unset($temp);
				
				$loopStartKey = "<!--{".$table."}-->";
				$loopEndKey = "<!--{/".$table."}-->";
				if(strpos($return, $loopStartKey) !== false) {
					$pieces = explode($loopStartKey, $return);
					if(strpos($pieces[1], $loopEndKey) !== false) {
						$subPieces = explode($loopEndKey, $pieces[1]);
						$return = $pieces[0].ltrim($subPieces[1]);
					} else {
						$return = $pieces[0];
					}
				}
				unset($pieces, $subPieces);
			} else {
				//xfArray Rows
				$loopStartKey = "<!--{".$table."}-->";
				$loopEndKey = "<!--{/".$table."}-->";
				
				$pieces = explode($loopStartKey, $return);
				
				foreach($pieces as $index => $piece) {
					if($index==0) {
						$return = $piece;
						continue;
					}
					
					$subPiece = explode($loopEndKey, $piece);
					if(count($subPiece)>2) {
						//Loop Error
						break;
					}
					
					$i=0;
					while(1) {
						if(is_null($rows[$i]))
							break;
						$temp = $subPiece[0];
						foreach($rows[$i] as $column => $value) {
							$key = "<!--{=".$table."[".$column."]}-->";
							$temp = str_replace($key, $value, $temp);
							$key = $wrapStart."=".$table."[".$column."]".$wrapEnd;
							$temp = str_replace($key, $value, $temp);
						}
						$return .= ltrim($temp, " \n");
						unset($temp);
						$i++;
					}
					$return .= ltrim($subPiece[1], " \n")."\n";
					unset($subPiece);
				}
			}
		}
		$this->string = trim($return, " \n");
		return trim($return);
	}
}
?>