<?php

$consoleTxt.=consoleMessage("info", "Get the recap of sites/comments/years");

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);
$okCon=true;

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
            'actID.projectActivityId'=> $commonFields["std"]["projectActivityId"],
            'actID.status' => [ '$ne' => 'deleted' ],
            'actID.verificationStatus' => [ '$nin' => [ 'draft', 'not approved' ] ]
        ]],
        ['$project'=>[
            "data.isGpsUsed" => 1,
            "data.eventRemarks" => 1,
            "data.comments" => 1,
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
            "data.isGpsUsed" => 1,
            "data.eventRemarks" => 1,
            "data.comments" => 1,
            "data.surveyDate" => 1,
            "actID.siteId" => 1,
            "siteID.adminProperties.internalSiteId" => 1,
            "activityId" => 1
        ]],
        ['$limit'=> 20]
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

$tabSitesPeriod=array();

if ($okCon) {

    $consoleTxt.=consoleMessage("info","MongoDB ok to ".$mongoConnection[$server]);

    $response = $cursor->toArray();
    //$consoleTxt.=consoleMessage("info", count($response[0]->result)." surveys in the database for scheme ".$protocol);
    $consoleTxt.=consoleMessage("info", count($response)." active surveys");

    
    //foreach ($response[0]->result as $document) {
    foreach ($response as $document) {

        $year=substr($document->data->surveyDate, 0, 4);
        $siteId=$document->siteID->adminProperties->internalSiteId;

        $tabSitesPeriod[$siteId][$year]["activityId"]=$document->activityId;
        $tabSitesPeriod[$siteId][$year]["eventRemarks"]=$document->data->eventRemarks;
        $tabSitesPeriod[$siteId][$year]["eventRemarks"]=$document->data->eventRemarks;
        $tabSitesPeriod[$siteId][$year]["comments"]=$document->data->comments;
        $tabSitesPeriod[$siteId][$year]["isGpsUsed"]=$document->data->isGpsUsed;

    }


    $consoleTxt.=consoleMessage("info", count($tabSitesPeriod)." lines to add to the file");

}




// excel creation
//fputcsv($fp, $line, ";");
?>