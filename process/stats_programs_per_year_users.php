<?php
// 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

$okCon=true;

$dateFrom = $inputYearStart."-01-01";
echo $dateFrom;
/*
db.output.aggregate([
  {
    $lookup: {
      from: "activity",
      localField: "activityId",
      foreignField: "activityId",
      as: "actID"
    }
  },
  {
    $unwind: "$actID"
  },
  {
    $match: {
      "actID.status": { $ne: "deleted" },
      "status": { $ne: "deleted" },
      "actID.verificationStatus": "approved"
    }
  },
  {
    $addFields: {
      surveyYear: {
        $year: {
          $toDate: "$data.surveyDate"
        }
      }
    }
  },
  {
    $group: {
      _id: {
        dataOrigin: "$dataOrigin",
        year: "$surveyYear"
      },
      count: { $sum: 1 }
    }
  },
  {
    $sort: {
      "_id.dataOrigin": 1,
      "_id.year": 1
    }
  }
])
*/


// get all the outputs counted group by year and scriptOrigin 
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
            'actID.status' => [ '$ne' => 'deleted' ],
            "status" => [ '$ne' => 'deleted' ],
            'actID.verificationStatus' => "approved",
            'data.surveyDate' => [ '$gte' => $dateFrom ]            
        ]],
        ['$addFields'=>[
            "surveyYear" => [
                '$year' => [
                    '$toDate' => '$data.surveyDate'
                ]
            ]
        ]],
        ['$group'=>[
            "_id" => [
                "dataOrigin" => '$dataOrigin', 
                "year" => '$surveyYear'
            ],
            "count" => [ '$sum' => 1 ]
        ]],
        ['$sort' => [
          "_id.year"=> 1, 
          "_id.dataOrigin"=> 1
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
    $consoleTxt.=consoleMessage("info", count($response)." lines year/scriptOrigin");

    $matrixYearOrigin=array();
    $listOrigin=array();
    $listYear=array();
    $totalOrigin=array();
    $totalOrigin["total"]=0;
    //foreach ($response[0]->result as $document) {
    foreach ($response as $document) {
        if (trim($document->_id->dataOrigin)=="") $origin="BioCollect";
        else $origin=$document->_id->dataOrigin;

        if (!isset($matrixYearOrigin[$document->_id->year]))
            $matrixYearOrigin[$document->_id->year]["total"]=0;
        if (!isset($totalOrigin[$origin])) 
            $totalOrigin[$origin]=0;


        $matrixYearOrigin[$document->_id->year][$origin]=$document->count;
        $matrixYearOrigin[$document->_id->year]["total"]+=$document->count;
        $totalOrigin[$origin]+=$document->count;
        $totalOrigin["total"]+=$document->count;

        if (!in_array($origin, $listOrigin))
            $listOrigin[]=$origin;
        if (!in_array($document->_id->year, $listYear))
            $listYear[]=$document->_id->year;
    }

    asort($listOrigin);
    $consoleTxt.=consoleMessage("info", count($matrixYearOrigin)." different years");
    $consoleTxt.=consoleMessage("info", count($listOrigin)." different dataorigin");

}

?>