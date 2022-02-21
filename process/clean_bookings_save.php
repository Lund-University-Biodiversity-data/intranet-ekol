<?php

$consoleTxt.=consoleMessage("info", "1) Save the bookings");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$projectId=$commonFields[$protocol]["projectId"];

$filter = [
	'status' => 'active', 
	'bookedBy' => [ '$exists' => true, '$ne' => null, '$ne' => "" ],
	'projects' => $projectId
];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.site", $query);

$filenameSave=date("Y-m-d-H-i-s")."_".$protocol."_saveBookings.log";
$pathSave=PATH_SAVE_BOOKINGS.$filenameSave;

if ($handle=fopen($pathSave, "w")) {
	$nbL=0;
	foreach($rows as $row) {
		fwrite($handle, $row->siteId."#".$row->name."#".$row->bookedBy."\n");
		$nbL++;
	}

	$consoleTxt.=consoleMessage("info", "Writing complete with ".$nbL." lines");

	$okSave=true;
	fclose($handle);
}
else {
	$consoleTxt.=consoleMessage("error", "Can't write in ".$pathSave);
}

?>