<?php


$array_sites=getArraySitesFromMongo($protocol, $commonFields[$protocol]["projectId"], $server);
if ($array_sites=== false) {
    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
}
else {

	$consoleTxt.=consoleMessage("info", "1) Get sites for protocol ".$protocol);
	$consoleTxt.=consoleMessage("info", count($array_sites)." site(s).");

	//include "process/excel_import_get_existing_surveys.php";
	foreach ($array_sites as $indexSite => $dataSite) {
		$lineSite=array();

		$lineSite["locationID"]=$dataSite["locationID"];
		$lineSite["urlBioCollect"]=$linkBioSite[$server].$dataSite["locationID"];
		$lineSite["locationName"]=$dataSite["locationName"];
		$lineSite["bookedBy"]=$dataSite["bookedBy"];
		$lineSite["bookingComment"]=$dataSite["bookingComment"];
		$lineSite["lastYearSurveyed"]="?";

		if (trim($lineSite["bookedBy"])!="") {
			$lineSite["booked"]="yes";
			$arrPersons[]=$lineSite["bookedBy"];
			$arrPersonsDetails[$lineSite["bookedBy"]]=array();
		}
		else {
			$lineSite["booked"]="no";
		}
		$arrRecap[$indexSite]=$lineSite;
	}

}

?>
