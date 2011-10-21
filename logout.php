<?php
	// Destroy cookies
	setcookie("zabbixPassword", "bogus", time() + 1);
	setcookie("zabbixAuthHash", "", time() - 100);
	
	header("Location: index.php");
	exit();
?>