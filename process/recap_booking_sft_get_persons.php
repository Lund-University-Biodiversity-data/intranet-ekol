<?php

$consoleTxt.=consoleMessage("info", "2) Get surveyors for protocol ".$protocol);

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
if (count($mng->getServers())==1) $consoleTxt.=consoleMessage("info", "Connection to mongoDb ok");
else $consoleTxt.=consoleMessage("error", "No connection to mongoDb");

$filter = ['personId' => ['$in' => $arrPersons]];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.person", $query);

foreach ($rows as $row){
	$arrPersonsDetails[$row->personId]["name"]=$row->firstName." ".$row->lastName;
	$arrPersonsDetails[$row->personId]["email"]=$row->email;
}


$consoleTxt.=consoleMessage("info", count($arrPersonsDetails)." person(s).");

// check no missing data
foreach ($arrPersons as $person){

	if (!isset($arrPersonsDetails[$person]["name"]))	{
		echo "No data found for person ".$person;

		$consoleTxt.=consoleMessage("error", "No data found for person ".$person);
		$arrPersonsDetails[$person]["name"]="ERROR - see console for details";
		$arrPersonsDetails[$person]["email"]="ERROR - see console for details";
	}

}

?>
