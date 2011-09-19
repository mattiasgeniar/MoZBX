<?php
	require_once("config.inc.php");
	require_once("functions.php");
	require_once("class_zabbix.php");	
	
	$zabbix = new Zabbix($arrSettings);
	
	// Get values from cookies, if any
	require_once("cookies.php");
	
	// Populate our class
	$zabbix->setUsername($zabbixUser);
	$zabbix->setPassword($zabbixPass);
	$zabbix->setZabbixApiUrl($zabbixApi);
	
	// Login
	if (isset($zabbixAuthHash) && strlen($zabbixAuthHash) > 0) { 
		// Try it with the authentication hash we have
		$zabbix->setAuthToken($zabbixAuthHash);
	} elseif (strlen($zabbix->getUsername()) > 0 && strlen($zabbix->getPassword()) > 0 && strlen($zabbix->getZabbixApiUrl()) > 0) {
		// Or try it with our info from the cookies
		$zabbix->login();
	} 

	if (!$zabbix->isLoggedIn()) {
		header("Location: index.php");
		exit();
	}
	
	$zabbixHostgroupId = (int) $_GET['hostgroupid'];
	if ($zabbixHostgroupId > 0) {
		$hostgroup 	= $zabbix->getHostgroupById($zabbixHostgroupId);
		$hosts 		= $zabbix->getHostsByGroupId ($zabbixHostgroupId);
		$hosts 		= $zabbix->filterActiveHosts($hosts);
		$hosts		= $zabbix->sortHostsByName($hosts);
	
?>
	<div id="hosts_general_<?php echo $zabbixHostgroupId?>">
		<div class="toolbar">
			<h1><?php echo $hostgroup->name?></h1>
			<a class="back" href="#">Back</a>
		</div>
		
		<ul class="rounded">
		<?php
			foreach ($hosts as $host) {
				echo "<li><a href=\"host.php?hostid=". $host["hostid"] ."\"><img src=\"images/host.png\" class=\"icon_list\">". $host["host"] ."</a></li>";
			}
		?>
		</li>
	</div>
<?php
	} else {
		echo "Invalid hostgroup.";
	}
?>