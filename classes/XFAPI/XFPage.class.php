<?php
//XFPage
/*
USAGE:
	PARAMETERS:
	$page = "<html>
	<body style='background-color: [=black];'>
	<span style='color: [=white];'>[helloworld]</span>
	
	<table>
		<thead>
			<tr>
				<td>A</td>
				<td>B</td>
				<td>C</td>
			</tr>
		</thead>
		<tbody>
			<!--[table]-->
			<tr>
				<td>[=table:a]</td>
				<td>[=table:b]</td>
				<td>[=table:c]</td>
			</tr>
			<!--[/table]-->
		</tbody>
	</table>
	
	</body>
	</html>";
	$contents = array("black" => "#000", "[=white]" => "#FFF", "[helloworld]" => "HELLOWORLD!", "table" => array(array("a" => "I", "b" => "am", "c" => "a boy."), array("a" => "hello", "b" => "world", "c" => "!"), array("a" => "welcome", "b" => "to", "c" => "xFacility.")));

	RUN:
	$xfPage = new XFPage($page, $contents);
	echo $xfPage->page;
	
	OR
	$output = XFPage::replace($page, $contents);
	echo $output;

OUTPUT:
<html>
<body style='background-color: #000;'>
<span style='color: #FFF;'>HELLOWORLD!</span>

<table>
	<thead>
		<tr>
			<td>A</td>
			<td>B</td>
			<td>C</td>
		</tr>
	</thead>
	<tbody>
		
		<tr>
			<td>I</td>
			<td>am</td>
			<td>a boy.</td>
		</tr>

		<tr>
			<td>hello</td>
			<td>world</td>
			<td>!</td>
		</tr>

		<tr>
			<td>Welcome</td>
			<td>to</td>
			<td>xFacility.</td>
		</tr>
		
	</tbody>
</table>

</body>
</html>
*/

//2014MAR10 Array Support

class XFPage extends XFObject {
	var $page;
	var $show;
	
	function XFPage($path, $replacements = NULL) {
		$this->page = $this->replaceFile($path, $replacements);
		$this->show = $this->page;
	}
	
	function replaceFile($path, $replacements = NULL) {
		$contents = XFFile::readFile($path);
		$return = XFPage::replaceText($contents, $replacements);
		
		return $return;
	}
	
	function replaceText($return, $replacements = NULL) {
		if(is_array($replacements)) {
			foreach($replacements as $key => $value) {
				if(substr($key, 0, 1)=="[" && substr($key, -1)=="]" && !is_array($value)) {
					$return = str_replace($key, $value, $return);
				} else if(is_array($value)) {
					if(substr_count($return, "<!--[".$key."]-->")>0 && substr_count($return, "<!--[/".$key."]-->")>0 && substr_count($return, "<!--[".$key."]-->")==substr_count($return, "<!--[/".$key."]-->")) {
						$temps = explode("<!--[".$key."]-->", $return);
						foreach($temps as $tempsKey => $temp) {
							if($tempsKey==0)
								continue;
							list($loops[], $tails[]) = explode("<!--[/".$key."]-->", $temp, 2);
						}
						$return = $temps[0];
						foreach($loops as $loop => $none) {
							foreach($value as $row => $columns) {
								$tempLoop = $loops[$loop];
								foreach($columns as $column => $columnValue)
									$tempLoop = str_replace("[=".$key.":".$column."]", $columnValue, $tempLoop);
								$return .= $tempLoop;
							}
							$return .= $tails[$loop];
						}
					}
				} else {
					$return = str_replace("[=".$key."]", $value, $return);
					if(substr_count($return, "<!--[".$key."]-->")>0 && substr_count($return, "<!--[/".$key."]-->")>0 && substr_count($return, "<!--[".$key."]-->")==substr_count($return, "<!--[/".$key."]-->")) {
						$temps = explode("<!--[".$key."]-->", $return);
						foreach($temps as $tempsKey => $temp) {
							if($tempsKey==0)
								continue;
							list($loops[], $tails[]) = explode("<!--[/".$key."]-->", $temp, 2);
						}
						$return = $temps[0];
						foreach($loops as $loop => $none) {
							$return .= $tails[$loop];
						}
					}
					if(substr_count($return, "[=".$key.":")>0) {
						$temps = explode("[=".$key.":", $return);
						foreach($temps as $tempsKey => $temp) {
							if($tempsKey==0)
								continue;
							list($loops[], $tails[]) = explode("]", $temp, 2);
						}
						$return = $temps[0];
						foreach($loops as $loop => $none) {
							$return .= $tails[$loop];
						}
					}
				}
			}
		}
		return $return;
	}
	
	function show() {
		return $this->show;
	}
}
?>