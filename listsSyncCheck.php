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

// TO BE DONE
//require "process/list_get_available_lists.php";
$listAvailable=array(
	"dr627" => "Eurolist birds - for RTrim (backoffice)",
	"dr158" => "Eurolist birds (BioCollect forms)",
	"dr159" => "Eurolist mammals (BioCollect forms)",
	"dr630" => "Eurolist Owls (BioCollect forms)",
	"dr630" => "Eurolist amphibians (BioCollect forms)",
	"dr642" => "IWC Birds V2 (BioCollect forms)",
	"dr638" => "Coastal Birds - SE (BioCollect forms)",
	"dr202" => "Nocturnal birds  (BioCollect forms)",
);

if (isset($_POST["formCompareLists"]) && $_POST["formCompareLists"]=="OK") {

	$listIdsToCompare=$_POST["listIdsToCompare"];

	if (count($listIdsToCompare)>0) {
		require "process/list_sync_compare.php";
	}	

} // FIN IF $_POST["formNattSite"] OK




include ("views/header.html");

include ("views/list_sync_compare.php");

?>

