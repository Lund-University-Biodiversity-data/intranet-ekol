<?php


$arrSitesDetails=getArraySitesFromMongo($commonFields[$protocol]["projectId"], $server);
if ($arrSitesDetails=== false) {
    $consoleTxt.=consoleMessage("error", "Can't connect to MongoDb");
}
else {

	$consoleTxt.=consoleMessage("info", "1) Get all sites for protocol ".$protocol);
	$consoleTxt.=consoleMessage("info", count($arrSitesDetails)." site(s).");

	$nb0=0;
	$nb20=0;
	//include "process/excel_import_get_existing_surveys.php";
	foreach ($arrSitesDetails as $indexSite => $dataSite) {
		//$arrSites[]=$dataSite["locationID"];
		//$arrSitesInternal[$dataSite["locationID"]]=$indexSite;

		if ($modeDisplay=="coord") {
			// add only if there are coordinates to display
			if (count($dataSite["transectParts"])>0) {
				foreach($dataSite["transectParts"] as $tp) {
					$lineSite=array();

					$lineSite["internalSiteID"]=$indexSite;
					$lineSite["locationID"]=$dataSite["locationID"];
					$lineSite["kartaTx"]=$dataSite["kartaTx"];
					$lineSite["urlBioCollect"]=$linkBioSite[$server].$dataSite["locationID"];
					$lineSite["name"]=$tp["name"];
					$lineSite["latitude"]=$tp["latitude"];
					$lineSite["longitude"]=$tp["longitude"];

					$arrCoordSites[]=$lineSite;
				}
			}
		}
		else {
			$lineSite=array();

			$lineSite["internalSiteID"]=$indexSite;
			$lineSite["locationID"]=$dataSite["locationID"];
			$lineSite["kartaTx"]=$dataSite["kartaTx"];
			$lineSite["urlBioCollect"]=$linkBioSite[$server].$dataSite["locationID"];
			$lineSite["nbTransectParts"]=count($dataSite["transectParts"]);

			if ($dataSite["transectParts"]==0) $nb0++;
			elseif ($dataSite["transectParts"]==20) $nb20++;
			$arrCoordSites[]=$lineSite;
		}
		
	}

	if ($modeDisplay!="coord") {
		$consoleTxt.=consoleMessage("info", $nb0." site(s) with 0 point coordinate (".number_format(($nb0*100)/count($arrSitesDetails), 2)."%)");
		$consoleTxt.=consoleMessage("info", $nb20." site(s) with 20 point coordinates (".number_format(($nb20*100)/count($arrSitesDetails), 2)."%)");
	}
}

?>
