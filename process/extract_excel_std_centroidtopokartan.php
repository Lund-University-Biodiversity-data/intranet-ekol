<?php
$consoleTxt.=consoleMessage("info", "Get the centroid topokartan objects");

$mng = new MongoDB\Driver\Manager($mongoConnection[ENVIRONMENT]);


$filter = [];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.internalCentroidTopokartan", $query);

$arrTopo=array();

foreach ($rows as $row){

    $topo=array();

    $topo["id"]=$row->id;

    $topo["karta"]=$row->karta;
    $topo["kartatx"]=$row->kartatx;
    $topo["rt90n"]=$row->rt90n;
    $topo["rt90o"]=$row->rt90o;
    $topo["wgs84_lat"]=$row->wgs84_lat;
    $topo["wgs84_lon"]=$row->wgs84_lon;
    $topo["sweref99_n"]=$row->sweref99_n;
    $topo["sweref99_o"]=$row->sweref99_o;

    $arrTopo[]=$topo;

}

$consoleTxt.=consoleMessage("info", count($arrTopo)." lines to add to the file");