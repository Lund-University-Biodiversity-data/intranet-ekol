<?php

$consoleTxt.=consoleMessage("info", "3) Get last surveyed years for protocol ".$protocol);


$startime=time();

//foreach ($arrSitesDetails as $indexSite => $dataSite) {

$commands = [ 
    'aggregate' => "output", 
    'pipeline' =>[
    	['$match' => [
    		'data.location' => ['$in' => $arrSites]
    	]],
        ['$lookup'=>[
        	'from'=>'activity',
        	'localField'=>'activityId',
        	'foreignField'=>'activityId',
        	'as'=>'actID'
        ]],
        ['$unwind'=> '$activityId'],
        ['$project'=>[
        	"data.period" => 1,
	        "data.surveyDate" => 1,
	        "data.location" => 1,
	        "actID.verificationStatus" => 1,
            "activityId" => 1
        ]],
        /*['$limit'=> 20],*/
    ]
];

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

$command = new MongoDB\Driver\Command($commands);

$cursor = $mng->executeCommand("ecodata", $command);
$response = $cursor->toArray();

foreach ($response[0]->result as $document) {

	if (trim($document->data->surveyDate)!="") {
		$yr=substr($document->data->surveyDate, 0, 4);
		if ($arrRecap[$arrSitesInternal[$document->data->location]]["lastYearSurveyed"]=="" || $arrRecap[$arrSitesInternal[$document->data->location]]["lastYearSurveyed"]<$yr) {
			$arrRecap[$arrSitesInternal[$document->data->location]]["lastYearSurveyed"]=$yr;
			$arrRecap[$arrSitesInternal[$document->data->location]]["lastYearSurveyedStatus"]=$document->actID[0]->verificationStatus;

		}
	}



}



$endtime=time();

$consoleTxt.=consoleMessage("info", ($endtime-$startime)." sec to process" );



/*


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

			// get the verificationStatus of the activityId
			$queryAct = new MongoDB\Driver\Query(["activityId" => $row->activityId], $options); 
			$rowsAct = $mng->executeQuery("ecodata.activity", $queryAct);
			foreach ($rowsAct as $rowAct){
				$arrRecap[$arrSitesInternal[$row->data->location]]["lastYearSurveyedStatus"]=$rowAct->verificationStatus;
			}

			
			//echo "new year for ".$arrSitesInternal[$row->data->location]." : ".$yr."<br>";
		}
	}

}

//}
$endtime=time();

$consoleTxt.=consoleMessage("info", ($endtime-$startime)." sec to process" );
*/
?>
