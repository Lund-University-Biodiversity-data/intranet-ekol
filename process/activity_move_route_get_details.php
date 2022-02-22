<?php

$consoleTxt.=consoleMessage("info", "Get activity and output details for $activityIdToFix");

$filter = ['activityId' => $activityIdToFix];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.activity", $query);

foreach ($rows as $row){
    $activityDetails=$row;
    $okAct=true;

    $filter = ['activityId' => $activityIdToFix];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options); 

    $rowsOutput = $mng->executeQuery("ecodata.output", $query);

    foreach ($rowsOutput as $rowOutput){
        $outputDetails=$rowOutput;
    }
}


if ($okAct) {

    $siteOldWrongId=$row->siteId;

    $filter = ['siteId' => $siteOldWrongId];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options); 

    $rows = $mng->executeQuery("ecodata.site", $query);

    foreach ($rows as $row){
        $siteOldWrongDetails=$row;
        $okSite=true;
    }
    if ($okSite) {

        $sitesMongo = getArraySitesFromMongo ($activityDetails->projectId, $server);
        // sort by internalsiteid
        ksort($sitesMongo);

        $consoleTxt.=consoleMessage("info", count($sitesMongo)." site(s) found in database for project ".$activityDetails->projectId);


    }
    else {
        $consoleTxt.=consoleMessage("error", "No site found with that id");
    }



    
}
else {
    $consoleTxt.=consoleMessage("error", "No activity found with that id");
}

?>