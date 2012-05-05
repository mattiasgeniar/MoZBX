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

	$zabbixHostId = (int) $_GET['hostid'];
	if ($zabbixHostId > 0) {
		$host 		= $zabbix->getHostById($zabbixHostId);

		// Graphs
		$graphs 	= $zabbix->getGraphsByHostId($zabbixHostId);
		$graphs		= $zabbix->sortGraphsByName($graphs);

		// Triggers
		$triggers	= $zabbix->getTriggersByHostId($zabbixHostId);
?>
	<div id="host_<?php echo $zabbixHostId?>">
		<div class="toolbar">
			<h1><?php echo $host->host?></h1>
			<a class="back" href="#">Back</a>
		</div>

        <h2>Host details</h2>
        <ul class="rounded">
            <li class="small">Host: <?php echo $host->host?></li>
            <li class="small">DNS: <?php echo $host->dns?></li>
            <li class="small">IP: <?php echo $host->ip?></li>
        </ul>

		<?php
			if (is_array($graphs) && count($graphs) > 0) {
		?>
		<h2>Graphs</h2>
		<ul class="rounded">
		<?php
			foreach ($graphs as $graph) {
                ?>
				<li class="small">
                    <a href="graph.php?graphid=<?php echo $graph["graphid"]?>&period=3600">
                    <img src="images/chart.png" class="icon_list"><?php echo $graph["name"]?></a>
                </li>
                <?php
			}
		?>
		</ul>
		<?php
			} else {
                echo "<h2>No graphs</h2>";
            }
		?>
	</div>
<?php
	} else {
		echo "Invalid hostgroup.";
	}
?>
