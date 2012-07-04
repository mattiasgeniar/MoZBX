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

    if ($zabbix->isLoggedIn()) {
        // Retrieve the data in one go
        $zabbix_auth 		= $zabbix->getAuthToken();
        $zabbix_version		= $zabbix->getVersion();
    }
?>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a> <span class="divider">/</span>
            </li>
            <li class="active">
                About
            </li>
        </ul>
    </div>
</div>

<div class="container">
    <h3>Mobile Zabbix</h3>
    <p>
        Mobile Zabbix version <b><?php echo $arrSettings["mZabbixVersion"]?></b><br />
        In development by <b>Mattias Geniar</b>
    </p>

    <h3>Changelog: version 0.5</h3>
    <p>
        Improved Zabbix 2.0 compatibility<br />
        Introduced entirely new layout (based on Twitter Bootstrap)<br />
        Fix #33: SSL tracker image<br />
        Fix #32: Home button not working <br />
        Fix #31: Windows CRLF<br />
        Fix #30: API URL: detect trailing slashes<br />
        Fix #29: Automatically determine Zabbix Version (1.8/2.0)<br />
        Fix #28: Active Triggers empty on Zabbix 2.0
    </p>

    <h3>Changelog: version 0.4</h3>
    <p>
        Changes for Zabbix 2.0 compatibility introduced<br />
        Updated jqTouch, using Zepto instead of jqTouch<br />
        Performance: default to not showing host-counts per hostgroup<br />
    </p>

    <h3>Zabbix Server info</h3>
    <p>
        Zabbix API version <b><?php echo $zabbix_version?></b> on the server.<br />
        Retrieved data from <b><?php echo $arrSettings["zabbixHostname"]?></b>.<br />
        You are logged in as <b><?php echo $zabbix->getUsername()?></b>.<br />
        Your current session-id is <b><?php echo $zabbix->getAuthToken()?></b><br />
    </p>
</div>
<?php
    require_once("template/footer.php");
?>
