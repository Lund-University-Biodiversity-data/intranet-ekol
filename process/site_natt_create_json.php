<?php

$internalSiteId=$kartaTx;
$siteName=$kartaTx." - NATT";

// check if a site already exists with this internalSiteId
$siteAlready = getSiteFromInternalId ($internalSiteId, $server);
if (isset($siteAlready["locationID"]) && $siteAlready["locationID"]!="") {
	$consoleTxt.= consoleMessage("error", "A site already exists in the database with internalSiteId $internalSiteId => ".$siteAlready["locationID"]);

}
else {


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
			"name" => $internalSiteId." - NATT",
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
				"kartaTx" => $kartaTx,
				"bookingComment" => "",
				"paperSurveySubmitted" => ""
			),
			"owner" => $person["personId"],
			"verificationStatus" => "godkänd",
			"dataOrigin" => $dataOrigin
		);

		$bulk = new MongoDB\Driver\BulkWrite;
		$_id1 = $bulk->insert($document);

		$result = $mng->executeBulkWrite('ecodata.site', $bulk);

		$consoleTxt.=consoleMessage("info", "Site created in MongoDb with siteId ".$siteId);


	    // need to update as well the projectActivityId to make this site available for users
   
	    $bulkPA = new MongoDB\Driver\BulkWrite;
	    //$filter = [];
	    $filter = ['projectActivityId' => $commonFields[$protocol]["projectActivityId"]];
	    $options =  ['$push' => ['sites' => $siteId]];
	    $updateOptions = [];
	    $bulkPA->update($filter, $options, $updateOptions); 
	    $result = $mng->executeBulkWrite('ecodata.projectActivity', $bulkPA);

	    $consoleTxt.=consoleMessage("info", "Site added to the projectActivity ".$commonFields[$protocol]["projectActivityId"]);


	}

}


?>