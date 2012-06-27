<?php
    require_once("config.inc.php");
    require_once("functions.php");
    require_once("class_zabbix.php");

    /* Include the basic header/css/... */
    require_once("template/header.php");

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
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="index.php">MoZBX - Mobile Monitoring</a>
        </div>
    </div>
</div>

<div class="container">
    <h2>Monitoring</h2>
    <p>
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="hostgroups.php">Your hosts</a><br />
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="activetriggers.php">Active Triggers</a><br />
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="<?php echo $arrSettings["urlApplication"]?>">Refresh page</a> <br />
    </p>

    <h2>Options</h2>
    <p>
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="logout.php">Logout</a><br />
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="feedback.php">Send Feedback</a><br />
        <a class="btn btn-large" style='padding: 15px; width:250px; margin-top: 7px;' href="about.php">About version 0.4</a><br />
    </p>
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
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="#">MoZBX</a>
    </div>
  </div>
</div>

<div class="container">
    <h2>Login</h2>
    <form method="post" action="index.php" class="form-horizontal" >
        <fieldset>
        <?php
            if (isset($_POST['zabbixUsername'])) {
                // POST variables set, but still showing the form. Invalid credentials?
                if (count($zabbix->getLastError()) > 0) {
                    $arrError = $zabbix->getLastError();
                    $errormsg = $arrError["data"];
                } else {
                    $errormsg = "invalid combination";
                }
            ?>
            <div class="alert alert-error">
                <?php echo $errormsg; ?>
            </div>
            <?php
            }
            if ($arrSettings["isHosted"] && !isset($_GET['hideapi'])) {
                // Hosted version, show input field for the JSON API
            ?>
            <div class="control-group">
                <label class="control-label" for="zabbixApi">API URL</label>
                <div class="controls">
                    <input type="text" id="zabbixApi" name="zabbixApi" value="<?php echo isset($_POST['zabbixApi']) ? $_POST['zabbixApi'] : $zabbixApi ?>" class="input-xlarge" />
                    <p class="help-block">The URL of your public Zabbix web interface.</p>
                </div>
            </div>
            <?php
            } else {
                // Fixed setup. Enter JSON API as hidden field
            ?>
            <input type="hidden" name="zabbixApi" value="<?php echo isset($_POST['zabbixApi']) ? $_POST['zabbixApi'] : $zabbixApi ?>" />
            <?php
            }
            ?>

            <div class="control-group">
                <label class="control-label" for="zabbixUsername">Username</label>
                <div class="controls">
                    <input type="text" name="zabbixUsername" id="zabbixUsername" value="<?php echo isset($_POST['zabbixUsername']) ? $_POST['zabbixUsername'] : $zabbixUser?>" class="input-xlarge" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="zabbixPassword">Password</label>
                <div class="controls">
                    <input type="password" name="zabbixPassword" id="zabbixPassword" value="<?php echo isset($_POST['zabbixPassword']) ? $_POST['zabbixPassword'] : $zabbixPass?>" class="input-xlarge" />
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="mZabbixLogin" class="btn btn-primary">Login</button>
            </div>
        </fieldset>
    </form>
<?php
    }
?>
</div>
<?php
	require_once("template/footer.php");
?>
