<?php
	require_once("config.inc.php");
	$fileid = (int) $_GET['fileid'];
	
	// Set correct header
	header("Content-Type: image/jpg");
	
	// Read the file & output
	$file_graph = $arrSettings["pathCookiesStorage"] ."zabbix_graph_". $fileid .".jpg";
	$strGraphImg = file_get_contents($file_graph);
	echo $strGraphImg;
	
	unlink($file_graph);
?>