<?php

$listData=array();

$oneList=getListSpeciesFromModule(null, $listIdToCheck, "guid");

//print_r($oneList);

$consoleTxt.=consoleMessage("info", count($oneList)." element(s) in list ".$listIdToCheck);

$projectsScope=[
  $commonFields["std"]["projectId"],
  $commonFields["natt"]["projectId"],
  $commonFields["vinter"]["projectId"],
  $commonFields["sommar"]["projectId"],
  $commonFields["kust"]["projectId"],
  $commonFields["iwc"]["projectId"],
];
// get all the distinct guid obtained in biocollect
/*
db.output.aggregate([
  {'$lookup' : {
    'from':'activity',
    'localField':'activityId',
    'foreignField':'activityId',
    'as':'actID'
  }},
  {'$match':{
      'actID.status' : { '$ne' : 'deleted' },
      'actID.verificationStatus' : { '$nin' : [ 'draft', 'not approved' ]}
  }},
  {$unwind:"$data.observations"},
  {$unwind:"$data.observations.species"},
  {
    $group: {
      _id: ["$data.observations.species.name", "$data.observations.species.guid"],
      count: { $sum: 1 }
    }
  },
  {$sort:{
    _id:1
  }}
])
*/
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
        ['$match'=>[
          'actID.projectId' => [ '$in' => $projectsScope ],
          'actID.status' => [ '$ne' => 'deleted' ],
          'actID.verificationStatus' => [ '$nin' => [ 'draft', 'not approved' ] ]
        ]],
        ['$unwind'=> '$data.observations'],
        ['$unwind'=> '$data.observations.species'],
        ['$group'=>[
          "_id" => ['$data.observations.species.name', '$data.observations.species.guid'],
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

$nbErrors=0;
$nbDuplicates=0;
$nbDiff=0;

if ($okCon) {

  $consoleTxt.=consoleMessage("info","MongoDB ok to ".$mongoConnection[$server]);

  $response = $cursor->toArray();
  $consoleTxt.=consoleMessage("info", count($response)." pair(s) guid/swedishName in the database ");

  $tabRecap=array();
  //foreach ($response[0]->result as $document) {
  $oldGuid="";
  $oldSN="";
  foreach ($response as $document) {

    $idItem = $document->_id;
    $nbItem = $document->count;

    $swedishnameItem=$idItem[0];
    $guidItem=$idItem[1];

    if (!isset($tabRecap[$guidItem])) {
      $tabRecap[$guidItem]["items"]=array();
      $tabRecap[$guidItem]["guidDupl"]="no";
      $tabRecap[$guidItem]["guidDiff"]="no";
    }

    $tabRecap[$guidItem]["items"][$swedishnameItem]["nbItems"]=$nbItem;

    if (!isset($oneList[$guidItem])) {
      $consoleTxt.=consoleMessage("error", "The guid ".$guidItem." is not in the LA-list module");
      $nbErrors++;

      $tabRecap[$guidItem]["items"][$swedishnameItem]["art"]="MISSING";
      $tabRecap[$guidItem]["items"][$swedishnameItem]["scientificName"]="MISSING";
      $tabRecap[$guidItem]["items"][$swedishnameItem]["swedishName-lists"]="MISSING";
    }
    else {
      $tabRecap[$guidItem]["items"][$swedishnameItem]["art"]=$oneList[$guidItem]["art"];
      $tabRecap[$guidItem]["items"][$swedishnameItem]["scientificName"]=$oneList[$guidItem]["name"];
      $tabRecap[$guidItem]["items"][$swedishnameItem]["swedishName-lists"]=$oneList[$guidItem]["nameSWE"];
    }

    if ($guidItem==$oldGuid) {
      $nbDuplicates++;
      $tabRecap[$guidItem]["guidDupl"]="GUID-dupl";
    }

    if ($guidItem!=$oldGuid && $oldSN==$tabRecap[$guidItem]["items"][$swedishnameItem]["scientificName"]) {
      $nbDiff++;
      $tabRecap[$guidItem]["guidDiff"]="GUID-diff";
    }

    $oldGuid=$guidItem;
    $oldSN=$tabRecap[$guidItem]["items"][$swedishnameItem]["scientificName"];
  }
}


$consoleTxt.=consoleMessage("info", $nbDuplicates." duplicate(s) found to be fixed. Can be fixed with the \"Fix duplicates\" button");
$consoleTxt.=consoleMessage("info", $nbDiff." row(s) with same sientificName but different GUID found. Can only be fixed manually");
$consoleTxt.=consoleMessage("error", $nbErrors." error(s) found with items that don't exist in the species list");

$final_result.="<p>";
$final_result.=$nbDuplicates." duplicate(s) found to be fixed. Can be fixed with the \"Fix duplicates\" button.<br>";
$final_result.=$nbDiff." row(s) with same sientificName but different GUID found. Can only be fixed manually.<br>";
$final_result.=$nbErrors." error(s) found with items that don't exist in the species list.<br>";
$final_result.="</p>";
