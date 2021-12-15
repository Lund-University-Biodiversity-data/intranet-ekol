<?php

$finalOk=true;

for ($i=1;$i<=4;$i++) {
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
		case 4:
			$typeO="person";
			$json=$arr_json_person;
			break;
	}

	$nbRows[$typeO]=0;

	foreach($json as $document) {	

		$bulk = new MongoDB\Driver\BulkWrite;
		$_id1 = $bulk->insert($document);

		$result = $mng->executeBulkWrite('ecodata.'.$typeO, $bulk);
		$nbRows[$typeO]++;

	}

	$consoleTxt.=consoleMessage("info", $nbRows[$typeO]." document(s) added to collection ".$typeO);
}

if (count($arrActivityId)==0) {
	$consoleTxt.=consoleMessage("error", "No survey to create");
	$finalOk=false;
}
else {
	foreach($arrActivityId as $filename => $actID) {
		if ($server=="PROD") $link=$linkBioActivity["PROD"];
		else $link=$linkBioActivity["DEV"];
		// NO TEST !

		$final_result.=$filename. ' successfully imported => <a target="_blank" href="'.$link.$actID.'">LINK TO BIOCOLLECT</a><br>';
	}
}

if (!$debug) {
	// move files to OK folder
	foreach ($listFilesOk as $fil) {
		$cmdMv="mv '".$path_excel.$fil."' ".$path_excel."OK/";
		exec($cmdMv, $outputMv, $retVal);
		if ($retVal) {
			$consoleTxt.=consoleMessage("error", "Can't move files : ".$cmdMv);
			$finalOk=false;
		}
		else{
			$consoleTxt.=consoleMessage("info", "Command MV executed. File moved.");
			//$consoleTxt.=consoleMessage("info", print_r($outputMv));
		}
	}
}

if ($ratioSpecies!=1) {
	var_dump($arrSpeciesNotFound);
}

?>