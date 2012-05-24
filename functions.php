<?php
	function arrSortFunctionHostgroupsName ($arrElement1, $arrElement2) {
		return strcmp($arrElement1["name"], $arrElement2["name"]);
	}
	
	function arrSortFunctionHostsName ($arrElement1, $arrElement2) {
		return strcmp($arrElement1["host"], $arrElement2["host"]);
	}
	
	function getVisitorIP () {
		if (isset($_SERVER['HTTP_X_FORWARD_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
    
    function cleanTriggerDescription ($description) {
        $trigger_description = str_replace("{HOSTNAME}", "", $description);

        // If what remains is shown as ": trigger", delete the first char
        $arrReplaceChars = array(":", "-", " ");
        $char_cut = 0;
        for ($c = 0; $c < strlen($trigger_description); $c++) {
            if (!in_array($trigger_description[$c], $arrReplaceChars)) {
                $char_cut = $c;
                $c = strlen($trigger_description) + 1;		// Exit the loop
            }
        }
        
        $trigger_description = substr($trigger_description, $char_cut, strlen($trigger_description));  
        
        return $trigger_description;
    }
    
    function convertEventClock ($clock) {
        return date("d/m, H:i:s", $clock);
    }
    
    function convertEventValue ($value) {
        switch ($value) {
            case "1":
                return "Problem";
                break;
            case "0":
                return "OK";
                break;
            case "2":
                return "Acknowledged";
                break;
        }
    }
?>