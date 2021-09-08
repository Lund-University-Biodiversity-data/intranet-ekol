<?php

$consoleTxt.=consoleMessage("info", "3) Get last surveyed years for protocol ".$protocol);


$startime=time();

//foreach ($arrSitesDetails as $indexSite => $dataSite) {

	//echo "search output for ".$indexSite."<br>";
	$filter = ['data.location' => ['$in' => $arrSites]];
	//$dataSite["locationID"]
	//$options = ['sort' => ['data.surveyDate' => -1]];
	$options = [];
	$query = new MongoDB\Driver\Query($filter, $options); 

	$rows = $mng->executeQuery("ecodata.output", $query);

	foreach ($rows as $row){
		//echo "site :".$indexSite."<br>";
		//echo "site :".$row->data->surveyDate."<br>";
		if (trim($row->data->surveyDate)!="") {
			$yr=substr($row->data->surveyDate, 0, 4);
			if ($arrRecap[$arrSitesInternal[$row->data->location]]["lastYearSurveyed"]=="" || $arrRecap[$arrSitesInternal[$row->data->location]]["lastYearSurveyed"]<$yr) {
				$arrRecap[$arrSitesInternal[$row->data->location]]["lastYearSurveyed"]=$yr;
				//echo "new year for ".$arrSitesInternal[$row->data->location]." : ".$yr."<br>";
			}
		}

	}

//}
$endtime=time();

$consoleTxt.=consoleMessage("info", ($endtime-$startime)." sec to process" );

?>
