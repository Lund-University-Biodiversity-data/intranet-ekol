<?php

$startime=time();

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

foreach ($array_sites as $indexSite => $dataSite) {

	//echo "search output for ".$indexSite."<br>";
	$filter = ['data.location' => $dataSite["locationID"]];
	$options = ['sort' => ['data.surveyDate' => -1], 'limit' => 1];
	$query = new MongoDB\Driver\Query($filter, $options); 

	$rows = $mng->executeQuery("ecodata.output", $query);

	foreach ($rows as $row){
		//echo "site :".$indexSite."<br>";
		//echo "site :".$row->data->surveyDate."<br>";
		if (trim($row->data->surveyDate)!="")
		$arrRecap[$indexSite]["lastYearSurveyed"]=substr($row->data->surveyDate, 0, 4);

	}

}
$endtime=time();

$consoleTxt.=consoleMessage("info", ($endtime-$startime)." sec to process" );

?>
