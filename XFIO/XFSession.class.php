<?php
class XFSession extends XFObject {
	private $session_db;

	function XFSession($domain) {
		//DB에 테이블 있는지 확인하고 테이블 없으면 생성
		session_set_cookie_params(2592000, "/", $domain);
		ini_set('session.cache_limiter' ,'nocache, must-revalidate-revalidate');
		session_set_save_handler(
		array($this, "open"),
		array($this, "close"),
		array($this, "read"),
		array($this, "write"),
		array($this, "destroy"),
		array($this, "garbageCollect")
		);
		session_start();
	}

	function open($savePath, $sessionName) {
		return false;
	}

	function close() {
		return NULL;
	}

	function read($id) {
		$db = new XFMySQL();
		$db->query = sprintf("SELECT `data` FROM `xf_xfio_session` WHERE `id` = '%s';", $id);
		if ($result = $db->runQuery()) {
			if ($db->counter) {
				$return = $db->parseResult();
				return $return[0]['data'];
			}
		}
		return '';
	}

	function write($id, $data) {
		$db = new XFMySQL();
		$db->query = sprintf("REPLACE INTO `xf_xfio_session` VALUES('%s', '%s', '%s', '%s')", $id, $_SERVER["REMOTE_ADDR"], time(), $data);
		return  $db->runQuery();
	}

	function destroy($id) {
		$db = new XFMySQL();
		$db->query = sprintf("DELETE FROM `xf_xfio_session` WHERE `id` = '%s'", $id);
		return  $db->runQuery();
	}

	function garbageCollect($maxlifetime) {

		$db = new XFMySQL();
		$max = 2592000;
		$db->query = sprintf("DELETE FROM `xf_xfio_session` WHERE `timestamp` < '%s';OPTIMIZE TABLE `xf_xfio_session`;", time() - $max);
		return $db->runQuery();
	}
}
?>