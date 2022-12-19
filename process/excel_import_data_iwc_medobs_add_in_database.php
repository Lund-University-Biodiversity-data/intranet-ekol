<?php

$medObsAdded=array();

$finalOk=true;


if (count($arr_medobs)>0) {
	foreach($arr_medobs as $activityId => $helpersArray) {

		if ($inputAddInDb=="YES") {
		
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->update(
			    ['activityId' => $activityId],
			    ['$set' => ['data.helpers' => $helpersArray["helpers"]]],
			    ['multi' => false, 'upsert' => false]
			);

			$result = $mng->executeBulkWrite('ecodata.output', $bulk);
			$medObsAdded[]='<a target="_blank" href="'.$activityId.'">biocollect-survey</a>';	
		}
	}
}

$consoleTxt.=consoleMessage("info", count($medObsAdded)." document(s) edited with helpers");
