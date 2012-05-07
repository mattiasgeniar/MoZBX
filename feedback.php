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
if (strlen($zabbix->getUsername()) > 0 && strlen($zabbix->getPassword()) > 0 && strlen($zabbix->getZabbixApiUrl()) > 0) {
    $zabbix->login();
}

if (!$zabbix->isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$boolShowThankyou = false;
$boolShowFeedbackForm = true;
$boolShowTextlengthWarn = false;

if (isset($_POST['mZabbixFeedback'])) {
    // Process feedback
    $txtFeedback = htmlentities($_POST['txtFeedback']);
    if (strlen($txtFeedback) < 30) {
        // Don't bother, type more
        $boolShowTextlengthWarn = true;
        $boolShowThankyou = false;
        $boolShowFeedbackForm = true;
    } else {
        // Mail me
        $source_ip = getVisitorIP();
        $server_variables = $_SERVER;

        // I'll mail myself in HTML, thankyouverymuchkbye
        $mailHtml = array();
        $mailHtml[] = "Source: <b>" . $source_ip . "</b>";
        $mailHtml[] = "Date: <b>" . date("Y-m-d, H:i:s") . "</b>";
        $mailHtml[] = "Feedback: <br />";
        $mailHtml[] = "<b>" . $txtFeedback . "</b>";
        $mailHtml[] = "";
        $mailHtml[] = "<hr />";
        $mailHtml[] = "_SERVER variables";
        foreach ($server_variables as $argument => $value) {
            $mailHtml[] = "- " . $argument . ": " . $value;
        }
        $mailHtml = implode("<br />", $mailHtml);

        // Content-type for my HTML
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'To: Mattias Geniar <mattias.geniar@gmail.com>' . "\r\n";
        $headers .= 'From: mZabbix Feedback <fb@mzabbix.com>' . "\r\n";

        // Send it
        mail("mattias.geniar@gmail.com", "mZabbix Feedback from " . $source_ip, $mailHtml, $headers);

        // Show some output
        $boolShowThankyou = true;
        $boolShowFeedbackForm = false;
    }

} else {
    $boolShowFeedbackForm = true;
}

// "templates"
require_once("template/header.php");
?>

<div id="feedback">
    <div class="toolbar">
        <h1>Feedback</h1>
        <a class="button slideup" id="infoButton" href="<?php echo $arrSettings["urlApplication"]?>" target="_webapp">Back</a>
    </div>

    <?php
    if ($boolShowFeedbackForm) {
        ?>
        <ul class="rounded">
            <form method="post" action="feedback.php" class="form">
                <input type="hidden" name="mZabbixFeedback" value="Send"/>
                <li>
                    Comments on this app: <br/>
                    <textarea name="txtFeedback" cols="120"
                              style='margin-top: 4px; margin-bottom: 4px; padding: 4px; border: 1px solid gray; width: 270px; height:160px'><?php echo isset($_POST['txtFeedback']) ? $_POST['txtFeedback'] : '' ?></textarea><br/>
                    <?php
                    if ($boolShowTextlengthWarn) {
                        echo "<font color='red'>";
                    } else {
                        echo "<font color='gray'>";
                    }
                    ?>
                    <i>At least 30 characters long.</i>
                    </font>
                </li>

                <li>
                    <input type="submit" name="mZabbixFeedback" value="Send!" class="whiteButton" onclick="submit()"/>
                </li>
            </form>
        </ul>
        <?php
    }

    if ($boolShowThankyou) {
        ?>
        <ul class="rounded">
            <li>Thank you for your valuable feedback!</li>
            <li><a href="<?php echo $arrSettings["urlApplication"]?>" target="_webapp">Home</a></li>
        </ul>
        <?php
    }
    ?>
</div>
<?php
require_once("template/footer.php");
?>
