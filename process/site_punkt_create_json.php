<?php

$explInternalSiteId=explode("-", $internalSiteId);
if (count($explInternalSiteId)!=3) {
	$consoleTxt.= consoleMessage("error", "Wrong format for the persnr, must contain 3 parts (".count($explInternalSiteId).")");
}
else {

	// check if a site already exists with this internalSiteId
	$siteAlready = getSiteFromInternalId ($internalSiteId, $server);
	if (isset($siteAlready["locationID"]) && $siteAlready["locationID"]!="") {
		$consoleTxt.= consoleMessage("error", "A site already exists in the database with internalSiteId $internalSiteId => ".$siteAlready["locationID"]);

	}
	else {
		$persnr=$explInternalSiteId[0]."-".$explInternalSiteId[1];

		$person=getPersonFromInternalId ($persnr, $server);
		if (!isset($person["personId"]) || $person["personId"]=="") {
			$consoleTxt.= consoleMessage("error", "No personId found in the database with the persnr ".$persnr);
		}
		else {
			$consoleTxt.= consoleMessage("info", "personId found in the database with the persnr ".$persnr);

			$qKarta="SELECT * FROM koordinater_mittpunkt_topokartan WHERE kartatx='".$kartaTx."'";
			$rKarta = pg_query($db_connection, $qKarta);
			$rtKarta = pg_fetch_array($rKarta);
			$lat=$rtKarta["wgs84_lat"];
			$lon=$rtKarta["wgs84_lon"];

			if (trim($lat)=="" || trim($lon)=="") {
				$consoleTxt.= consoleMessage("error", "Can't get the WGS84 coordinates for ".$kartaTx.' ('.$lat.'/'.$lon.')');
			}
			else {
				$consoleTxt.= consoleMessage("info", "WGS84 coordinates found for ".$kartaTx.' ('.$lat.'/'.$lon.')');
							
				$siteId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");

				$initDate = new \DateTime();
				$stampedDate = $initDate->getTimestamp() * 1000;
				$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

				$document=array(
					"siteId" => $siteId,
					"dateCreated" => $nowISODate,
					"lastUpdated" => $nowISODate,
					"name" => $internalSiteId." - ".$siteName,
					"status" => "active",
					"type" => "",
					"area" => "0",
					"projects" => array(
						$commonFields[$protocol]["projectId"]
					),
					"extent" => array(
						"geometry" => array(
							"type" => "Point",
							"coordinates" => array(
								floatval($lon), floatval($lat)
							),
							"decimalLongitude" => floatval($lon),
							"decimalLatitude" => floatval($lat),
							"areaKmSq" => 0,
							"aream2" => 0,
							"centre" => array(
								floatval($lon), floatval($lat)
							)
						),
						"source" => "Point"
					),
					"geoIndex" => array(
						"type" => "Point",
						"coordinates" => array(
							floatval($lon), floatval($lat)
						)
					),
					"transectParts" => array(),
					"adminProperties" => array(
						"internalSiteId" => $internalSiteId,
						"lan" => $lan,
						"kartaTx" => $kartaTx
					),
					"owner" => $person["personId"],
					"verificationStatus" => "godkänd",
					"dataOrigin" => $dataOrigin
				);

				$bulk = new MongoDB\Driver\BulkWrite;
				$_id1 = $bulk->insert($document);

				$result = $mng->executeBulkWrite('ecodata.site', $bulk);

				$consoleTxt.=consoleMessage("info", "Site created in MongoDb with siteId ".$siteId);


				// need to update the person with the ownedSites field


				$bulkP = new MongoDB\Driver\BulkWrite;
			    //$filter = [];
			    $filter = ['personId' => $person["personId"]];
			    $options =  ['$push' => ['ownedSites' => $siteId]];
			    $updateOptions = [];
			    $bulkP->update($filter, $options, $updateOptions); 
			    $result = $mng->executeBulkWrite('ecodata.person', $bulkP);


			    $consoleTxt.=consoleMessage("info", "Site added to the person ownedSites");

			    // need to update as well the projectActivityId to make this site available for users

			    $arrPAIds=array(
			    	$commonFields["sommar"]["datasetName"] => $commonFields["sommar"]["projectActivityId"],
			    	$commonFields["vinter"]["datasetName"] => $commonFields["vinter"]["projectActivityId"]
			    );
			    foreach ($arrPAIds as $PAname => $PAId) {
				    $bulkPA = new MongoDB\Driver\BulkWrite;
				    //$filter = [];
				    $filter = ['projectActivityId' => $PAId];
				    $options =  ['$push' => ['sites' => $siteId]];
				    $updateOptions = [];
				    $bulkPA->update($filter, $options, $updateOptions); 
				    $result = $mng->executeBulkWrite('ecodata.projectActivity', $bulkPA);

				    $consoleTxt.=consoleMessage("info", "Site added to the projectActivity ".$PAname);

			    }

			}

		}
	}
}

?>