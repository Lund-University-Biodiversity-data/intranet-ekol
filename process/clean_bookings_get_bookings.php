<?php

$consoleTxt.=consoleMessage("info", "**Get number of booked sites for each protocol**");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if ($mng) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

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



/*

> db.site.find({name:"12HNV - NATT"},{siteId:1})
{ "_id" : ObjectId("6062ee581c5c6e492d087bd1"), "siteId" : "c7f4ec3d-7670-9cbb-5197-700a429b0417" }
> db.person.find({bookedSites:"c7f4ec3d-7670-9cbb-5197-700a429b0417"}).count()
1
> db.person.find({bookedSites:"c7f4ec3d-7670-9cbb-5197-700a429b0417"},{personId:1})
{ "_id" : ObjectId("5f77502a4f0ccf943f8de57f"), "personId" : "aab3345e-efb6-45bc-97e5-559d3982864c" }




db.person.update({"personId" : "aab3345e-efb6-45bc-97e5-559d3982864c"},
{'$pull' : {bookedSites:"c7f4ec3d-7670-9cbb-5197-700a429b0417"}})
*/

?>