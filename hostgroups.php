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
?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">
                Hostgroups
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <?php
        $zabbixHostgroups = $zabbix->getHostgroups();
        $zabbixHostgroups = $zabbix->sortHostgroupsByName($zabbixHostgroups);

        if (is_array($zabbixHostgroups) && count($zabbixHostgroups) > 0) {
    ?>
            <ul class="nav nav-pills nav-stacked">
                <?php
                    foreach ($zabbixHostgroups as $groupobject) {
                        $linkHostgroup = "hosts.php?hostgroupid=". $groupobject["groupid"];

                        if ($arrSettings["countHostsPerGroup"] == true) {
                            $hosts = $zabbix->getHostsByGroupId ($groupobject["groupid"]);
                            $hosts = $zabbix->filterActiveHosts($hosts);
                            $hostCount = is_array($hosts) ? count($hosts) : 0;
                        } else {
                            // Assume this hostgroup has hosts in them
                            $hostCount = 1;
                        }

                        if ($arrSettings["showEmptyHostgroups"] || $hostCount > 0) {
                        ?>
                            <li>
                                <a href="<?php echo $linkHostgroup?>">
                                    <i class="icon-hdd"></i> <?php echo $groupobject["name"]; ?>
                                </a>
                            </li>
                        <?php
                        }
                    }
                ?>
            </ul>
    <?php
        } else {
        ?>
            <div class="alert alert-error">
                <h2>Access Denied</h2>
                <p>
                    Sorry, you don't seem have access to any hostgroups.<br />
                    <br />
                    Does the user with which you login, have <b>API Access</b> enabled in the Zabbix <b>User Administration</b> screen?
                </p>
             </div>
    <?php
        }
    ?>
</div>

<?php
    require_once("template/footer.php");
?>