<?php
$database="SFT";
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

// get the lists avalable from the module
//require "process/list_get_available_lists.php";

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

$final_result="";

$listIdToCheck="dr627";
$animalsSelected="";

if (isset($_POST["formSelectAnimals"]) && $_POST["formSelectAnimals"]=="OK") {

	if (isset($_POST["inputSpeciesList"]) && $_POST["inputSpeciesList"]!="") {

		$animalsSelected=$_POST["inputSpeciesList"];
		require "process/list_check_biocollect_guids.php";

		$consoleTxt.=consoleMessage("info", "End check");

	}
	else {
		$consoleTxt.=consoleMessage("error", "No animals selected");
	}

}
elseif (isset($_POST["formFixDuplicates"]) && $_POST["formFixDuplicates"]=="OK" && $nbDuplicates>0) {
	
	$consoleTxt.=consoleMessage("info", "Fix duplicates");

	require "process/list_check_biocollect_guids_fix_duplicates.php";

	$consoleTxt.=consoleMessage("info", "End script");

}
/*
if (isset($_POST["formCompareLists"]) && $_POST["formCompareLists"]=="OK") {

	$listIdToCheck=$_POST["listIdToCheck"];

	if ($listIdToCheck) {
		require "process/list_check_biocollect_guids.php";
	}	

	$consoleTxt.=consoleMessage("info", "End check");

} // FIN IF $_POST["formNattSite"] OK
*/



include ("views/header.html");

include ("views/list_check_biocollect_guid.php");

?>

