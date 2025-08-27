<?php
// 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

$okCon=true;

$dateFrom = (date("Y")-$inputNbYears+1)."-01-01";

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
            'actID.status' => [ '$ne' => 'deleted' ],
            'actID.verificationStatus' => [ '$nin' => [ 'draft', 'not approved' ] ],
            'data.surveyDate' => [ '$gte' => $dateFrom ]
        ]],
        ['$project'=>[
        	"data.period" => 1,
	        "data.surveyDate" => 1,
            "year" => [ '$year' =>  ['$toDate' => '$data.surveyDate' ]],
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
            "actID.projectActivityId" => 1,
        	"data.period" => 1,
	        "data.surveyDate" => 1,
            "year" => 1,
	        "actID.siteId" => 1,
        	"siteID.adminProperties.internalSiteId" => 1,
            "siteID.adminProperties.lan" => 1,
            "siteID.adminProperties.lsk" => 1,
            "activityId" => 1
        ]],
        ['$group'=>[
            "_id" => ["year" => '$year', "geoarea" => '$siteID.adminProperties.'.$inputGeoObject],
            "count" => [ '$sum' => 1 ]
        ]],
        ['$sort' => [
          "_id"=> 1
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
    $consoleTxt.=consoleMessage("info", count($response)." lines year/".$inputGeoObject." for scheme ".$protocol);

    $matrixLanYear=array();
    $listArea=array();
    $listYear=array();
    //foreach ($response[0]->result as $document) {
    foreach ($response as $document) {
        if (!isset($matrixLanYear[$document->_id->geoarea]))
            $matrixLanYear[$document->_id->geoarea]["total"]=0;

        $matrixLanYear[$document->_id->geoarea][$document->_id->year]=$document->count;
        $matrixLanYear[$document->_id->geoarea]["total"]+=$document->count;

        if (!in_array($document->_id->geoarea, $listArea))
            $listArea[]=$document->_id->geoarea;
        if (!in_array($document->_id->year, $listYear))
            $listYear[]=$document->_id->year;
    }


//print_r($matrixLanYear);
    $consoleTxt.=consoleMessage("info", count($matrixLanYear)." different years for scheme ".$protocol);
    $consoleTxt.=consoleMessage("info", count($listArea)." different ".$inputGeoObject." for scheme ".$protocol);

}

// END 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

?>