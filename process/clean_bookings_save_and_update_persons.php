<?php

$consoleTxt.=consoleMessage("info", "1) Save the bookings and update the persons");

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
	fwrite($handle, "siteId#siteName#personId\n");

	$nbL=0;
	$nbPModified=0;
	foreach($rows as $row) {
		fwrite($handle, $row->siteId."#".$row->name."#".$row->bookedBy."\n");
		$nbL++;


		$filter = [
			'personId' => $row->bookedBy
		];
		$options = ['$pull' => ['bookedSites' => $row->siteId]];
		$updateOptions = [];

		$bulk = new MongoDB\Driver\BulkWrite;

		$bulk->update($filter, $options, $updateOptions); 
		$result = $mng->executeBulkWrite('ecodata.person', $bulk);

		if ($result) {
			$nbPModified+=$result->getModifiedCount();
			if ($result->getModifiedCount()!=1)
				$consoleTxt.=consoleMessage("warn", $result->getModifiedCount()." person(s) modified instead of 1 for person ".$row->bookedBy);
		}
		else {
			$okSaveUpdatePersons=false;
			$consoleTxt.=consoleMessage("error", "Can't udate person ".$row->bookedBy);
		}

	}

	$consoleTxt.=consoleMessage("info", "Save complete with ".$nbL." lines in ".$pathSave);
	$consoleTxt.=consoleMessage("info", "Update of persons complete with ".$nbPModified." person(s) updated");

	fclose($handle);
}
else {
	$okSaveUpdatePersons=false;
	$consoleTxt.=consoleMessage("error", "Can't write in ".$pathSave);
}

?>