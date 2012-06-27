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

    // Get all active triggers (for the counter on homepage);
    $triggersActive 	= $zabbix->getTriggersActive($arrSettings["minimalSeverity"]);
    if (!is_array($triggersActive))
        $triggersActive = array();
?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">
                Active Triggers
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <?php
    if (count($triggersActive) > 0) {
        // First, group our active triggers per host
        $arrSortedTriggers = array();

        foreach ($triggersActive as $triggerActive) {
            if (array_key_exists("hosts", $triggerActive) && array_key_exists(0, $triggerActive["hosts"])) {
                $arrSortedTriggers[$triggerActive["hosts"][0]->hostid]["host"] = $triggerActive["hosts"][0]->host;
                $arrSortedTriggers[$triggerActive["hosts"][0]->hostid]["triggers"][] =
                    array(
                        "description"	=> $triggerActive["description"],
                        "comments"		=> $triggerActive["comments"],
                        "lastchange"	=> $triggerActive["lastchange"],
                        "priority"		=> $triggerActive["priority"],
                        "triggerid"     => $triggerActive["triggerid"],
                    );
            }
        }

        asort($arrSortedTriggers);
        ?>
        <ul class="nav nav-list">
            <?php
            foreach ($arrSortedTriggers as $hostid => $arrTriggers) {
                ?>
                <li class="nav-header">
                    <a href="host.php?hostid=<?php echo $hostid?>">
                        <?php echo $arrTriggers["host"]?>
                    </a>
                </li>

                <?php
                foreach ($arrTriggers["triggers"] as $arrTrigger) {
                    $trigger_description = cleanTriggerDescription($arrTrigger["description"]);
                    ?>
                    <li class="severity_<?php echo $arrTrigger["priority"]?>">
                        <a href="trigger_info.php?triggerid=<?php echo $arrTrigger["triggerid"]?>&hostid=<?php echo $hostid?>">
                            <i class="icon-arrow-right"></i> <?php echo $trigger_description?>
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
        <div class="alert alert-info">
            <p>
                There don't seem to be any active triggers.
            </p>
        </div>
        <?php
    }
    ?>

</div>

<?php
    require_once("template/footer.php");
?>