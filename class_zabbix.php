<?php
	/* ##################################################################
	##
	##
	##	Originally developed by Mattias Geniar
	##	for the Mobile ZBX project: www.MoZBX.net
	##
	##	Github: http://github.com/mattiasgeniar/MoZBX
	##
	##
	####################################################################*/


	class Zabbix {
		/* ##########################################################
		##
		##
		##      CONFIG: core settings
		##
		##
		############################################################*/

		private $zabbix_url				= ""; #"http://zabbix.lab.mojah.be/api_jsonrpc.php";
		private	$zabbix_username		= "";
		private $zabbix_password		= "";
		private $zabbix_title			= false;
		private	$zabbix_hostname		= "";

		private $zabbix_json_headers	= array('Content-Type: application/json-rpc',
												'User-Agent: ZabbixAPI Poller');
		private $zabbix_curl_options	= array(CURLOPT_RETURNTRANSFER => true,
												CURLOPT_VERBOSE => true,
												/*CURLOPT_HEADER => true, */
												CURLOPT_TIMEOUT => 30,
												CURLOPT_CONNECTTIMEOUT => 5,
												CURLOPT_SSL_VERIFYHOST => false,
												CURLOPT_SSL_VERIFYPEER => false,
												CURLOPT_FOLLOWLOCATION => true,
												CURLOPT_FRESH_CONNECT => true
										);
		private $json_debug				= null;
		private $zabbix_tmp_cookies		= "";
		private $zabbix_url_graph		= "";
		private	$zabbix_url_index		= "";















		/* ##########################################################
		##
		##
		##      VARIABLES: generic
		##
		##
		############################################################*/
		
		private $auth_token			= false;
		private $last_error_message	= "";
		private $last_error_data	= "";
		private $last_error_code	= "";









		
		
		
		
		
		
		
		/* ##########################################################
		##
		##
		##      Construct
		##
		##
		############################################################*/

		public function __construct($arrSettings) {
			$this->zabbix_url			= $arrSettings["zabbixApiUrl"];
			$this->zabbix_hostname		= $arrSettings["zabbixHostname"];
			$this->zabbix_username		= $arrSettings["zabbixUsername"];
			$this->zabbix_password		= $arrSettings["zabbixPassword"];
			$this->zabbix_hostname		= $arrSettings["zabbixHostname"];
			$this->zabbix_tmp_cookies 	= $arrSettings["pathCookiesStorage"];
			$this->zabbix_url_graph		= $arrSettings["zabbixApiUrl"] ."chart2.php";
			$this->zabbix_url_index		= $arrSettings["zabbixApiUrl"] ."index.php";
			$this->json_debug			= $arrSettings["jsonDebug"];
		}

		
		
		
		
		
		
		
		
		
		
		
		
		





		/* ##########################################################
		##
		##
		##      METHODS: public interface for accessing data
		##
		##
		############################################################*/

		public function getAuthToken () {
			return $this->auth_token;
		}

		public function setAuthToken ($data) {
			$this->auth_token = $data;
		}
		
		public function getZabbixApiUrl () {
			return $this->zabbix_url;
		}

		public function setZabbixApiUrl ($data) {
			$this->zabbix_url = $data;
		}
		
		public function getUsername () {
			return $this->zabbix_username;
		}

		public function setUsername ($data) {
			$this->zabbix_username = $data;
		}
		
		public function getPassword () {
			return $this->zabbix_password;
		}

		public function setPassword ($data) {
			$this->zabbix_password = $data;
		}
		
		public function setLastError ($code, $message, $data) {
			$this->last_error_code		= $code;
			$this->last_error_data 		= $data;
			$this->last_error_message 	= $message;
		}
		
		public function getLastError () {
			return array(	"code" 		=> $this->last_error_code,
							"data"		=> $this->last_error_data,
							"message"	=> $this->last_error_message,
						);
		}

		public function Login () {
			$result 		= $this->sendRequest("user.authenticate", array("user" => $this->getUsername(), "password" => $this->getPassword()));
		
			//$result				= $this->decodeJson($json_login);
			if (isset($result->result))
				$this->auth_token	= $result->result;
		}

		public function isLoggedIn() {
			return (bool) $this->auth_token;
		}

		public function getVersion() {
			// Retrieve Zabbix Version
			$result 	= $this->sendRequest("apiinfo.version");
			
			if (isset($result->result))
				return $result->result;
		}

		public function getHostgroups() {
			// Retrieve all hostgroups for which you have access
			$result	= $this->sendRequest("hostgroup.get", array("extendoutput" => 1));
			//$result			= $this->decodeJson($json_hostgroups);
			
			if (isset($result->result)) {
				$group_objects		= $result->result;
				if (is_array($group_objects) && count($group_objects) > 0) {
					$arrGroups = array();
					foreach ($group_objects as $object) {
						$arrGroups[] = array(	"groupid" 	=> $object->groupid,
												"name"		=> $object->name,
												"internal"	=> $object->internal
										);
					}

					return $arrGroups;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		public function getHostgroupById ($hostgroupid) {
			$result	= $this->sendRequest("hostgroup.get", 
										array(	"output" => "extend",
												"groupids" => array($hostgroupid)
										)
									);
			if (isset($result->result)) {
				$hostgroup_objects	= $result->result;
			
				if (array_key_exists(0, $hostgroup_objects))
					return $hostgroup_objects[0];
				else
					return false;
			} else {
				return false;
			}
		}

		public function getHostsByGroupId ($groupid) {
			$result		= $this->sendRequest("host.get", 
										array(	"output" => "extend",
												"groupids" => array($groupid)
										)
									);
			
			if (isset($result->result)) {
				$host_objects	= $result->result;

				if (is_array($host_objects) && count($host_objects) > 0) {
					$arrHosts = array();
					foreach ($host_objects as $object) {					
						$arrHosts[$object->hostid] = 
										array(	"hostid"	=> $object->hostid,
												"host"		=> $object->host,
												"dns"		=> $object->dns,
												"ip"		=> $object->ip,
												"useip"		=> $object->useip,
												"status"	=> $object->status,  /* Enabled or not */
												"available"	=> $object->available,
												"disable_until" => $object->disable_until,
												"error"		=> $object->error,
												
											);
					}
					return $arrHosts;			
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		public function getHostById ($hostid) {
			$result	= $this->sendRequest("host.get", 
								array(	"output" => "extend",
										"hostids" => array($hostid)
									)
							);
							
			if (isset($result->result)) {
				$host_object	= $result->result;
			
				if (array_key_exists(0, $host_object))
					return $host_object[0];
				else
					return false;
			}
		}

		public function getTriggersByHostId ($hostid) {
			$result	= $this->sendRequest("trigger.get", 
										array( 	"hostids" => array($hostid),
												"output" => "extend",
												/*"only_true" => 1,*/
												/*"monitored" => 1*/
										)
									);
									
			if (isset($result->result)) {
				$trigger_objects= $result->result;

				if (is_array($trigger_objects) && count($trigger_objects) > 0) {
					$arrTriggers = array();
					foreach ($trigger_objects as $object) {
						$arrTriggers[$object->triggerid] = $this->convertTriggerJson($object);									
					}
					return $arrTriggers;			
				} else {
					return false;
				}
			}
		}
		
		public function getTriggersActive ($minimalSeverity) {
			$result	= $this->sendRequest("trigger.get", 
										array( 	"monitored" 	=> 1,
												"output" 		=> "extend",
												"only_true"		=> 1,
												"select_hosts" 	=> "extend",
												"min_severity"	=> $minimalSeverity,
												"filter" => array("value" => 1),
										)
									);
			
			if (isset($result->result)) {
				$trigger_objects= $result->result;
				
				if (is_array($trigger_objects) && count($trigger_objects) > 0) {
					$arrTriggers = array();
					foreach ($trigger_objects as $object) {
						$arrTriggers[$object->triggerid] = $this->convertTriggerJson($object);									
					}
					return $arrTriggers;			
				} else {
					return false;
				}
			}
		}
		
		private function convertTriggerJson ($object) {
			$arrTrigger =	array(	"triggerid" 	=> $object->triggerid,
									"expression"	=> $object->expression,
									"description"	=> $object->description,
									"url"			=> $object->url,
									"status"		=> $object->status,
									"value"			=> $object->value,
									"priority"		=> $object->priority,
									"lastchange"	=> $object->lastchange,
									"dep_level"		=> $object->dep_level,
									"comments"		=> $object->comments,
									"error"			=> $object->error,
									"templateid"	=> $object->templateid,
									"type"			=> $object->type,									
								);
			if (isset($object->hosts))
				$arrTrigger["hosts"] = $object->hosts;
				
			return $arrTrigger;
		}
		
		public function getGraphsByHostId ($hostid) {
			$result	= $this->sendRequest("graph.get", 
										array( 	"hostids" => array($hostid),
												"output" => "extend",
												/*"only_true" => 1,*/
												/*"monitored" => 1*/
										)
									);
			if (isset($result->result)) { 
				$graph_objects	= $result->result;
				
				if (is_array($graph_objects) && count($graph_objects) > 0) {
					$arrGraphs = array();
					foreach ($graph_objects as $object) {
						$arrGraphs[$object->graphid] = 
										array(	"graphid" 	=> $object->graphid,
												"name"		=> $object->name,
												"width"		=> $object->width,
												"height"	=> $object->height,
												"yaxismin"	=> $object->yaxismin,
												"yaxismax"	=> $object->yaxismax,
												"graphtype"	=> $object->graphtype,
												"show_legend" => $object->show_legend,
												"show_3d"	=> $object->show_3d,											
										);									
					}
					return $arrGraphs;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		public function getGraphById ($graphid) {
			$result	= $this->sendRequest("graph.get", 
								array(	"output" => "extend",
										"graphids" => array($graphid)
									)
							);
							
			if (isset($result->result)) {
				$graph_object	= $result->result;
			
				if (array_key_exists(0, $graph_object))
					return $graph_object[0];
				else
					return false;
			} else {
				return false;
			}
		
		}
		
		public function getGraphImageById ($graphid) {
			// First: we forge our cookies Zabbix would normally create.
			//$strCookie 			= $this->zabbix_hostname ."     FALSE   /       FALSE   0       zbx_sessionid   ". $this->auth_token;
			
			// Cookiename
			$filename_cookie 	= $this->zabbix_tmp_cookies . "zabbix_cookie_". $graphid .".txt";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,  $this->zabbix_url_index);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$post_data = array(
				'name' => $this->getUsername(),
				'password' => $this->getPassword(),
				'enter' => 'Enter'
				);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $filename_cookie);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $filename_cookie);
			
			// Login
			curl_exec($ch);
			
			// Fetch image
			curl_setopt($ch, CURLOPT_URL, $this->zabbix_url_graph ."?graphid=". $graphid ."&width=450");
			$output = curl_exec($ch);
			
			// Close session
			curl_close($ch);
			
			// Delete our cookie 
			unlink($filename_cookie);
			
			// Return the image
			return $output;
		}







		
		/* ##########################################################
		##
		##
		##	"Data conversion", make Zabbix Output readable
		##
		##
		############################################################*/

		public function getTriggerSeverity($priority) {
			switch ($priority) {
				case 5:
					return "Disaster";
					break;
				case 4:
					return "High";
					break;
				case 3:
					return "Average";
					break;
				case 2:
					return "Warning";
					break;
				case 1:
					return "Information";
					break;
				default:
					return "Not classified";
			}
		}
		
		public function getAvailability ($available) {
			switch ($available) {
				case 2:
					return "Zabbix Agent offline";
					break;
				case 1:
					return "Zabbix Agent online";
					break;
				case 0:
					return "Zabbix Agent not needed";
					break;
				default:
					return "unknown";
					break;
			}
		}
		
		public function shortTriggerDisplay ($arrSeverity) {
			$arrOutput = array();
			asort($arrSeverity);	
			
			foreach ($arrSeverity as $priority => $count) {
				if ($count > 0) {
					// This is worth mentioning
					$arrOutput[] = "<b>". $count ."</b> ". $this->getTriggerSeverity($priority);
				}
			}
			
			if (count($arrOutput) > 0)
				return ": ". implode(", ", $arrOutput);
			else 
				return false;
		}
		
		public function sortHostgroupsByName ($arrHostgroups) {
			if (is_array($arrHostgroups))
				uasort($arrHostgroups, "arrSortFunctionHostgroupsName");
			
			return $arrHostgroups;
		}
		
		public function sortHostsByName ($arrHosts) {
			if (is_array($arrHosts))
                            uasort($arrHosts, "arrSortFunctionHostsName");
			return $arrHosts;
		}
		
		public function sortGraphsByName ($arrGraphs) {
			if (is_array($arrGraphs))
                            uasort($arrGraphs, "arrSortFunctionHostgroupsName");
			return $arrGraphs;
		}
		
		public function filterActiveHosts($arrHosts) {
			// Input: array of hosts
			// Output: only the active hosts
			$arrActiveHosts = array();
			
			if (is_array($arrHosts) && count($arrHosts) > 0) {
				foreach ($arrHosts as $host) {
					if (array_key_exists("status", $host) && $host["status"] == 0)
						$arrActiveHosts[] = $host;
				}
				
				return $arrActiveHosts;
			} else
				return false;
		}
		

		
		
		
		
		
		
		
		
		
		
		
		




		/* ##########################################################
		##
		##
		##	The "core" functions
		##	- Send JSON requests to the API
		##	- Retrieving & Parsing JSON requests
		##
		##
		############################################################*/

		public function sendRequest($action, $parameters = '') {
			$curl_init 	= curl_init($this->zabbix_url);
			
			// Get our "config" variables
			$curl_opts 	= $this->zabbix_curl_options;
			$json_headers 	= $this->zabbix_json_headers;

			// Build our encoded JSON
			$json_data	= $this->genericJSONPost($action, $parameters);

			$curl_opts[CURLOPT_HTTPHEADER]		= $json_headers;
			$curl_opts[CURLOPT_CUSTOMREQUEST] 	= "POST";
			$curl_opts[CURLOPT_POSTFIELDS] 		= is_array($json_data) ? http_build_query($json_data) : $json_data;

			curl_setopt_array($curl_init, $curl_opts);
			$ret = curl_exec($curl_init);
			curl_close($curl_init);

			if ($this->json_debug) {
				echo "<h3>Json Answer</h3>";
				echo "<pre>";
				echo var_dump($ret, true);
				echo "</pre>";
			}
			
			// Make the output "readable"
			$result	= $this->decodeJson($ret);
			
			if (isset($result->error)) {
				$this->setLastError($result->error->code, $result->error->message, $result->error->data);
				return false;
			} else {
				return $result;
			}			
		}

		private function genericJSONPost($action, $parameters = '') {
			$json_request	= array(
				'auth' 		=> $this->auth_token,
				'method'	=> $action,
				'id'		=> 1,
				'params'	=> is_array($parameters) ? $parameters : array(),
				'jsonrpc'	=> '2.0'
			);

			if ($this->json_debug) {
				echo "<h3>Json Request</h3>";
				echo "<pre>";
				echo var_dump($json_request, true);
				echo "</pre>";
			}

			return json_encode($json_request);
		}

		private function decodeJson ($json) {
			$decoded = json_decode($json);
			return $decoded;
		}
	}
?>
