<?php
$consoleTxt.=consoleMessage("info", "Get the centroid std coord objects");

$mng = new MongoDB\Driver\Manager($mongoConnection[ENVIRONMENT]);


$filter = [];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options); 

$rows = $mng->executeQuery("ecodata.internalCentroidStdCoordinates", $query);

$arrTopo=array();

foreach ($rows as $row){

    $topo=array();

    $topo["id"]=$row->id;

    $topo["karta"]=$row->karta;
    $topo["kartatx"]=$row->kartatx;
    $topo["mitt_rt90_n"]=$row->mitt_rt90_n;
    $topo["mitt_rt90_o"]=$row->mitt_rt90_o;
    $topo["p1_rt90_n"]=$row->p1_rt90_n;
    $topo["p1_rt90_o"]=$row->p1_rt90_o;
    $topo["mitt_wgs84_lat"]=$row->mitt_wgs84_lat;
    $topo["mitt_wgs84_lon"]=$row->mitt_wgs84_lon;
    $topo["p1_wgs84_lat"]=$row->p1_wgs84_lat;
    $topo["p1_wgs84_lon"]=$row->p1_wgs84_lon;
    $topo["p1_sweref99_n"]=$row->p1_sweref99_n;
    $topo["p1_sweref99_o"]=$row->p1_sweref99_o;
    $topo["mitt_sweref99_n"]=$row->mitt_sweref99_n;
    $topo["mitt_sweref99_o"]=$row->mitt_sweref99_o;

    $arrTopo[]=$topo;

}

$consoleTxt.=consoleMessage("info", count($arrTopo)." lines to add to the file");