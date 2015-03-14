<?php
class UIMenu {
	var $selected;
	
	var $tpl;
	var $view;
	var $value;
	
	function UIMenu($tpl = "default") {
		//Selected Menu
		$this->value['index'][0]['menu'] = "projects";
		$this->value['index'][0]['menuName'] = "프로젝트";
		$this->value['index'][1]['menu'] = "people";
		$this->value['index'][1]['menuName'] = "사람";
		$this->value['index'][2]['menu'] = "applications";
		$this->value['index'][2]['menuName'] = "응용프로그램";
		$this->value['index'][3]['menu'] = "files";
		$this->value['index'][3]['menuName'] = "파일";
		$this->value['index'][4]['menu'] = "spots";
		$this->value['index'][4]['menuName'] = "장소";
		$this->value['index'][5]['menu'] = "schedules";
		$this->value['index'][5]['menuName'] = "일정";
		for($i=0; $i<count($this->value['index']); $i++) {
			$this->value['index'][$i]['selected'] = NULL;
			if($this->value['index'][$i]['menu']==$_GET['jarvis']&&!is_null($_GET['jarvis'])) {
				$this->value['index'][$i]['selected'] = " Selected";
				$this->selected = true;
			}
		}
		
		//tpl
		$this->tpl = $tpl;
	}
	
	function show() {
		$file = new XFIOFile("/xfacility/utils/jarvis.xfapp/classes/UIMenu.ui/".$this->tpl."/menu.htm");
		$contents = new XFString($file->readFile());
		$contents->replace($this->value);
		return $contents->string;
	}
}
?>