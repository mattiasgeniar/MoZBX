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

	$zabbixHostId = (string) $_GET['hostid'];
    $zabbixHostGroupId = (string) $_GET['groupid'];
    $zabbixHostGroupName = (string) urldecode($_GET['groupname']);
	if ($zabbixHostId > 0) {
		$host 		= $zabbix->getHostById($zabbixHostId);

		// Graphs
		$graphs 	= $zabbix->getGraphsByHostId($zabbixHostId);
		$graphs		= $zabbix->sortGraphsByName($graphs);

		// Triggers
		$triggers	= $zabbix->getTriggersByHostId($zabbixHostId);
?>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="hostgroups.php">Hostgroups</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="hosts.php?hostgroupid=<?php echo $zabbixHostGroupId; ?>">
                    <?php echo $zabbixHostGroupName; ?>
                </a> <span class="divider">/</span>
            </li>
            <li class="active">
                <?php echo $host->host; ?>
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <h2><?php echo $host->host?></h2>

    <h3>Host details</h3>
    <p>
        Host: <?php echo $host->host?> <br />
        <?php
            if ($zabbix->getVersion() == '1.4') {
                /* Zabbix 2.x compatible */
                echo "Name: ". $host->name ."<br />";

                echo "<h3>Interfaces</h3>";
                $interfaces = (array) $host->interfaces;
                if (is_array($interfaces) && count($interfaces) > 0) {
                    foreach ($host->interfaces as $interfaceId => $interfaceValue) {
                        echo "DNS: ". $interfaceValue->dns ."<br />";
                        echo "IP: ". $interfaceValue->ip ."<br />";
                        echo "Zabbix Agent Port: ". $interfaceValue->port ."<br /><br />";
                    }
                } else {
                    echo '<div class="alert alert-info">This host does not have any configured interfaces.</div>';
                }
            } else {
                /* Zabbix 1.8 compatible */
        ?>
                DNS: <?php echo @isset($host->dns) ? $host->dns : '' ?> <br />
                IP: <?php echo @isset($host->ip) ? $host->ip : '' ?> <br />
        <?php
            }
        ?>
    </p>

    <?php
        if (is_array($graphs) && count($graphs) > 0) {
    ?>
		<h3>Graphs</h3>
        <ul class="nav nav-pills nav-stacked">
		<?php
			foreach ($graphs as $graph) {
                ?>
				<li>
                    <a href="graph.php?graphid=<?php echo $graph["graphid"]?>&hostid=<?php echo $zabbixHostId; ?>&groupid=<?php echo $zabbixHostGroupId; ?>&groupname=<?php echo urlencode($zabbixHostGroupName); ?>&hostname=<?php echo $host->host; ?>&period=3600">
                        <i class="icon-signal"></i> <?php echo $graph["name"]; ?>
                    </a>
                </li>
                <?php
			}
		?>
		</ul>
		<?php
			} else {
                ?>
                <div class="alert alert-info">
                    <p>This host does not have any available graphs to display. As soon as you add a graph in Zabbix, it will show up here.</p>
                </div>
                <?php
            }
		?>
	</div>
<?php
	} else {
		?>
        <div class="alert alert-error">
            Invalid hostgroup specified.
        </div>
        <?php
	}
?>
