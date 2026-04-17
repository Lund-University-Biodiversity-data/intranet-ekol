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
$inputYearStart=2020;

if (isset($_POST["formStatsProgramOrigin"]) && $_POST["formStatsProgramOrigin"]=="OK") {

	$timeStart=time();

	//$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	//$/inputGeoObject = (isset($_POST["inputGeoObject"]) ? $_POST["inputGeoObject"] : "");
	//$inputNbYears = (isset($_POST["inputNbYears"]) ? $_POST["inputNbYears"] : "");
	$inputYearStart = (isset($_POST["inputYearStart"]) ? $_POST["inputYearStart"] : "");

	$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
	if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
	else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");


	require "process/stats_programs_per_year_users.php";
	

	$consoleTxt.=consoleMessage("info", "End check");

} // FIN IF $_POST["formNattSite"] OK


include ("views/header.html");

include ("views/stats_programs_per_year_users.php");

?>

