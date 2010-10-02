<?php
	if (strpos($_SERVER['SERVER_NAME'], 'demo') !== false)
		require_once("config-demo.php");
	else {
		if (file_exists("config.php"))
			require_once("config.php");
		else
			require_once("config-live.php");
	}
?>