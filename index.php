<?php
	require_once("config.inc.php");
	require_once("functions.php");
	require_once("class_zabbix.php");

	// Debug
	error_reporting(E_ALL);
	ini_set("display errors", 1);

	// Check for php curl module
	if (!function_exists("curl_init")) {
		echo "<h2>Missing PHP Curl module</h2>";
		exit();
	}

	// Main Zabbix object
	$zabbix = new Zabbix($arrSettings);

	// Login
	if (!$arrSettings["promptCredentials"]) {
		// Login using supplied credentials from the config.php file
		$zabbix->login();
	}else {
		// It's a hosted version, perhaps we can recover username & password from cookies?
		$zabbixApi 	= "";
		$zabbixUser	= "";
		$zabbixPass	= "";

		// Get the values
		require_once("cookies.php");

		// Populate our class
		$zabbix->setUsername($zabbixUser);
		$zabbix->setPassword($zabbixPass);
		$zabbix->setZabbixApiUrl($zabbixApi);

		// Login
		if (strlen($zabbix->getUsername()) > 0 && strlen($zabbix->getPassword()) > 0 && strlen($zabbix->getZabbixApiUrl()) > 0) {
			$zabbix->login();
		}
	}

	//$zabbix->Login("mattias_api", "test");


	if ($zabbix->isLoggedIn()) {
		// Authenticated, save cookie
		setcookie("zabbixUsername", $zabbix->getUsername(), $arrSettings["cookieExpire"]);
		setcookie("zabbixPassword", $zabbix->getPassword(), $arrSettings["cookieExpire"]);
		setcookie("zabbixApi", $zabbix->getZabbixApiUrl(), $arrSettings["cookieExpire"]);
		setcookie("zabbixAuthHash", $zabbix->getAuthToken(), $arrSettings["cookieExpire"]);
    }

	// "templates"
	require_once("template/header.php");

	if ($zabbix->isLoggedIn()) {

		// Retrieve the data in one go
		$zabbix_auth 		= $zabbix->getAuthToken();
		$zabbix_version		= $zabbix->getVersion();

		// Get all active triggers (for the counter on homepage);
		$triggersActive 	= $zabbix->getTriggersActive($arrSettings["minimalSeverity"]);
		if (!is_array($triggersActive))
			$triggersActive = array();
?>
		<div id="home" class="current">
			<div class="toolbar">
                <h1><?php echo $arrSettings["mZabbixName"]?></h1>
                <a class="button slideup" id="infoButton" href="#settings">Settings</a>
            </div>

			<ul class="rounded">
				<li><a href="#hostgroups"><img src="images/hostgroups.png" class="icon_list">Hosts Overview</a></li>
				<li><a href="#activetriggers"><img src="images/trigger.png" class="icon_list">Active Triggers</a> <small class="counter"><?php echo count($triggersActive)?></small></li>
				<li><a href="<?php echo $arrSettings["urlApplication"]?>" target="_webapp"><img src="images/refresh.png" class="icon_list">Refresh page</a></li>
			</ul>

			<ul class="rounded">
				<li><a href="<?php echo $arrSettings["urlApplication"]?>logout.php" target="_webapp"><img src="images/logout.png" class="icon_list">Logout</a></li>
				<li><a href="feedback.php" target="_webapp"><img src="images/feedback.png" class="icon_list">Send feedback</a></li>
				<li><a href="#about"><img src="images/about.png" class="icon_list">About version <?php echo $arrSettings["mZabbixVersion"]?></a></li>
			</ul>
		</div>

		<div id="settings" class="selectable">
			<div class="toolbar">
				<h1>Settings</h1>
				<a class="back" href="#">Home</a>
			</div>

        	This should, one day, feature some customizable settings.
    	</div>


		<div id="about" class="selectable">
			<div class="toolbar">
				<h1>About</h1>
				<a class="back" href="#">Home</a>
			</div>

            <ul class="rounded">
                <li class="title">Mobile Zabbix</li>
                <li class="small">
                    Mobile Zabbix version <b><?php echo $arrSettings["mZabbixVersion"]?></b><br />
                    In development by <b>Mattias Geniar</b>
                </li>

                <li class="title">Changelog: version 0.3</li>
                <li class="small">
                    <u>New features</u> <br />
                    Added support for Trigger Acknowledgements<br />
                    Modified layout for detail items (smaller)<br />
                    Ability to change the time-period on graphs<br />
                    <br />
                    <u>Bugfixes</u><br />
                    Feedback form should work again<br />
                    Logout button should work again<br />
                    Refresh page should work again<br />
                </li>
                <li class="title">Changelog: version 0.2</li>
                <li class="small">
                    <u>New features</u> <br />
                    Listing active triggers<br />
                    Retrieving host graphs<br />
                    Browsing hostgroups &amp; hosts<br />
                </li>

                <li class="title">Zabbix Server</li>
                <li class="small">
                    Zabbix API version <b><?php echo $zabbix_version?></b> on the server.<br />
                    Retrieved data from <b><?php echo $arrSettings["zabbixHostname"]?></b>.<br />
                    You are logged in as <b><?php echo $zabbix->getUsername()?></b>.<br />
                    Your current session-id is <b><?php echo $zabbix->getAuthToken()?></b><br />
                </li>
            </ul>
    	</div>

		<div id="hostgroups" class="selectable">
			<div class="toolbar">
				<h1>Hostgroups</h1>
				<a class="back" href="#">Home</a>
			</div>


			<ul class="rounded">
			<?php
				$zabbixHostgroups = $zabbix->getHostgroups();
				$zabbixHostgroups = $zabbix->sortHostgroupsByName($zabbixHostgroups);

				if (is_array($zabbixHostgroups) && count($zabbixHostgroups) > 0) {
					foreach ($zabbixHostgroups as $groupobject) {
						// Start list item
						$linkHostgroup = "hosts.php?hostgroupid=". $groupobject["groupid"];

						$hosts = $zabbix->getHostsByGroupId ($groupobject["groupid"]);
						$hosts = $zabbix->filterActiveHosts($hosts);
						$hostCount = is_array($hosts) ? count($hosts) : 0;

						if ($arrSettings["showEmptyHostgroups"] || $hostCount > 0) {
                            ?>
							<li>
                                <a href="<?php echo $linkHostgroup?>">
					<img src="images/hosts.png" class="icon_list"><?php echo $groupobject["name"]?>
                                </a>
                                <small class="counter"><?php echo $hostCount?></small>
                            </li>
                            <?php
                        }

					}
				} else {
					?>
                    Sorry, you don't seem have access to any hostgroups.<br />
                    <br />
                    Does the user with which you login, have <b>API Access</b> enabled in the Zabbix <b>User Administration</b> screen?
                    <?php
				}
			?>
			</ul>
		</div>

		<div id="activetriggers" class="selectable">
			<div class="toolbar">
				<h1>Triggers</h1>
				<a class="back" href="#">Home</a>
			</div>

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
				<ul class="rounded">
				<?php
					foreach ($arrSortedTriggers as $hostid => $arrTriggers) {
						?>
                        <li>
                            <a href="host.php?hostid=<?php echo $hostid?>">
                                <img src="images/host.png" class="icon_list"><?php echo $arrTriggers["host"]?>
                            </a>
                        </li>

                        <?php
						foreach ($arrTriggers["triggers"] as $arrTrigger) {
							$trigger_description = cleanTriggerDescription($arrTrigger["description"]);
							?>
							<li class="severity_<?php echo $arrTrigger["priority"]?>">
                                <a href="trigger_info.php?triggerid=<?php echo $arrTrigger["triggerid"]?>&hostid=<?php echo $hostid?>">
                                    <?php echo $trigger_description?>
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
                    <ul class="rounded">There don't seem to be any active triggers.</ul>
                    <?php
				}
			?>
    	</div>
<?php
	} else {
		// Show login screen

		$zabbixUser = isset($_GET['username']) && strlen($_GET['username']) > 0 ? $_GET['username'] : $zabbixUser;
		$zabbixApi = isset($_GET['api_url']) && strlen($_GET['api_url']) > 0 ? $_GET['api_url'] : $zabbixApi;
		if (strpos($zabbixApi, 'api_jsonrpc.php') === false)
			$zabbixApi .= "api_jsonrpc.php";
		$zabbixHideApi = isset($_GET['hideapi']) ? 'hideapi' : 'donthideapi';
		$zabbixPass = isset($_GET['password']) ? $_GET['password'] : $arrSettings["zabbixPassword"];
?>
	<div id="home_login" class="current">
		<div class="toolbar">
			<h1><?php echo $arrSettings["mZabbixName"]?></h1>
		</div>

		<ul class="rounded">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" class="form" >
				<?php
					if ($arrSettings["isHosted"] && !isset($_GET['hideapi'])) {
						// Hosted version, show input field for the JSON API
					?>
						<li>
							API URL<br />
							<input type='text' name='zabbixApi' value='<?php echo isset($_POST['zabbixApi']) ? $_POST['zabbixApi'] : $zabbixApi ?>' style="<?php echo $arrSettings["cssStyleTextfield"]?>" />
						</li>
					<?php
					} else {
						// Fixed setup. Enter JSON API as hidden field
					?>
					<input type='hidden' name='zabbixApi' value='<?php echo isset($_POST['zabbixApi']) ? $_POST['zabbixApi'] : $zabbixApi ?>' />
					<?php
					}
				?>
				<li>
					Please enter your Zabbix ID.
				</li>

				<li>
					User<br />
					<input type="text" name="zabbixUsername" value="<?php echo isset($_POST['zabbixUsername']) ? $_POST['zabbixUsername'] : $zabbixUser?>" style="<?php echo $arrSettings["cssStyleTextfield"]?>" />
				</li>

				<li>
					Password <br />
					<input type="password" name="zabbixPassword" value="<?php echo isset($_POST['zabbixPassword']) ? $_POST['zabbixPassword'] : $zabbixPass?>" style="<?php echo $arrSettings["cssStyleTextfield"]?>" />
				</li>
				<?php
					if (isset($_POST['zabbixUsername'])) {
						// POST variables set, but still showint the form. Invalid credentials?
						if (count($zabbix->getLastError()) > 0) {
							$arrError = $zabbix->getLastError();
							$errormsg = $arrError["data"];
							//print_r($arrError);
						} else
							$errormsg = "invalid combination";
				?>
				<li>
					<font color="red"><?php echo $errormsg?></font>
				</li>
				<?php
					}
				?>

				<li>
					<input type="submit" name="mZabbixLogin" value="Login" style="<?php echo $arrSettings["cssStyleButton"]?>" onclick="submit()" />
				</li>

			</form>
		</ul>
	</div>

	<?php
	}

	require_once("template/footer.php");
?>
