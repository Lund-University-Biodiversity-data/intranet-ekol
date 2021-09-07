<?php
$database="SFT";
$dataOrigin="scriptExcel";
require "lib/config.php";

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
$protocol="vinter";
$activityIdCreated=array();


// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";


//exec ("scp -i lib/ssh/id_rsa.pub /home/mathieu/Documents/repos/intranet-ekol/json/SFT/vinter/excel_json_* radar@canmove-dev.ekol.lu.se:/home/radar/convert-SFT-SEBMS-to-MongoDB/dump_json_sft_sebms/SFT/vinter/");

$final_result="";


$path_excel=PATH_INPUT_EXCEL.$database."/".$protocol."/";

if (ENVIRONMENT=="TEST") {
	$hostExcelFiles="localhost";
}
else {
	$hostExcelFiles="Serveur Canmove-App";	
}

if (isset($_POST["execFormListFiles"]) && $_POST["execFormListFiles"]=="OK") {

	$server=$_POST["inputServer"];

	$array_sites=getArraySitesFromMongo($protocol, $commonFields[$protocol]["projectId"], $server);
	if ($array_sites=== false) {
	    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
	}
	else {

		$consoleTxt.=consoleMessage("info", "1) Get Existing Surveys");

		include "process/excel_import_get_existing_surveys.php";

		if ($okCon) {
			$consoleTxt.=consoleMessage("info", "2) Check filenames");

			include "process/excel_import_filenames_check.php";

		}
	}
} // FIN IF $_POST["execFormListFiles"] OK



if (isset($_POST["execFormProcessFiles"]) && $_POST["execFormProcessFiles"]=="OK") {

	$server=$_POST["serverHidden"];

	$array_sites=getArraySitesFromMongo($protocol, $commonFields[$protocol]["projectId"], $server);
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

			$consoleTxt.=consoleMessage("info", "3) Check lists animals");

			include "process/excel_import_list_animals.php";

			if ($okList) {
				include "process/excel_import_process_files.php";

				if ($fileRefused) {
					$final_result.="<p><b>Something wrong happened with ".$file.". Please check the file template and the console.</b></p>";
					$consoleTxt.=consoleMessage("error", "File could not be processed ".$file);
				}
				else {
					include "process/excel_import_insert_mongo.php";

					if ($finalOk)
						$final_result.="<p><b>SUCCESS</b></p>";
					else
						$final_result.="<p><b>Something wrong happened. Please check the console, and warn ".EMAIL_PROBLEM." if needed</b></p>";
				}
			}
			else {
				$consoleTxt.=consoleMessage("error", "Unable to proceed, due to errors in animals lists (see above)");
			}
		}
		else {
			$consoleTxt.=consoleMessage("info", "No OK file to process");
		}
	}
} // FIN IF $_POST["execFormProcessFiles"] OK



include ("views/header.html");

include ("views/import_excel.php");

?>

