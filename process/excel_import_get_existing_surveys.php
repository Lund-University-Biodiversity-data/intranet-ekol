<?php
// 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

$okCon=true;


// get all the output for a dedicated scheme 
$commands = [ 
    'aggregate' => "output", 
    'pipeline' =>[
        ['$lookup'=>[
        	'from'=>'activity',
        	'localField'=>'activityId',
        	'foreignField'=>'activityId',
        	'as'=>'actID'
        ]],
        ['$unwind'=> '$activityId'],
        ['$match'=>[
        	'actID.projectActivityId'=> $commonFields[$protocol]["projectActivityId"],
            'actID.status' => [ '$ne' => 'deleted' ]
        ]],
        ['$project'=>[
        	"data.period" => 1,
	        "data.surveyDate" => 1,
	        "actID.siteId" => 1,
            "activityId" => 1
        ]],
        ['$lookup'=>[
        	'from'=>'site',
        	'localField'=>'actID.siteId',
        	'foreignField'=>'siteId',
        	'as'=>'siteID'
        ]],
        ['$unwind'=> '$siteID'],
        ['$project'=>[
        	"data.period" => 1,
	        "data.surveyDate" => 1,
	        "actID.siteId" => 1,
        	"siteID.adminProperties.internalSiteId" => 1,
            "activityId" => 1
        ]],
        /*['$limit'=> 20],*/
    ],
    'cursor' => new stdClass,
];

$command = new MongoDB\Driver\Command($commands);

try{
    $cursor = $mng->executeCommand("ecodata", $command);

}
catch(Exception $e){
    $consoleTxt.=consoleMessage("error","MongoDB Connection Error ".$mongoConnection[$server]);
    $okCon=false;
}

if ($okCon) {

    $consoleTxt.=consoleMessage("info","MongoDB ok to ".$mongoConnection[$server]);

    $response = $cursor->toArray();
    //$consoleTxt.=consoleMessage("info", count($response[0]->result)." surveys in the database for scheme ".$protocol);
    $consoleTxt.=consoleMessage("info", count($response)." surveys in the database for scheme ".$protocol);

    $tabSitesPeriod=array();
    //foreach ($response[0]->result as $document) {
    foreach ($response as $document) {

        $year=substr($document->data->surveyDate, 0, 4);

        // specific for the year in punkturutter
        if ($protocol=="sommar" || $protocol=="vinter") {
            // year -1 if before june
            if (substr($document->data->surveyDate, 5, 2)<6) {
                $year=substr($document->data->surveyDate, 0, 4)-1;
                //echo "mois decoupé :".substr($document->data->surveyDate, 5, 2)." => year : ".$year."<br>";
            }
        }

        $periodDoc=$document->data->period;
        $siteId=$document->siteID->adminProperties->internalSiteId;
        //var_dump($document);

        if ($protocol=="vinter" || $protocol=="natt")
            $tabSitesPeriod[$siteId][$year."-".intval($periodDoc)]=$document->activityId;
        elseif ($protocol=="sommar" || $protocol=="std")
            $tabSitesPeriod[$siteId][$year]=$document->activityId;

    }

    $consoleTxt.=consoleMessage("info", count($tabSitesPeriod)." different sites surveyed for scheme ".$protocol);

}

// END 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

?>