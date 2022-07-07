<?php


$filename=date("Ymd-His")."_extract_stdCentroidTopokartan.csv";
$path_extract=PATH_OUTPUT_CSV."stdCentroidTopokartan/";

$headers=array("id", "karta", "kartatx", "rt90n", "rt90o", "wgs84_lat", "wgs84_lon", "sweref99_n", "sweref99_o");

if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($arrTopo as $topo ) {
    	fputcsv($fp, $topo, ";");
    }


    $file_download=$path_extract.$filename;
}
