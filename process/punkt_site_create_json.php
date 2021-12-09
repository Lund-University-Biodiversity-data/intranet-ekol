<?php

$explInternalSiteId=explode("-", $internalSiteId);
if (count($explInternalSiteId)!=3) {
	$consoleTxt.= consoleMessage("error", "Wrong format for the persnr, must contain 3 parts (".count($explInternalSiteId).")");
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
				"name" => $siteName,
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
		}

	}
	
}

?>