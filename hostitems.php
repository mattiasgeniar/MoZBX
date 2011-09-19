<?php	
	// First list: short version, all hosts listed
	if (is_array($arrZabbixItems["hostgroups"]) && count($arrZabbixItems["hostgroups"]) > 0) {
		foreach ($arrZabbixItems["hostgroups"] as $hostgroupid => $hostgroup) {
			echo "<ul id=\"hostgroup_". $hostgroupid ."\" title=\"". $hostgroup["group"]["name"] ."\">";
			if (key_exists("hosts", $hostgroup) && is_array($hostgroup["hosts"]) && count($hostgroup["hosts"]) > 0) {
				$hosts = $hostgroup["hosts"];
				
				foreach ($hosts as $hostid => $host) {
					echo "<li><a href=\"#host_detail_". $hostid ."\">". $host["host"]["host"] ."</a></li>";
				}				
			}
			echo "</ul>";
		}
	}
	
	// Second list: detail view of each host
	if (is_array($arrZabbixItems["hostgroups"]) && count($arrZabbixItems["hostgroups"]) > 0) {
		foreach ($arrZabbixItems["hostgroups"] as $hostgroupid => $hostgroup) {
			if (key_exists("hosts", $hostgroup) && is_array($hostgroup["hosts"]) && count($hostgroup["hosts"]) > 0) {
				$hosts = $hostgroup["hosts"];
				
				foreach ($hosts as $hostid => $host) {
					$host_object 	= $host["host"];
					$trigger 		= key_exists("triggers", $host) && is_array($host["triggers"]) ? $host["triggers"] : array();;
					
					//print_r($host_object);
					
					// Start our detailed list
					echo "<ul id=\"host_detail_". $hostid ."\" title=\"". $host_object["host"] ."\">";
					echo "<li>General info</li>";
					
					// Give a small overview of this host
					echo "<ul id=\"host_detail_". $hostid ."_summary\">";
					echo "	<li>DNS: ". $host_object["dns"] ."</li>";
					echo "	<li>IP: ". $host_object["ip"] ."</li>";
					echo "	<li>Availability: ". $zabbix->getAvailability($host_object["available"]) ."</li>";
					if ($host_object["available"] != 1)
						echo "<li>Error: ". $host_object["error"] ."</li>";
					echo "</ul>";
					
					// Show possible graphs
					echo "<li><a href=\"host_detail_". $hostid ."_graphs\">Graphs</a></li>";
					if (key_exists("graphs", $host) && is_array($host["graphs"]) && count($host["graphs"]) > 0) {						
						
						echo "<ul id=\"host_detail_". $hostid ."_graphs\">";
						foreach ($host["graphs"] as $graphid => $graph) {
							echo "	<li>". $graph["name"] ."</li>";
						}
						echo "</ul>";
					} else {
						echo "<li>No graphs available</li>";
					}
					
					echo "</ul>";
				}				
			}
		}
	}
?>