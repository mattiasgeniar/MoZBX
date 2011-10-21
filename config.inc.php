<?php
	if (strpos($_SERVER['SERVER_NAME'], 'demo') !== false)
		require_once("config-demo.php");
	else {
        // Load the default configuration file
        if (file_exists("config-live.php"))
            require_once("config-live.php");
        
        // If a custom config.php file exists, load it to overwrite the above.
        if (file_exists("config.php"))
			require_once("config.php");
	}
?>