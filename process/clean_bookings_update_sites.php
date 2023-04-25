<?php

$consoleTxt.=consoleMessage("info", "2) execute the command to empty the bookings");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if (count($mng->getServers())==1) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

$projectId=$commonFields[$protocol]["projectId"];

$filter = [
	'status' => 'active', 
	'bookedBy' => [ '$exists' => true, '$ne' => null, '$ne' => "" ],
	'projects' => $projectId
];
$options = ['$set' => ['bookedBy' => ""]];
$updateOptions = ['multi' => 1];

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update($filter, $options, $updateOptions); 
$result = $mng->executeBulkWrite('ecodata.site', $bulk);

if ($result) {
	$okUpdate=true;

	$consoleTxt.=consoleMessage("info", $result->getModifiedCount()." row(s) modified");

}
else {
	$consoleTxt.=consoleMessage("error", "Can't run udate query");
}

?>