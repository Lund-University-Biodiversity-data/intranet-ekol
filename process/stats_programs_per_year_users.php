<?php
// 1** GET ALL THE EXISTING SURVEYS DATE/SITE FROM THE DATABASE

$okCon=true;

$dateFrom = $inputYearStart."-01-01";
/*
db.activity.aggregate([
  {
    $match: {
      status : { $ne : "deleted" },
      verificationStatus : "approved",
      $expr: {
        $gt: [
          { $year: "$dateCreated" }, 2021
        ]
      }
    }
  },
{
$group: {
_id: {
year: { $year: "$dateCreated" }
},
users: { $addToSet: "$userId" }
}
},
{
    $project: {
        _id: 0,
        year: "$_id.year",
        distinctUserCount: { $size: "$users" }
    }
},
{
    $sort: { year: 1 }
}
])

*/


// get all the outputs counted group by year and scriptOrigin 
$commands = [ 
    'aggregate' => "activity", 
    'pipeline' =>[
        ['$match'=>[
            "status" => [ '$ne' => 'deleted' ],
            'verificationStatus' => "approved",
            '$expr' => [
                '$gt' => [
                  [ '$year' => '$dateCreated' ], (int)$inputYearStart                  
                ]
            ]  
        ]],
        ['$group'=>[
            "_id" => [
                "year" => [ '$year' => '$dateCreated' ]
            ],
            'users' => [ '$addToSet' => '$userId' ]
        ]],
        ['$project'=>[
            "_id" => 0,
            'year' => '$_id.year',
            'distinctUserCount' => [ '$size' => '$users' ] 
        ]],
        ['$sort' => [
          "year"=> 1, 
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

    $matrixYearUser=array();

    foreach($listYear as $year) {
        $matrixYearUser[$year]=0;
    }
    $totalOrigin["total"]=0;

    foreach ($response as $document) {
        $matrixYearUser[$document->year]=$document->distinctUserCount;
    }

    $consoleTxt.=consoleMessage("info", count($matrixYearUser)." different yearsorigin");

}

?>