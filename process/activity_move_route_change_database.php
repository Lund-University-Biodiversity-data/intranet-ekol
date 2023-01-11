<?php



$filter = ['siteId' => $siteNewCorrectId];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.site", $query);

foreach ($rows as $row){
    $siteNewDetails=$row;
    $okSite=true;
}
if ($okSite) {


    $consoleTxt.=consoleMessage("info", "Ready to change the data with $siteNewCorrectId");

    //if (!isset($siteNewDetails->geoIndex->coordinates[0]) || !is_numeric($siteNewDetails->geoIndex->coordinates[0])  || !isset($siteNewDetails->geoIndex->coordinates[1]) || !is_numeric($siteNewDetails->geoIndex->coordinates[1]) ) {
    if (!isset($siteNewDetails->extent->geometry->coordinates[0]) || !is_numeric($siteNewDetails->extent->geometry->coordinates[0])  || !isset($siteNewDetails->extent->geometry->coordinates[1]) || !is_numeric($siteNewDetails->extent->geometry->coordinates[1]) ) {
        $consoleTxt.=consoleMessage("info", "No coordinates found in extent.geometry.coordinates for the new site");
    }

    else {

        $longitude=$siteNewDetails->extent->geometry->coordinates[0];
        $latitude=$siteNewDetails->extent->geometry->coordinates[1];

        $bulkP = new MongoDB\Driver\BulkWrite;
        //$filter = [];
        $filter = ['activityId' => $activityIdToFix];
        $options =  ['$set' => ['siteId' => $siteNewCorrectId]];
        $updateOptions = [];
        $bulkP->update($filter, $options, $updateOptions); 
        $result = $mng->executeBulkWrite('ecodata.activity', $bulkP);

        $consoleTxt.=consoleMessage("info", $result->getModifiedCount()." activity updated");


        $bulkP = new MongoDB\Driver\BulkWrite;
        //$filter = [];
        $filter = ['activityId' => $activityIdToFix];
        $options =  ['$set' => [
            "data.location" => $siteNewCorrectId,
            "data.locationLongitude" => $longitude,
            "data.locationHiddenLongitude" => $longitude,
            "data.locationLatitude" => $latitude,
            "data.locationHiddenLatitude" => $latitude,
        ]];
        $updateOptions = [];
        $bulkP->update($filter, $options, $updateOptions); 
        $result = $mng->executeBulkWrite('ecodata.output', $bulkP);

        $consoleTxt.=consoleMessage("info", $result->getModifiedCount()." output updated");

        $bulkP = new MongoDB\Driver\BulkWrite;
        //$filter = [];
        $filter = ['activityId' => $activityIdToFix];
        $options =  ['$set' => [
            'locationID' => $siteNewCorrectId,
            'decimalLatitude' => $latitude,
            'decimalLongitude' => $longitude,
        ]];
        $updateOptions = ['multi' => true];
        $bulkP->update($filter, $options, $updateOptions); 
        $result = $mng->executeBulkWrite('ecodata.record', $bulkP);

        $consoleTxt.=consoleMessage("info", $result->getModifiedCount()." record(s) updated");
        $finalOk=true;


        include "process/activity_move_route_get_details.php";
    }


}
else {
    include "process/activity_move_route_get_details.php";

    $consoleTxt.=consoleMessage("error", "No site found with that id $siteNewCorrectId");
}


?>