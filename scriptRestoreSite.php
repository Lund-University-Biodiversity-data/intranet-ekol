<?php

$pathSave="/home/mathieu/Documents/repos/intranet-ekol/bookings-save/2022-02-22-08-24-24_natt_saveBookings.log";

$mng = new MongoDB\Driver\Manager("mongodb://localhost");


if ($handle=fopen($pathSave, "r")) {
	echo "read\n";
	$nbL=0;
	$nbSModified=0;
	while (!feof($handle)) {
		$nbL++;
	    $line=fgets($handle);

	    $explLine=explode("#", $line);
	    if (count($explLine)==3) {
	    	$siteId=$explLine[0];
		    $personId=$explLine[2];

		    if ($siteId!="" && $personId!="") {
		    	$filter = [
					'siteId' => $siteId
				];
				$options = ['$set' => ['bookedBy' => $personId]];
				$updateOptions = [];

				$bulk = new MongoDB\Driver\BulkWrite;

				$bulk->update($filter, $options, $updateOptions); 
				$result = $mng->executeBulkWrite('ecodata.site', $bulk);

				if ($result) {
					$nbSModified+=$result->getModifiedCount();
					if ($result->getModifiedCount()!=1)
						echo $result->getModifiedCount()." site(s) modified instead of 1 for site ".$siteId."\n";
				}
				else {
					echo "ERROR : Can't udate site ".$siteId;
				}

		    }
		    else {
				echo "ERROR : empty site/person ".$siteId."-".$personId;
			}

	    }

	}

	echo $nbL." lines in the file\n";
	echo $nbSModified." site(s) modified\n";

}

?>