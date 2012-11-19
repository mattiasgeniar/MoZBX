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

    require_once("template/header.php");

	$zabbixTriggerId = (string) $_GET['triggerid'];
    $zabbixHostId = (string) $_GET['hostid'];
	if ($zabbixTriggerId > 0 && $zabbixHostId > 0) {
        // Retrieve the trigger information
        $trigger = $zabbix->getTriggerByTriggerAndHostId($zabbixTriggerId, $zabbixHostId);
        $host = $zabbix->getHostById($zabbixHostId);
        $events = $zabbix->getEventsByTriggerAndHostId($zabbixTriggerId, $zabbixHostId);

?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="activetriggers.php">Active Triggers</a> <span class="divider">/</span>
            </li>
            <li class="active">
                <?php echo cleanTriggerDescription($trigger["description"]); ?>
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <h2><?php echo $host->host?>: <?php echo cleanTriggerDescription($trigger["description"])?></h2>


    <h3>Host details</h3>
    <p>
        Host: <?php echo $host->host?> <br />
        DNS: <?php echo $host->dns?> <br />
        IP: <?php echo $host->ip?> <br />
    </p>

    <h3>Trigger details</h3>
    <p>
        Description: <?php echo cleanTriggerDescription($trigger["description"]) ?> <br />
        Comment: <?php echo cleanTriggerDescription($trigger["comments"]) ?> <br />
    </p>

    <?php
        if (is_array($events) && count($events) > 0) {
            $first_event  = $events[0];
            $acknowledge_event = "";
            if ($first_event->value == 1 && $first_event->acknowledged != 1) {
                // This event is in the "problem" state, and it's the last event: meaning the trigger is still in problem
                // We can acknowledge that
                ?>
                <h3>Acknowledge event</h3>
                <form action="trigger_ack.php" method="post" class="well">
                    <input type="hidden" name="eventid" value="<?php echo $first_event->eventid; ?>" />
                    <input type="hidden" name="triggerid" value="<?php echo $zabbixTriggerId; ?>" />
                    <input type="hidden" name="type" value="acknowledge" />

                    <label>Acknowledge</label>
                    <textarea  name="comment" id="comment" value="" placeholder="Comment"></textarea>
                    <span class="help-block">Please describe why you're acknowledging this event.</span>

                    <button type="submit" name="mZabbixTriggerAck" class="btn btn-primary">Acknowledge</button>
                </form>
                <?php
            }
        }
    ?>

    <h3>Events</h3>
    <?php
        $prev_clock = null;
        if (is_array($events) && count($events) > 0) {
        ?>
            <ul class="nav nav-list">
        <?php
            foreach ($events as $event) {
                $problem_time = "";
                if ($event->value == 1) {
                    // This event is in the "problem" state
                    if ($prev_clock != null) {
                        $problem_time = "<i>(lasted ". ($prev_clock - $event->clock) ." seconds)</i>";
                    }
                }

                if ($event->acknowledged == 1) {
                    $event->value = 2;
                }
            ?>
            <li<?php echo (convertEventValue($event->value) == 'Acknowledged') ? ' style="background-color: #dff0d8;"' : ''; ?>>
                <i class="icon-map-marker"></i>
                <?php echo convertEventClock($event->clock)?>: <?php echo convertEventValue($event->value)?>
                <?php echo $problem_time?>
            </li>
            <?php
                $prev_clock = $event->clock;
            }
        } else {
        ?>
            <div class="alert alert-info">
                <p>No events were found for this trigger.</p>
            </div>
        <?php
        }
    ?>

</div>

<?php
	} else {
		?>
        <div class="alert alert-error">
            <p>Invalid triggerID specified.</p>
        </div>
        <?php
	}
?>
