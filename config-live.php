<?php
	$arrSettings	= array();
	
	/* #########################################################################
	##
	##
	##		CORE CONFIG - BE CAREFUL
	##
	##
	###########################################################################*/
	
	/* If set to true, will show a textfield to input the URL */
	$arrSettings["isHosted"]				= true;
	
	/* Should we prompt for username & password, or just accept what's in this config and login automatically? */
	/* Note: if you set this to false, everyone visiting this URL will have access !! */
	$arrSettings["promptCredentials"]		= true;		
	
	/* Your Zabbix's server's hostname: used in the Zabbix Cookie Forging for retrieving graphs */
	/* This needs to match your server's hostname. Ie: http://zabbix.lab.mojah.be/zabbix/index.php */
	/* Would mean your zabbixHostname is 'zabbix.lab.mojah.be' (the FQDN) */
	$arrSettings["zabbixHostname"]			= "dev.mozbx.net";
	
	/* This is mostly only useful during debugging (less typing) */
	$arrSettings["zabbixUsername"]			= "demo";
	
	/* Again, useful for debugging. You don't want this in production. */
	$arrSettings["zabbixPassword"]			= "demo";
	
	/* Where is the 'api_jsonrpc.php', 'chart2.php' and 'index.php' file of Zabbix located?? */
	$arrSettings["zabbixApiUrl"]			= "http://www.mozbx.net/zabbix/";
	
	/* Debug JSON requests: echo all sent & received data */
	$arrSettings["jsonDebug"]				= false;
    $arrSettings["jsonDebug_path"]          = "/var/www/vhosts/mozbx.net/subdomains/dev/httpdocs/logs/";
	
	/* How long should our cookies be valid? */
	$arrSettings["cookieExpire"]			= time() + 60 * 60 * 7;
	
	/* #########################################################################
	##
	##
	##		PATHS & DIRECTORIES
	##
	##
	###########################################################################*/
	
	/* Where should we store our tempory cookie files? */
	$arrSettings["pathCookiesStorage"]		= "/tmp/";
	
	/* What URL are we on? */
	$arrSettings["urlApplication"]			= "http://dev.mozbx.net/MoZBX/";
	
	/* #########################################################################
	##
	##
	##		PRESENTATION - SHOWING THE DATA
	##
	##
	###########################################################################*/
	
	/* Mobile Zabbix version number */
	$arrSettings["mZabbixVersion"]			= "0.2";
	
	/* What should we name our little app? */
	$arrSettings["mZabbixName"]				= "Mobile ZBX";
	
	/* Show empty hostgroups ? */
	$arrSettings["showEmptyHostgroups"]		= false;
	
	/* Minimum severity for triggers to be shown? */
	/* This can help keep pages quick, if you can ignore ie. all information/not classified triggers you don't want */
	/* 0: not classified, 1: information, 2: warning, 3: average, 4: high, 5: disaster */
	/* If you put it on 0, you'll show all triggers. If on 2, you'll only show warnings or higher */
	$arrSettings["minimalSeverity"]			= 2;	// >=
	
	/* Styling for the textfields? */
	//$arrSettings["cssStyleTextfield"]		= "border: 1px solid gray; padding: 3px 0px 3px 0px; color: white; ";	// For JQT theme
	$arrSettings["cssStyleTextfield"]		= "border: 1px solid gray; padding: 3px 0px 3px 0px; color: black; ";	// For APPLE theme
	
	/* Styling for the (submit) buttons? */
	$arrSettings["cssStyleButton"]			= "border-width: 0 12px; display: block; padding 10px; text-align: center; font-size: 20px;";
	
	/* What theme should we use for the app? Options: jqt || apple */
	$arrSettings["appTheme"]				= "apple";
	
	/* What color should our statusbar be? This should match our theme (jqt = black, apple = white) */
	$arrSettings["appStatusbarColor"]		= "white";
	
	/* Google Analytics */
	$arrSettings["googleAnalytics"]			="<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-18606758-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";
?>
