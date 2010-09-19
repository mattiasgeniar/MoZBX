<?php
	function arrSortFunctionHostgroupsName ($arrElement1, $arrElement2) {
		return strcmp($arrElement1["name"], $arrElement2["name"]);
	}
	
	function arrSortFunctionHostsName ($arrElement1, $arrElement2) {
		return strcmp($arrElement1["host"], $arrElement2["host"]);
	}
	
	function getVisitorIP () {
		if ($_SERVER['HTTP_X_FORWARD_FOR']) {
			$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
?>