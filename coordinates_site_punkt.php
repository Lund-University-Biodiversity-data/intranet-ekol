<?php
$database="SFT";
$dataOrigin="scriptSitePunktIntranet";
require "lib/config.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN

require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

if (isset($_GET["display"]) && $_GET["display"]=="coord")
	$modeDisplay="coord";
else $modeDisplay="default";

$consoleTxt="";
$server=DEFAULT_SERVER;


$activityIdCreated=array();

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");


$final_result="";

$arrCoordSites=array();
$protocol="punkt";
include "process/coordinates_site_punkt_get_list.php";

include ("views/header.html");

include ("views/coordinates_site_punkt.php");

?>

