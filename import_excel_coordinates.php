<?php
$database="SFT";
$dataOrigin="scriptExcel";
require "lib/config.php";
require "lib/functions.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN

require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$debug=false;

$consoleTxt="";
$server=DEFAULT_SERVER;
$listHiddenOkFiles="";
$listFiles=array();
$protocol="sommar";

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


if (ENVIRONMENT=="TEST") {
	$hostExcelFiles="localhost";
}
else {
	$hostExcelFiles="Serveur Canmove-App";	
}

if (isset($_POST["execFormListFiles"]) && $_POST["execFormListFiles"]=="OK") {

	$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	$server=$_POST["inputServer"];

	$path_excel=PATH_INPUT_EXCEL_COORDINATES.$database."/".$protocol."/";

	$templatePath="";
	switch($protocol) {

		case "punkt":
			$templateFileName="kartor_punktrutter_mall.xls";
			if (file_exists(PATH_INPUT_EXCEL_COORDINATES.$database."/".$protocol."/"."Template/".$templateFileName)) {
				$templateUrl=URL_WEBSITE_SURVEYS.$database."/".$protocol."/"."Template/".str_replace("#", "%23", $templateFileName);
			}
			break;

		case "natt":
		default:
			$templateFileName="kartor_nattrutter_mall.xls";
			if (file_exists(PATH_INPUT_EXCEL_COORDINATES.$database."/".$protocol."/"."Template/".$templateFileName)) {
				$templateUrl=URL_WEBSITE_SURVEYS.$database."/".$protocol."/"."Template/".str_replace("#", "%23", $templateFileName);
			}
			break;

	}


	$array_sites=getArraySitesFromMongo($commonFields[$protocol]["projectId"], $server);
	if ($array_sites=== false) {
	    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
	}
	else {

		//$consoleTxt.=consoleMessage("info", "1) Get Existing Coordinates");

		include "process/coordinates_site_get_list.php";

		$consoleTxt.=consoleMessage("info", "2) Check filenames");
		
		include "process/excel_import_coordinates_filenames_check.php";
	}
} // FIN IF $_POST["execFormListFiles"] OK



if (isset($_POST["execFormProcessFiles"]) && $_POST["execFormProcessFiles"]=="OK") {

	$protocol = (isset($_POST["inputProtocolHidden"]) ? $_POST["inputProtocolHidden"] : "");

	$server=$_POST["serverHidden"];

	$path_excel=PATH_INPUT_EXCEL_COORDINATES.$database."/".$protocol."/";

	$array_sites=getArraySitesFromMongo($commonFields[$protocol]["projectId"], $server);
	if ($array_sites=== false) {
	    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
	}
	else {

		$mongoConnectionUrl=$mongoConnection[$server];

		$consoleTxt.=consoleMessage("info", " List OK Files : ".$_POST["listHiddenOkFiles"]);

		$listFilesOk=explode(FILENAME_SEPARATOR, $_POST["listHiddenOkFiles"]);

		// remove the final empty file if needed
		if ($listFilesOk[count($listFilesOk)-1]=="") unset($listFilesOk[count($listFilesOk)-1]);

		if (count($listFilesOk)>0) {

			$consoleTxt.=consoleMessage("info", "3) Import files");

			include "process/excel_import_coordinates_process_files.php";

			if ($fileRefused) {
				$final_result.="<p><b>Something wrong happened with ".$file.". Please check the file template and the console.</b></p>";
				$consoleTxt.=consoleMessage("error", "File could not be processed ".$file);
			}
			else {
				include "process/excel_import_coordinates_insert_mongo.php";

				if ($finalOk)
					$final_result.="<p><b>SUCCESS</b></p>";
				else
					$final_result.="<p><b>Something wrong happened. Please check the console, and warn ".EMAIL_PROBLEM." if needed</b></p>";
			}
		}
		else {
			$consoleTxt.=consoleMessage("info", "No OK file to process");
		}
	}
} // FIN IF $_POST["execFormProcessFiles"] OK


include ("views/header.html");

include ("views/import_excel_coordinates.php");

?>

