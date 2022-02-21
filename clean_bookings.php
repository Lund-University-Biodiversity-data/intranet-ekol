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

$debug=false;

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

// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";

$final_result="";

$arrProtocol=array("kust", "iwc", "natt", "std");

require "process/clean_bookings_get_bookings.php";

if (isset($_POST["execCleanBookings"]) && $_POST["execCleanBookings"]=="OK") {

	$protocol=$_POST["inputProtocol"];
	

	if (in_array($protocol, $arrProtocol)) {

		//require "process/clean_bookings_exec.php";

	}
	else {
		$final_result.="<p><b>Wrong protocol selected</b></p>";
	
	}
	

} // FIN IF $_POST["formPunktSite"] OK




include ("views/header.html");

include ("views/clean_bookings.php");

?>

