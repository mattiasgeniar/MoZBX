<?php
    require_once("config.inc.php");
    require_once("functions.php");
    require_once("class_zabbix.php");

    /* Include the basic header/css/... */
    require_once("template/header.php");
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


    <h3>Changelog: version 0.4</h3>
    <p>
        Changes for Zabbix 2.0 compatibility introduced<br />
        Updated jqTouch, using Zepto instead of jqTouch<br />
        Performance: default to not showing host-counts per hostgroup<br />
    </p>

    <h3>Changelog: version 0.3</h3>
    <p>
        <h4>Features</h4>
        Added support for Trigger Acknowledgements<br />
        Modified layout for detail items (smaller)<br />
        Ability to change the time-period on graphs<br />
        <br />
        <h4>Bugfixes</h4>
        Feedback form should work again<br />
        Logout button should work again<br />
        Refresh page should work again<br />
    </p>

    <h3>Changelog: version 0.2</h3>
    <p>
        <h4>Features</h4>
        Listing active triggers<br />
        Retrieving host graphs<br />
        Browsing hostgroups &amp; hosts<br />
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
