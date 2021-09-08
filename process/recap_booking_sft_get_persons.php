<?php

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$filter = ['personId' => ['$in' => $arrPersons]];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.person", $query);

foreach ($rows as $row){
	$arrPersonsDetails[$row->personId]["name"]=$row->firstName." ".$row->lastName;
	$arrPersonsDetails[$row->personId]["email"]=$row->email;
}


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
