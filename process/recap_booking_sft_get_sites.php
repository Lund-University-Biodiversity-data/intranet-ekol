<?php


$arrSitesDetails=getArraySitesFromMongo($commonFields[$protocol]["projectId"], $server);
if ($arrSitesDetails=== false) {
    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
}
else {

	$consoleTxt.=consoleMessage("info", "1) Get sites for protocol ".$protocol);
	$consoleTxt.=consoleMessage("info", count($arrSitesDetails)." site(s).");

	//include "process/excel_import_get_existing_surveys.php";
	foreach ($arrSitesDetails as $indexSite => $dataSite) {

		$arrSites[]=$dataSite["locationID"];
		$arrSitesInternal[$dataSite["locationID"]]=$indexSite;
		$lineSite=array();

		$lineSite["locationID"]=$dataSite["locationID"];
		$lineSite["urlBioCollect"]=$linkBioSite[$server].$dataSite["locationID"];
		$lineSite["commonName"]=$dataSite["commonName"];
		$lineSite["routetype"]=$dataSite["routetype"];
		$lineSite["county"]=$dataSite["lan"];
		$lineSite["province"]=$dataSite["lsk"];
		$lineSite["booked"]=$dataSite["bookedBy"];
		$lineSite["bookedBy"]=$dataSite["bookedBy"];
		$lineSite["bookingComment"]=$dataSite["bookingComment"];
		$lineSite["paperSurveySubmitted"]=$dataSite["paperSurveySubmitted"];
		$lineSite["summarySurveySubmitted"]=$dataSite["summarySurveySubmitted"];
		$lineSite["lastYearSurveyed"]="";
		$lineSite["lastYearSurveyedStatus"]="";
		$lineSite["excelReceived"]="";
		$lineSite["excelReceivedYear"]="";

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
