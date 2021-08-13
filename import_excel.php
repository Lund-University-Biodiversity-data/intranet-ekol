<?php
$database="SFT";
$dataOrigin="scriptExcel";
require "lib/config.php";
require "lib/functions.php";

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$consoleTxt="";
$server="";
$listHiddenOkFiles="";
$listFiles=array();
$protocol="vinter";
$activityIdCreated=array();

if (isset($_POST["execFormListFiles"]) && $_POST["execFormListFiles"]=="OK") {

	$server=$_POST["inputServer"];

	$consoleTxt.=consoleMessage("info", "1) Get Existing Surveys");

	include "process/excel_import_get_existing_surveys.php";

	if ($okCon) {
		$consoleTxt.=consoleMessage("info", "2) Check filenames");

		include "process/excel_import_filenames_check.php";
	}

} // FIN IF $_POST["execFormListFiles"] OK





if (isset($_POST["execFormProcessFiles"]) && $_POST["execFormProcessFiles"]=="OK") {


	$server=$_POST["serverHidden"];


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
		}
		else {
			$consoleTxt.=consoleMessage("error", "Unable to proceed, due to errors in animals lists (see above)");
		}
	}
	else {
		$consoleTxt.=consoleMessage("info", "No OK file to process");
	}
} // FIN IF $_POST["execFormProcessFiles"] OK



include ("views/header.html");

include ("views/import_excel.php");

?>

