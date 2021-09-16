<?php
$database="SFT";
$dataOrigin="scriptExcel";
require "lib/config.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN


require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

$consoleTxt="";
$server=DEFAULT_SERVER;

$protocol="std";

$arrRecap=array();
$arrPersons=array();
$arrPersonsDetails=array();
$arrSites=array();
$arrSitesInternal=array();
$arrSitesDetails=array();
$arrExcelReceived=array();

$pathInputFiles=PATH_INPUT_EXCEL.$database."/".$protocol."/received/";

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

if (isset($_POST["execFormRecapBookingScheme"]) && $_POST["execFormRecapBookingScheme"]=="OK") {

	if (isset($_POST["protocol"])) $protocol=$_POST["protocol"];
	if (isset($_POST["server"])) $server=$_POST["server"];

	include "process/recap_booking_sft_get_sites.php";

	if (count($arrPersons)>0) {
		include "process/recap_booking_sft_get_persons.php";
	}

	include "process/recap_booking_sft_get_lastsurvey.php";

	include "process/recap_booking_sft_get_excelreceived.php";

	ksort($arrRecap);
}

include ("views/header.html");

include ("views/recap_booking_scheme_sft.php");


?>
