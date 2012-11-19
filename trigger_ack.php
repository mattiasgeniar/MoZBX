<?php
	require_once("config.inc.php");
	require_once("functions.php");
	require_once("class_zabbix.php");
	require_once("cookies.php");
	
	$zabbix = new Zabbix($arrSettings);
	
	// Populate our class
	$zabbix->setUsername($zabbixUser);
	$zabbix->setPassword($zabbixPass);
	$zabbix->setZabbixApiUrl($zabbixApi);
	
	// Login
	if (isset($zabbixAuthHash) && strlen($zabbixAuthHash) > 0) { 
		// Try it with the authentication hash we have
		$zabbix->setAuthToken($zabbixAuthHash);
	} elseif (strlen($zabbix->getUsername()) > 0 && strlen($zabbix->getPassword()) > 0 && strlen($zabbix->getZabbixApiUrl()) > 0) {
		$zabbix->login();
	} 

	if (!$zabbix->isLoggedIn()) {
		header("Location: index.php");
		exit();
	}
    
    // Process the AJAX call of the form, if it exists
    $ack_ok = false;
    if (isset($_POST['type'])) {
        $post_type = $_POST['type'];
        switch ($post_type) {
            case "acknowledge":
                $zabbixEventId = array((string) $_POST['eventid']);
                $comment = addslashes(htmlspecialchars($_POST["comment"]));
    
                $zabbix->acknowledgeEvent($zabbixEventId, $comment);
                
                /* For now: if we got here, assume the ACK went OK
                TODO: check the return value of this call */
                $ack_ok = true;
                
                break;
            
            default:
                break;
        }
    }
    
	if ($ack_ok) {
        header("Location: trigger_info.php?triggerid=". (string) $_POST['triggerid']);
        exit();
    } else {
        /* Something, somewhere, went terribly wrong */
        ?>
        <ul class="rounded">
            <li>Sorry, something went wrong</li>
        </ul>
        <?php
    }
?>
