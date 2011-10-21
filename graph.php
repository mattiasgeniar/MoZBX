<?php
        require_once("config.inc.php");
	require_once("functions.php");
	require_once("class_zabbix.php");
	require_once("cookies.php");

	$arrSettings["zabbixApiUrl"] = str_replace("api_jsonrpc.php", "", $zabbixApi);
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

	$zabbixGraphId = (int) $_GET['graphid'];
    $zabbixGraphPeriod = (int) $_GET['period'];
    if ($zabbixGraphId > 0) {
        $graph = $zabbix->getGraphById($zabbixGraphId);
        ?>
	<div id="graphs_general<?php echo $zabbixGraphId?>">
		<div class="toolbar">
			<h1><?php echo $graph->name?></h1>
			<a class="back" href="#">Back</a>
		</div>
        <img src="graph_img.php?graphid=<?php echo $zabbixGraphId?>&period=<?php echo $zabbixGraphPeriod?>" width="500" />
        <ul class="rounded">
            <li><a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=3600">1 hour</a></li>
            <li><a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=7200">2 hours</a></li>
            <li><a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=10800">3 hours</a></li>
            <li><a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=21600">6 hours</a></li>
            <li><a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=43200">12 hours</a></li>
        </ul>
	</div>
<?php
	} else {
		echo "Invalid graph.";
	}
?>
