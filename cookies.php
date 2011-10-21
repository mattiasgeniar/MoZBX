<?php

	// Zabbix JSON API url
	if (isset($_POST['zabbixApi']))
		$zabbixApi = $_POST['zabbixApi'];
	elseif (isset($_COOKIE['zabbixApi']))
		$zabbixApi = $_COOKIE['zabbixApi'];
	else
		$zabbixApi = $arrSettings["zabbixApiUrl"];
	
	// Zabbix Username
	if (isset($_POST['zabbixUsername']))
		$zabbixUser = $_POST['zabbixUsername'];
	elseif (isset($_COOKIE['zabbixUsername']))
		$zabbixUser = $_COOKIE['zabbixUsername'];
	else
		$zabbixUser = $arrSettings["zabbixUsername"];
		
	// Zabbix Password
	if (isset($_POST['zabbixPassword']))
		$zabbixPass = $_POST['zabbixPassword'];
	elseif (isset($_COOKIE['zabbixPassword']))
		$zabbixPass = $_COOKIE['zabbixPassword'];
	else
		$zabbixPass = $arrSettings["zabbixPassword"];
		
	// Zabbix Login hash
	if (isset($_COOKIE['zabbixAuthHash']))
		$zabbixAuthHash = $_COOKIE['zabbixAuthHash'];
	elseif (isset($_POST['zabbixAuthHash']))
		$zabbixAuthHash = $_POST['zabbixAuthHash'];
?>