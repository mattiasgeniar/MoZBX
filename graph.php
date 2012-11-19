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

    require_once("template/header.php");

	$zabbixGraphId = (string) $_GET['graphid'];
    $zabbixGraphPeriod = (string) $_GET['period'];
    $zabbixHostId = (string) $_GET['hostid'];
    $zabbixHostGroupId = (string) $_GET['groupid'];
    $zabbixHostGroupName = (string) urldecode($_GET['groupname']);
    $zabbixHostName = (string) urldecode($_GET['hostname']);

    $urlParameters = "hostid=". $zabbixHostId ."&hostname=". $zabbixHostName ."&groupid=". $zabbixHostGroupId ."&groupname=". urlencode($zabbixHostGroupName);

    if ($zabbixGraphId > 0) {
        $graph = $zabbix->getGraphById($zabbixGraphId);
    } else {
        $graph = null;
    }
?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a>
                <span class="divider">/</span>
            </li>
            <li>
                <a href="hostgroups.php">Hostgroups</a>
                <span class="divider">/</span>
            </li>
            <li>
                <a href="hosts.php?hostgroupid=<?php echo $zabbixHostGroupId; ?>">
                    <?php echo $zabbixHostGroupName; ?>
                </a>
                <span class="divider">/</span>
            </li>
            <li>
                <a href="host.php?<?php echo $urlParameters; ?>">
                    <?php echo $zabbixHostName; ?>
                </a>
                <span class="divider">/</span>
            </li>
            <li class="active">
                <?php echo $graph->name; ?>
            </li>
        </ul>
    </div>
</div>

<div class="container">


<?php
    if ($graph != null) {
    ?>
	    <h2><?php echo $graph->name?></h2>
        <p>
            Now showing a <?php echo round($zabbixGraphPeriod/3600, 0); ?> hour period.
        </p>
        <img src="graph_img.php?graphid=<?php echo $zabbixGraphId?>&period=<?php echo $zabbixGraphPeriod?>" width="500" />

        <ul class="nav nav-pills nav-stacked">
            <li<?php echo ((string) $zabbixGraphPeriod == 3600) ? ' class="active"' : ''; ?>>
                <a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=3600&<?php echo $urlParameters; ?>">
                    <i class="icon-time"></i> 1 hour
                </a>
            </li>
            <li<?php echo ((string) $zabbixGraphPeriod == 7200) ? ' class="active"' : ''; ?>>
                <a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=7200&<?php echo $urlParameters; ?>">
                    <i class="icon-time"></i> 2 hours
                </a>
            </li>
            <li<?php echo ((string) $zabbixGraphPeriod == 10800) ? ' class="active"' : ''; ?>>
                <a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=10800&<?php echo $urlParameters; ?>">
                    <i class="icon-time"></i> 3 hours
                </a>
            </li>
            <li<?php echo ((string) $zabbixGraphPeriod == 21600) ? ' class="active"' : ''; ?>>
                <a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=21600&<?php echo $urlParameters; ?>">
                    <i class="icon-time"></i> 6 hours
                </a>
            </li>
            <li<?php echo ((string) $zabbixGraphPeriod == 43200) ? ' class="active"' : ''; ?>>
                <a href="graph.php?graphid=<?php echo $zabbixGraphId?>&period=43200&<?php echo $urlParameters; ?>">
                    <i class="icon-time"></i> 12 hours
                </a>
            </li>
        </ul>
	</div>
<?php
	} else {
    ?>
        <div class="alert alert-error">
            Invalid graph specified.
        </div>
    <?php
	}
?>
