<?php
	// Destroy cookies
	//setcookie("zabbixUsername", "", time() - 1);
	//setcookie("zabbixApi", "", time() - 1);
	setcookie("zabbixPassword", "", time() - 1);
	setcookie("zabbixAuthHash", "", time() - 1);	
	
	header("Location: index.php");
	exit();
?>