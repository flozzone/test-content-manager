<?php
	error_reporting(E_ALL);
	require_once("config.php");
	$jstree = new json_tree();
	if($_REQUEST["operation"] && strpos($_REQUEST["operation"], "_") !== 0 && method_exists($jstree, $_REQUEST["operation"])) {
		header("HTTP/1.0 200 OK");
		header('Content-type: application/json; charset=utf-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		echo $jstree->{$_REQUEST["operation"]}($_REQUEST);
		
		die();
	}
	header("HTTP/1.0 404 Not Found"); 
?>
