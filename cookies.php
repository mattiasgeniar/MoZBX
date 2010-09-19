<?php

	// Zabbix JSON API url
	if (isset($_COOKIE['zabbixApi']))
		$zabbixApi = $_COOKIE['zabbixApi'];
	elseif (isset($_POST['zabbixApi']))
		$zabbixApi = $_POST['zabbixApi'];
	else
		$zabbixApi = $arrSettings["zabbixApiUrl"];
	
	// Zabbix Username
	if (isset($_COOKIE['zabbixUsername']))
		$zabbixUser = $_COOKIE['zabbixUsername'];
	elseif (isset($_POST['zabbixUsername']))
		$zabbixUser = $_POST['zabbixUsername'];
	else
		$zabbixUser = $arrSettings["zabbixUsername"];
		
	// Zabbix Password
	if (isset($_COOKIE['zabbixPassword']))
		$zabbixPass = $_COOKIE['zabbixPassword'];
	elseif (isset($_POST['zabbixPassword']))
		$zabbixPass = $_POST['zabbixPassword'];
	else
		$zabbixPass = $arrSettings["zabbixPassword"];
		
	// Zabbix Login hash
	if (isset($_COOKIE['zabbixAuthHash']))
		$zabbixAuthHash = $_COOKIE['zabbixAuthHash'];
	elseif (isset($_POST['zabbixAuthHash']))
		$zabbixAuthHash = $_POST['zabbixAuthHash'];
?>