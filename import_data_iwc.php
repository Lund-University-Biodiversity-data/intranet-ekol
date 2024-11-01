<?php
$dataOrigin="scriptExcel";
require "lib/config.php";

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

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

$protocol="iwc";
$dataOrigin="scriptImportIWC";

// check mode as default
$inputAddInDb="NO";

$final_result="";

if (isset($_POST["execFormImportDataIWC"]) && $_POST["execFormImportDataIWC"]=="OK" && count($_FILES)==1) {

	$uploadDir=PATH_INPUT_EXCEL_SURVEYS."SFT/IWC/";

	$nameFileOriginal=basename($_FILES['inputFile']['name']);
	$extension = pathinfo($nameFileOriginal, PATHINFO_EXTENSION);
	$uploadFile="import_data_iwc_".date("YmdHis").".".$extension;
	$uploadFilePath=$uploadDir.$uploadFile;

	$inputAddInDb=$_POST["inputAddInDb"];

	if (move_uploaded_file($_FILES['inputFile']['tmp_name'], $uploadFilePath)) {
		$consoleTxt.=consoleMessage("info", "1) File uploaded and ready to be processed ".$uploadFilePath);
		$array_sites=getArraySitesFromMongo($commonFields[$protocol]["projectId"], $server);
		$array_persons=getArrayPersonsFromMongo($commonFields[$protocol]["projectId"], $server);
		if ($array_sites=== false) {
		    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
		}
		else {

			$consoleTxt.=consoleMessage("info", "2) Check lists animals");

			include "process/excel_import_surveys_list_animals.php";

			if ($okList) {

				$consoleTxt.=consoleMessage("info", "3) Read the excel file");
				$fileRefused=true;
				include "process/excel_import_data_iwc_read_file.php";

				if (!$fileRefused) {
					$consoleTxt.=consoleMessage("info", "4) Import in the mongoDb");
					
					include "process/excel_import_data_iwc_add_in_database.php";

				}
				else {
					$consoleTxt.=consoleMessage("info", "Script stopped due to file refused (see errors above)");
				}
			}
		}
	}
	else {
		$consoleTxt.=consoleMessage("error", "Can't move uploaded file to ".$uploadFile);
	}
} // FIN IF $_POST["execFormListFiles"] OK




include ("views/header.html");

include ("views/import_data_iwc.php");

?>

