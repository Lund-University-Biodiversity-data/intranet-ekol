<?php


$db_connection = pg_connect("host=".$DB["host"]." dbname=".$DB["database"]." user=".$DB["username"]." password=".$DB["password"])  or die("CONNECT:" . consoleMessage("error", pg_result_error()));

$qKarta='SELECT DISTINCT kartatx FROM koordinater_mittpunkt_topokartan ORDER BY kartatx';
$rKarta = pg_query($db_connection, $qKarta);
$arrKartaTx=array();
while ($rtKarta = pg_fetch_array($rKarta)) {
	$arrKartaTx[]=$rtKarta["kartatx"];
}

$qLan='SELECT DISTINCT lan FROM standardrutter_oversikt ORDER BY lan';
$rLan = pg_query($db_connection, $qLan);
$arrLan=array();
while ($rtLan = pg_fetch_array($rLan)) {
	$arrLan[]=$rtLan["lan"];
}


?>