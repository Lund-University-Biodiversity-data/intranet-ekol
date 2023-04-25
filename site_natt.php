<?php
$database="SFT";
$dataOrigin="scriptSiteNattIntranet";
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

$internalSiteId="";
$kartaTx="";
$lan="";
$siteName="";

$listFiles=array();
$protocol="natt";

$activityIdCreated=array();

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if (count($mng->getServers())==1) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
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

require "process/site_get_param_data.php";

if (isset($_POST["formNattSite"]) && $_POST["formNattSite"]=="OK") {

	$kartaTx=$_POST["inputKartaTx"];
	$lan=$_POST["inputLan"];

	if (trim($_POST["inputLan"])=="" || trim($_POST["inputKartaTx"])=="") {

		$final_result.="<p><b>ERROR - All the fields are mandatory.</b></p>";
		$consoleTxt.=consoleMessage("error", "All the fields are mandatory.");
	}
	else {
		require "process/site_natt_create_json.php";

		if (isset($siteId) && $siteId!="") {
			if ($server=="PROD") $link=$linkBioSite["PROD"];
			else $link=$linkBioSite["DEV"];

			$final_result.='Site created => <a target="_blank" href="'.$link.$siteId.'">LINK TO BIOCOLLECT</a><br>';

		}
		else
			$final_result.="<p><b>Something wrong happened. Please check the console, and warn ".EMAIL_PROBLEM." if needed</b></p>";
	}
	

} // FIN IF $_POST["formNattSite"] OK




include ("views/header.html");

include ("views/site_natt.php");

?>

