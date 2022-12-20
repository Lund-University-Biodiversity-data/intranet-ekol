<?php

$medObsAdded=array();

$finalOk=true;

$initDate = new \DateTime();
$stampedDate = $initDate->getTimestamp() * 1000;
$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

if (count($arr_medobs)>0) {
	foreach($arr_medobs as $activityId => $helpersData) {

		if ($inputAddInDb=="YES") {
			
			// edit the activity with helpersIds
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(
			    ['activityId' => $activityId],
			    ['$set' => [
			    	'helperIds' => $helpersData["helperIds"],
			    	"lastUpdated" => $nowISODate
			    ]],
			    ['multi' => false, 'upsert' => false]
			);

			$result = $mng->executeBulkWrite('ecodata.activity', $bulk);


			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(
			    ['activityId' => $activityId],
			    ['$set' => [
			    	'data.helpers' => $helpersData["helperNames"],
			    	"lastUpdated" => $nowISODate
			    ]],
			    ['multi' => false, 'upsert' => false]
			);

			$result = $mng->executeBulkWrite('ecodata.output', $bulk);


			$medObsAdded[]='<a target="_blank" href="'.$linkBioActivity[$server].$activityId.'">biocollect-survey</a>';	
		}
	}
}

$consoleTxt.=consoleMessage("info", count($medObsAdded)." document(s) edited with helpers");
