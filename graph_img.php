<?php
	if (ereg('demo', $_SERVER['SERVER_NAME']))
		require_once("config-demo.php");
	else
		require_once("config-live.php");
	$fileid = (int) $_GET['fileid'];
	
	// Set correct header
	header("Content-Type: image/jpg");
	
	// Read the file & output
	$file_graph = $arrSettings["pathCookiesStorage"] ."zabbix_graph_". $fileid .".jpg";
	$strGraphImg = file_get_contents($file_graph);
	echo $strGraphImg;
	
	unlink($file_graph);
?>