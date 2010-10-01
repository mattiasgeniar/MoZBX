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
	
	$zabbixHostId = (int) $_GET['hostid'];
	if ($zabbixHostId > 0) {
		$host 		= $zabbix->getHostById($zabbixHostId);
		
		// Graphs
		$graphs 	= $zabbix->getGraphsByHostId($zabbixHostId);
		$graphs		= $zabbix->sortGraphsByName($graphs);
		
		// Triggers
		$triggers	= $zabbix->getTriggersByHostId($zabbixHostId);
?>
	<div id="host_<?=$zabbixHostId?>" class="current">
		<div class="toolbar">
			<h1><?=$host->host?></h1>
			<a class="back" href="#">Back</a>
		</div>
		
		<?php
			if (is_array($graphs) && count($graphs) > 0) {
		?>
		<h2>Graphs</h2>
		<ul class="rounded">
		<?php
			foreach ($graphs as $graph) {
				echo "<li><a href=\"graph.php?graphid=". $graph["graphid"] ."\"><img src=\"images/chart.png\" class=\"icon_list\">". $graph["name"] ."</a></li>";
			}
		?>
		</li>
		<?php
			}
		?>
	</div>
<?php
	} else {
		echo "Invalid hostgroup.";
	}
?>