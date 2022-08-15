<?php

$consoleTxt.=consoleMessage("info", "Get the recap of sites/comments/years");

$mng = new MongoDB\Driver\Manager($mongoConnection[ENVIRONMENT]);

$okCon=true;


$tabSurveyorsYears=array();

for ($iLoop=1;$iLoop<=2;$iLoop++) {

    // get all the persons involved as personId
    $commands = [ 
        'aggregate' => "output", 
        'pipeline' =>[
            ['$lookup'=>[
                'from'=>'activity',
                'localField'=>'activityId',
                'foreignField'=>'activityId',
                'as'=>'act'
            ]],
            ['$match'=>[
                "act.projectActivityId" => $commonFields["std"]["projectActivityId"], // can be removed later if protocol = all
                "act.status" => [
                    '$in' => ["active"]
                ],
                "act.verificationStatus" => [
                    '$in' => ["approved", "under review"]
                ]
            ]],
            ['$lookup'=>[
                'from'=>'person',
                'localField'=>'act.personId',
                'foreignField'=>'personId',
                'as'=>'pers'
            ]],
            ['$unwind'=> '$pers'],
            ['$match'=>[
                "pers.personId" => ['$exists'=> 1] // useless but if the match is empty it doesn't work
                // will add later inthe code the hub=sft if protocol=all
            ]],
            ['$lookup'=>[
                'from'=>'site',
                'localField'=>'act.siteId',
                'foreignField'=>'siteId',
                'as'=>'siteID'
            ]],
            ['$unwind'=> '$siteID'],
            ['$project'=>[
                "activityId" => 1,
                "data.surveyDate" => 1,
                "siteID.adminProperties.internalSiteId" => 1,
                "pers.personId" => 1,
                "name" => 1,
                "pers.internalPersonId" => 1,
                "pers.firstName" => 1,
                "pers.lastName" => 1,
                "pers.userId" => 1,
            ]]
        ],
        'cursor' => new stdClass,
    ];

    // for the 2nd round, remove the link to activity based on 
    if ($iLoop==2) {
        $aggregate["pipeline"][2]['$lookup']['localField']='act.helperIds';
    }

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
        $consoleTxt.=consoleMessage("info", count($response)." survey(s)");

        
        //foreach ($response[0]->result as $document) {
        foreach ($response as $document) {

            // get the date and fix it if needed with timezone
            $eventDate=getEventDateAfterTimeZone($document->data->surveyDate);

            // get the year and fix it based on the protocol
            $year=getYearFromSurveyDateAndProtocol($eventDate, $protocol);

            $okOutput=true;
            if (is_numeric($yrStart) && $yrStart>$year) $okOutput=false;
            if (is_numeric($yrEnd) && $yrEnd<$year) $okOutput=false;


            if ($okOutput) {
                $row=array();

                $row["person"]=$document->pers->internalPersonId;
                $row["site"]=$document->siteID->adminProperties->internalSiteId;
                $row["year"]=$year;
                $row["huvud"]=($iLoop==1 ? "ja" :"");
                $row["med"]=($iLoop==2 ? "ja" :"");

                $tabSurveyorsYears[]=$row;
            }

        }



    }
    $consoleTxt.=consoleMessage("info", count($tabSurveyorsYears)." lines to add to the file");

}



// excel creation
//fputcsv($fp, $line, ";");
?>