<?php
	if (key_exists("hosts", $arrZabbixItems["hostgroups"][$group["groupid"]]))
		$hosts = $arrZabbixItems["hostgroups"][$group["groupid"]]["hosts"];
	else
		$hosts = false;
			
	echo "<ul id=\"hostgroup_triggered_overview". $group["groupid"] ."\">";
	if (is_array($hosts) && count($hosts) > 0) {
		// First loop: for the short "overview" of triggers
		foreach ($hosts as $hostid => $host) {
			// Get all triggers for this host
			$triggers = $host["triggers"];
			
			if (is_array($triggers) && count($triggers) > 0) {
				// Host has triggers: count & group them by severity
				$arrTriggerSeverity = array(0 => 0, 
											1 => 0, 
											2 => 0, 
											3 => 0, 
											4 => 0, 
											5 => 0
										);
										
				foreach ($triggers as $trigger) {
					$arrTriggerSeverity[$trigger["priority"]]++;
				}
				
				// Now show this host as it has some triggers highlighted
				echo "<li>". $host["host"]["host"] ." ". $zabbix->shortTriggerDisplay($arrTriggerSeverity) ."</li>";
			} else {
				// Host doesn't have active triggers
			}
		}
	} else {
		echo "<li>No hosts in this group.</li>";
	}
	echo "</ul>";
?>