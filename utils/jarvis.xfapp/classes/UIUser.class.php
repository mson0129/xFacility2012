<?php
class UIUser extends XFObject {
	//User
	var $no;
	var $status;
	var $id;
	var $etc;
	var $online;
	
	//UI
	var $tpl;
	var $view;
	var $value;
	
	function UIUser($tpl = "default") {
		//PROC
		//session check-up
		if(is_null($_SESSION['xfcore']['users'])) {
			//Need to sign-in
			$this->view = "signin";
			$this->online = false;
			if($_POST['id']||$_POST['password']) {
				//Sign-in
				$xfUser = new XFUser();
				$xfUser->id = $_POST['id'];
				$xfUser->password = $_POST['password'];
				if($xfUser->signin()===false) {
					//Fail to sign-in
					$this->value['msg'] = "{=Jarvis_UIUser.sign-in_error}";
				} else {
					//Succeed to sign-in
					$this->no = $xfUser->no;
					$this->status = $xfUser->status;
					$this->id = $xfUser->id;
					$this->etc = $xfUser->etc;
					$this->view = "online";
					$this->online = true;
					$this->value['msg'] = "{Jarvis_UIUser.sign-in}";
				}
				unset($xfUser);
			} else {
				//No User -> Signin Form
				$this->value['msg'] = "{=Jarvis_UIUser.null}";
			}
		} else {
			//Get User
			$this->view = "online";
			$this->online = true;
			if($_GET['user']=="signout") {
				$this->view = "signin";
				$this->online = false;
				$this->signout();
				$this->value['msg'] = "{=Jarvis_UIUser.sign-out}";
			} else {
				$this->value['msg'] = "{Javis_UIUser.hello_".$id."}";
				$this->no = $_SESSION['xfcore']['users'][0]['no'];
				$this->status = $_SESSION['xfcore']['users'][0]['status'];
				$this->id = $_SESSION['xfcore']['users'][0]['id'];
				$this->etc = $_SESSION['xfcore']['users'][0]['etc'];
				$this->value['msg'] = "{Jarvis_UIUser.online}";
			}
		}
		
		//Template
		$this->tpl = $tpl;
	}
	
	function signin($id, $password) {
		echo $id."@".$password;
	}
	
	function signout() {
		unset($_SESSION['xfcore']['users']);
	}
	
	function show($widget = "false") {
		$file = new XFIOFile("/xfacility/utils/jarvis.xfapp/classes/UIUser.ui/".$this->tpl."/".$this->view.".htm");
		$contents = new XFString($file->readFile());
		if($this->online===false) {
			$this->value['id'] = "아이디";
			$this->value['password'] = "비밀번호";
			$this->value['signin'] = "로그인";
			$this->value['Jarvis_UIUser.null'] = "로그인해주십시오.";
			$this->value['Jarvis_UIUser.sign-in_error'] = "로그인에 실패했습니다. 아이디와 비밀번호를 확인해주십시오.";
			$this->value['Jarvis_UIUser.sign-out'] = "로그아웃했습니다.";
		} else {
			$this->value['id'] = $this->id;
		}
		
		$return = $contents->replace($this->value);
		return $return;
	}
}
?>