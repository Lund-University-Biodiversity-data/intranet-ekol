<?php
$consoleTxt.=consoleMessage("info", "Get the punkt 20 pts objects");

$mng = new MongoDB\Driver\Manager($mongoConnection[ENVIRONMENT]);


$filter = [];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.internalPunkt20Pts", $query);

$arrPoint=array();

foreach ($rows as $row){

    $pointData=array();

    $pointData["id"]=$row->id;

    $pointData["internalSiteId"]=$row->internalSiteId;
    $pointData["pointNumber"]=$row->pointNumber;
    $pointData["pointName"]=$row->pointName;
    $pointData["anonymousPointID"]=$row->anonymousPointID;
    $pointData["countyName"]=$row->countyName;
    $pointData["countySCB"]=$row->countySCB;
    $pointData["municipality"]=$row->municipality;
    $pointData["StnRegOSId"]=$row->StnRegOSId;
    $pointData["StnRegPPId"]=$row->StnRegPPId;

    $arrPoint[]=$pointData;

}

$consoleTxt.=consoleMessage("info", count($arrPoint)." lines to add to the file");