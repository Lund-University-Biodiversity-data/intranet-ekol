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

$internalSiteId="";
$kartaTx="";
$lan="";
$siteName="";

$listFiles=array();
$protocol="sommar";

$activityIdCreated=array();

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";

$final_result="";

$arrCoordSites=array();
$protocol="punkt";
include "process/coordinates_site_punkt_get_list.php";


if (isset($_POST["formCoordPunktSite"]) && $_POST["formCoordPunktSite"]=="OK") {

	/*
	$internalSiteId=$_POST["inputInternalSiteId"];
	$kartaTx=$_POST["inputKartaTx"];
	$lan=$_POST["inputLan"];
	$siteName=$_POST["inputSiteName"];

	if (trim($_POST["inputLan"])=="" || trim($_POST["inputInternalSiteId"])=="" || trim($_POST["inputKartaTx"])=="" || trim($_POST["inputSiteName"])=="") {

		$final_result.="<p><b>ERROR - All the fields are mandatory.</b></p>";
		$consoleTxt.=consoleMessage("error", "All the fields are mandatory.");
	}
	else {
		require "process/site_punkt_create_json.php";

		if (isset($siteId) && $siteId!="") {
			if ($server=="PROD") $link=$linkBioSite["PROD"];
			else $link=$linkBioSite["DEV"];

			$final_result.='Site created => <a target="_blank" href="'.$link.$siteId.'">LINK TO BIOCOLLECT</a><br>';

		}
		else
			$final_result.="<p><b>Something wrong happened. Please check the console, and warn ".EMAIL_PROBLEM." if needed</b></p>";
	}
	*/

} // FIN IF $_POST["formPunktSite"] OK




include ("views/header.html");

include ("views/coordinates_site_punkt.php");

?>

