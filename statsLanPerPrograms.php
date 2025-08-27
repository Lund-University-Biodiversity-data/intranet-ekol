<?php
$database="SFT";
require "lib/config.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN

require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

$debug=false;

$consoleTxt="";
$server=DEFAULT_SERVER;
$protocol="kust";
$inputGeoObject="lan";
$inputNbYears=5;

if (isset($_POST["formStatsLanPrograms"]) && $_POST["formStatsLanPrograms"]=="OK") {

	$timeStart=time();

	$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	$inputGeoObject = (isset($_POST["inputGeoObject"]) ? $_POST["inputGeoObject"] : "");
	$inputNbYears = (isset($_POST["inputNbYears"]) ? $_POST["inputNbYears"] : "");

	$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
	if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
	else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");


	require "process/stats_lan_per_programs.php";
	

	$consoleTxt.=consoleMessage("info", "End check");

} // FIN IF $_POST["formNattSite"] OK


include ("views/header.html");

include ("views/stats_lan_per_programs.php");

?>

