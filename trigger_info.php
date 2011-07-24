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
	
	$zabbixTriggerId = (int) $_GET['triggerid'];
    $zabbixHostId = (int) $_GET['hostid'];
	if ($zabbixTriggerId > 0 && $zabbixHostId > 0) {
        // Retrieve the trigger information
        $trigger = $zabbix->getTriggerByTriggerAndHostId($zabbixTriggerId, $zabbixHostId);       
        $host = $zabbix->getHostById($zabbixHostId);
        $events = $zabbix->getEventsByTriggerAndHostId($zabbixTriggerId, $zabbixHostId);
                
?>
	<div id="trigger_<?php echo $zabbixTriggerId?>">
		<div class="toolbar">
			<h1><?=$host->host?>: <?=cleanTriggerDescription($trigger["description"])?></h1>
			<a class="back" href="#">Back</a>
		</div>
        
        <h2>Host details</h2>
        <ul class="rounded">
            <li>Host: <?=$host->host?></li>
            <li>DNS: <?=$host->dns?></li>
            <li>IP: <?=$host->ip?></li>            
        </ul>
		
		<h2>Trigger details</h2>
		<ul class="rounded">
            <li>Description: <?=cleanTriggerDescription($trigger["description"])?></li>
            <li>Comment: <?=$trigger["comments"]?></li>
        </ul>
        
        <h2>Events</h2>
        <ul class="rounded">
        <?php
            $prev_clock = null;
            if (is_array($events) && count($events) > 0) {
                foreach ($events as $event) {
                    $problem_time = "";
                    if ($event->value == 1) {
                        // This event is in the "problem" state
                        if ($prev_clock != null) {
                            $problem_time = "<i>(lasted ". ($prev_clock - $event->clock) ." seconds)</i>";
                        }
                    }
                ?>
                    <li>
                        <font class="event_list_<?=$event->value?>">
                            <?=convertEventClock($event->clock)?>: <?=convertEventValue($event->value)?>
                        </font>
                        <font class="event_list_timing">
                            <?=$problem_time?>
                        </font>
                    </li>
                <?php
                    $prev_clock = $event->clock;
                }
            } else {
            ?>
                <li>No events found</li>
            <?php
            }
        ?>
        </ul>
	</div>
<?php
	} else {
		echo "Invalid triggerid.";
	}
?>