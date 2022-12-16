<?php

$surveysAdded=array();


$finalOk=true;

for ($i=1;$i<=3;$i++) {
	switch ($i) {
		case 1:
			$typeO="activity";
			$json=$arr_json_activity;
			break;
		case 2:
			$typeO="output";
			$json=$arr_json_output;
			break;
		case 3:
			$typeO="record";
			$json=$arr_json_record;
			break;
	}

	$nbRows[$typeO]=0;

	foreach($json as $document) {	

		if ($typeO=="output") {

			$surveysAdded[]=array(
				"persnr" => $document["data"]["recordedBy"],
				"site" => '<a target="_blank" href="'.$linkBioSite[$server].$document["data"]["location"].'">biocollect-site</a><br>',
				"period" => $document["data"]["period"],
				"method" => $document["data"]["observedFrom"],
				"date" => $document["data"]["surveyDate"],
				"link" => ($inputAddInDb=="YES" ? '<a target="_blank" href="'.$linkBioActivity[$server].$document["activityId"].'">biocollect-survey</a><br>' : "")
			);
		}

		if ($inputAddInDb=="YES") {
			/*
			$bulk = new MongoDB\Driver\BulkWrite;
			$_id1 = $bulk->insert($document);

			$result = $mng->executeBulkWrite('ecodata.'.$typeO, $bulk);
			*/			
			$nbRows[$typeO]++;
		}

	}

	$consoleTxt.=consoleMessage("info", $nbRows[$typeO]." document(s) added to collection ".$typeO);
}