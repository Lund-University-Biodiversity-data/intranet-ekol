<?php

$finalOk=true;

/*
db.site.find({siteId:"b2668f4f-ca84-ac56-e2bb-015a9015effa"}).pretty()
db.site.updateOne({siteId:"b2668f4f-ca84-ac56-e2bb-015a9015effa"},{$unset:{transectParts:1}})
*/

//print_r($arr_json_sites);exit();

$nbUpdate=0;
$arrSiteIdOk=array();
foreach($arr_json_sites as $mongoSiteId => $dataCoord) {

	$bulk = new MongoDB\Driver\BulkWrite;
	$filter = [
    	'siteId' => $mongoSiteId
    ];
    $options =  ['$set' => [
    	'transectParts' => ($dataCoord["data"]),
    	'geoIndex.coordinates' => [$dataCoord["centroidLon"], $dataCoord["centroidLat"]],
    	'extent.geometry.coordinates' => [$dataCoord["centroidLon"], $dataCoord["centroidLat"]],
    	'extent.geometry.centre' => [number_format($dataCoord["centroidLon"], 5), number_format($dataCoord["centroidLat"], 5)],
    	'extent.geometry.decimalLatitude' => $dataCoord["centroidLat"],
    	'extent.geometry.decimalLongitude' => $dataCoord["centroidLon"]
    ]];

    $updateOptions = ['multi' => false];
    $bulk->update($filter, $options, $updateOptions); 
    $result = $mng->executeBulkWrite('ecodata.site', $bulk);

    $nbUpdate++;

    $arrSiteIdOk[$dataCoord["siteInternal"]]=$mongoSiteId;
}

$consoleTxt.=consoleMessage("info", $nbUpdate." site(s) updated to collection ".$typeO);

if ($server=="PROD") $link=$linkBioSite["PROD"];
else $link=$linkBioSite["DEV"];
foreach($arrSiteIdOk as $siteInternal => $mongoSiteId) {
	$final_result.='Site '.$siteInternal.' successfully imported => <a target="_blank" href="'.$link.$mongoSiteId.'">LINK TO BIOCOLLECT</a><br>';
}




if ($finalOk && !$debug) {
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

?>