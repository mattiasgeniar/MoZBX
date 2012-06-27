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

    require_once("template/header.php");

	$zabbixHostgroupId = (int) $_GET['hostgroupid'];
	if ($zabbixHostgroupId > 0) {
		$hostgroup 	= $zabbix->getHostgroupById($zabbixHostgroupId);
		$hosts 		= $zabbix->getHostsByGroupId ($zabbixHostgroupId);
		$hosts 		= $zabbix->filterActiveHosts($hosts);
		$hosts		= $zabbix->sortHostsByName($hosts);
	
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
            <li class="active">
                <?php echo $hostgroup->name; ?>
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <ul class="nav nav-pills nav-stacked">
		<?php
			foreach ($hosts as $host) {
                ?>
                <li>
                    <a href="host.php?hostid=<?php echo $host["hostid"]; ?>&groupid=<?php echo $zabbixHostgroupId; ?>&groupname=<?php echo urlencode($hostgroup->name); ?>">
                        <i class="icon-inbox"></i>  <?php echo $host["host"]; ?>
                   </a>
                </li>
                <?php
			}
		?>
	</ul>
<?php
	} else {
		?>
        <div class="alert alert-error">
            Invalid hostgroup, aborting request.
        </div>

        <?php
	}
?>

</div>

<?php
    require_once("template/footer.php");
?>