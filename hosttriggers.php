<?php
	$triggers = $zabbix->getTriggersByHostId ($host["hostid"]);
	if (is_array($triggers) && count($triggers) > 0) {
		// Host has triggers
		foreach ($triggers as $trigger) {
			 $trigger_string = "- ". $trigger["description"] .": ". $trigger["value"] ."<br />";;
		}
	} else {
		$trigger_string = "none defined";
	}
?>