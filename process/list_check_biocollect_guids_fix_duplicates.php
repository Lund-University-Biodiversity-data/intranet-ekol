<?php


$tabCommand=array();

$initDate = new \DateTime();
$stampedDate = $initDate->getTimestamp() * 1000;
$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

$nbUpdate=0;

foreach ($tabRecap as $guid => $rowContent) {

	if ($rowContent["guidDupl"]=="GUID-dupl") {

		$maxRt=0;
		$okSwedishName="";

		// get the maximum to obtain the correct result
		foreach ($rowContent["items"] as $swedishName => $rowItem) {
			if ($rowItem["nbItems"]>$maxRt) {
				$okSwedishName=$rowItem["swedishName-lists"];
				$okScientificName=$rowItem["scientificName"];
			}
			else {

			}
		}

		if ($okSwedishName!="" && $okSwedishName!="MISSING") {
			$bulk = new MongoDB\Driver\BulkWrite;
			$filter = [
		    	'data.observations.species.guid' => (string)$guid,
		    	'data.observations.species.name' => [
		    		'$ne' => $okSwedishName
		    	]
		    ];
		    $options =  ['$set' => [
		    	'data.observations.$.species.name' => $okSwedishName,
		    	'data.observations.$.species.scientificName' => $okScientificName,
		    	'data.observations.$.species.commonName' => $okSwedishName,
		    	'lastUpdated' => $nowISODate
		    ]];


		    $updateOptions = ['multi' => true];
		    $bulk->update($filter, $options, $updateOptions); 
		    $result = $mng->executeBulkWrite('ecodata.output', $bulk);


		    $consoleTxt.=consoleMessage("info", "Fix guid/name/SN in OUTPUT ".$guid. " to ".$okSwedishName." / ".$okScientificName." => ".$result->getMatchedCount()." matched result(s), and ".$result->getModifiedCount()." modified");


		    // update the records as well
			$bulk = new MongoDB\Driver\BulkWrite;
			$filter = [
		    	'guid' => (string)$guid,
		    	'name' => [
		    		'$ne' => $okSwedishName
		    	]
		    ];
		    $options =  ['$set' => [
		    	'name' => $okSwedishName,
		    	'scientificName' => $okScientificName,
		    	'lastUpdated' => $nowISODate
		    ]];


		    $updateOptions = ['multi' => true];
		    $bulk->update($filter, $options, $updateOptions); 
		    $result = $mng->executeBulkWrite('ecodata.record', $bulk);


		    $nbUpdate++;

		    $consoleTxt.=consoleMessage("info", "Fix guid/name/SN in RECORD ".$guid. " to ".$okSwedishName." / ".$okScientificName." => ".$result->getMatchedCount()." matched result(s), and ".$result->getModifiedCount()." modified");

		}
	}
}

$consoleTxt.=consoleMessage("info", $nbUpdate." row(s) updated");

$final_result.="<p>".$nbUpdate." row(s) updated. Refresh the page to get the updated table.</p>";
?>