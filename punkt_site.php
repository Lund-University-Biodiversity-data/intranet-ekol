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
$listFiles=array();
$protocol="sommar";

$activityIdCreated=array();


// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";

$final_result="";

if (isset($_POST["formPunktSite"]) && $_POST["formPunktSite"]=="OK") {

	/*
	$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	$server=$_POST["inputServer"];

	$path_excel=PATH_INPUT_EXCEL.$database."/".$protocol."/";

	$templatePath="";
	switch($protocol) {
		case "sommar":
			$templateFileName="SomYY-YYMMDD-X-#XX.xls";
			if (file_exists(PATH_INPUT_EXCEL.$database."/".$protocol."/"."Template/".$templateFileName)) {
				$templateUrl=URL_WEBSITE_SURVEYS.$database."/".$protocol."/"."Template/".str_replace("#", "%23", $templateFileName);
			}
			break;

		case "vinter":
		default:
			$templateFileName="VinYY-YYMMDD-X-#XX-PX.xls";
			if (file_exists(PATH_INPUT_EXCEL.$database."/".$protocol."/"."Template/".$templateFileName)) {
				$templateUrl=URL_WEBSITE_SURVEYS.$database."/".$protocol."/"."Template/".str_replace("#", "%23", $templateFileName);
			}
			break;

	}


	$array_sites=getArraySitesFromMongo($protocol, $commonFields[$protocol]["projectId"], $server);
	if ($array_sites=== false) {
	    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
	}
	else {

		$consoleTxt.=consoleMessage("info", "1) Get Existing Surveys");

		include "process/excel_import_get_existing_surveys.php";

		if ($okCon) {
			$consoleTxt.=consoleMessage("info", "2) Check filenames");
//echo "YEAH";exit();
			include "process/excel_import_filenames_check.php";

		}
	}
	
	*/
$link="";
$actID="";
	$final_result.='SITE NOT CREATED BUT THE LINK TO IT WILL BE HERE => <a target="_blank" href="'.$link.$actID.'">LINK TO BIOCOLLECT</a><br>';

	$consoleTxt.=consoleMessage("info", "NOTHING TRIED, EVERYTHING WORKED :)");

} // FIN IF $_POST["formPunktSite"] OK




include ("views/header.html");

include ("views/punkt_site.php");

?>

