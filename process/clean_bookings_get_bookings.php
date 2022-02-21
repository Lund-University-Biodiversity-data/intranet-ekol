<?php

$consoleTxt.=consoleMessage("info", "**Get number of booked sites for each protocol**");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$recapBookedSites=array();

foreach ($arrProtocol as $protocol) {
	$projectId=$commonFields[$protocol]["projectId"];

	$filter = [
		'status' => 'active', 
		'bookedBy' => [ '$exists' => true, '$ne' => null, '$ne' => "" ],
		'projects' => $projectId
	];
	$options = [];
	$query = new MongoDB\Driver\Query($filter, $options); 

	$rows = $mng->executeQuery("ecodata.site", $query);
	$results=$rows->toArray();

	$consoleTxt.=consoleMessage("info", count($results)." booked active site(s) for protocol ".$protocol);

	$recapBookedSites[$protocol]=count($results);

}


?>