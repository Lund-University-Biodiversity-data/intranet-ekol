<?php

$consoleTxt.=consoleMessage("info", "1) Get number of booked sites for each protocol");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$recapBookedSites=array();

foreach ($arrProtocol as $protocol) {
	$projectId=$commonFields[$protocol]["projectId"];

	$filter = [
		'status' => 'active', 
		'bookedBy' => [ '$exists' => true, '$ne' => null ],
		'projectId' => $projectId
	];
	$options = [];
	$query = new MongoDB\Driver\Query($filter, $options); 

	$rows = $mng->executeQuery("ecodata.site", $query);
	$results=$rows->toArray();

	$consoleTxt.=consoleMessage("info", count($results)." booked active site(s) for protocol ".$protocol);

	$recapBookedSites[$protocol]=count($results);

}


?>