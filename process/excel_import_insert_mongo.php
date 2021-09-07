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
	//exit();

	$consoleTxt.=consoleMessage("info", $nbRows[$typeO]." document(s) added to collection ".$typeO);
/*
	$filename_json='excel_json_'.$database.'_'.$protocol.'_'.$typeO.'s_'.date("Y-m-d-His").'.json';
	$path=PATH_OUTPUT_JSON.$database.'/'.$protocol."/".$filename_json;
	//echo 'db.'.$typeO.'.remove({"dateCreated" : {$gte: new ISODate("'.date("Y-m-d").'T01:15:31Z")}})'."\n";
	$cmdMongImport= 'mongoimport --db ecodata --collection '.$typeO.' --jsonArray --file '.$path;
	$consoleTxt.=consoleMessage("info", $cmdMongImport);

	//$json = json_encode($arr_rt, JSON_UNESCAPED_SLASHES); 
	if ($fp = fopen($path, 'w')) {
		fwrite($fp, $json);
		fclose($fp);
	}
	else $consoleTxt.=consoleMessage("error", "can't create file ".$path);

	//echo 'PATH: '.$path."\n\n";
*/
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
		$cmdMv='mv '.$path_excel.$fil.' '.$path_excel."OK/";
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

/*
if ($server=="PROD") $serverHostPath="ubuntu@89.45.234.73:/home/ubuntu/convert-SFT-SEBMS-to-MongoDB/dump_json_sft_sebms/";
else $serverHostPath="radar@canmove-dev.ekol.lu.se:/home/radar/convert-SFT-SEBMS-to-MongoDB/dump_json_sft_sebms/";

$cmdScp="scp ".PATH_OUTPUT_JSON."/".$database."/".$protocol."/excel_json_* ".$serverHostPath.$database."/".$protocol."/";
//exec ("scp  /home/mathieu/Documents/repos/intranet-ekol/json/SFT/vinter/excel_json_* radar@canmove-dev.ekol.lu.se:/home/radar/convert-SFT-SEBMS-to-MongoDB/dump_json_sft_sebms/SFT/vinter/");

if (!$debug) {

	exec($cmdScp, $outputScp, $retVal);
	if ($retVal) {
		$consoleTxt.=consoleMessage("error", "Can't copy to server host. Scp command error : ".$cmdScp);
	}
	else {
		$consoleTxt.=consoleMessage("info", "Command Scp executed. Json files sent. ");
		//$consoleTxt.=consoleMessage("info", print_r($outputScp));

		$final_result="<b>JSONS FILES TRANSFERED AND CREATED !</b><br>";

		foreach($arrActivityId as $filename => $actID) {
			if ($server=="PROD") $link=$linkBioActivity["PROD"];
			else $link=$linkBioActivity["DEV"];
			// NO TEST !

			$final_result.=$filename. ' => <a target="_blank" href="'.$link.$actID.'">LINK TO BIOCOLLECT</a><br>';
		}
	}


}
*/

?>