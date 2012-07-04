
<?php
    /* Only include the tracker on non-SSL connections, since I'm too cheap to
        actually buy a valid SSL cert
    */
    if (array_key_exists('HTTPS', $_SERVER) && strtolower($_SERVER['HTTPS']) == 'on') {
?>
<!-- Some basic usage tracking. Nice stats on browser agents etc. 
So I know on which browser(s) to focus most -->
<img src="http://www.mozbx.net/images/tracker.png" />
<?php
    }

    /* Show the Google Analytics URL, only if it's hosted */
    if ($arrSettings["isHosted"])
        echo $arrSettings['googleAnalytics'];
?>
</body>
</html>