<?php
$database="SFT";
$dataOrigin="scriptMoveRouet";
require "lib/config.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN

require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

$consoleTxt="";
$server=DEFAULT_SERVER;

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$consoleTxt.=consoleMessage("info", "Connection to ".$mongoConnection[$server]);

//$activityIdToFix="07ddb07e-1c8d-f87e-1a7a-1ee826e40318";
$activityIdToFix="";
$activityDetails="";
$siteNewCorrectId="";

$finalOk=false;

// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";

$final_result="";


if (isset($_POST["getActivityDetails"]) && $_POST["getActivityDetails"]=="OK") {

	$activityIdToFix=trim($_POST["activityIdToFix"]);
	if ($activityIdToFix=="") {
		$consoleTxt.=consoleMessage("error", "Please specify an activityId");
	}
	else {
		$server=$_POST["inputServer"];

		include "process/activity_move_route_get_details.php";
	}

} // FIN IF $_POST["getActivityDetails"] OK



if (isset($_POST["getNewCorrectSiteId"]) && $_POST["getNewCorrectSiteId"]=="OK") {

	$activityIdToFix=$_POST["activityIdToFixHidden"];
	$server=$_POST["serverHidden"];

	$siteNewCorrectId=trim($_POST["inputSelectNewSite"]);

	if ($siteNewCorrectId=="") {

		include "process/activity_move_route_get_details.php";
		$consoleTxt.=consoleMessage("error", "Please specify a new siteId");

		$finalOk=false;
	}
	else {
		$server=$_POST["inputServer"];

		include "process/activity_move_route_change_database.php";
	}




	if ($finalOk)
		$final_result.="<p><b>SUCCESS</b></p>";
	else
		$final_result.="<p><b>Something wrong happened. Please check the console, and warn ".EMAIL_PROBLEM." if needed</b></p>";
				
} // FIN IF $_POST["execFormProcessFiles"] OK


include ("views/header.html");

include ("views/activity_move_route.php");

?>

