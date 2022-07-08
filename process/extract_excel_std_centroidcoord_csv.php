<?php


$filename=date("Ymd-His")."_extract_stdCentroidStdCoord.csv";
$path_extract=PATH_OUTPUT_CSV."stdCentroidStdCoord/";

$headers=array("id", "karta", "kartatx", "mitt_rt90_n", "mitt_rt90_o", "p1_rt90_n", "p1_rt90_o", "mitt_wgs84_lat", "mitt_wgs84_lon", "p1_wgs84_lat", "p1_wgs84_lon", "p1_sweref99_n", "p1_sweref99_o", "mitt_sweref99_n", "mitt_sweref99_o");

if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($arrTopo as $topo ) {
    	fputcsv($fp, $topo, ";");
    }


    $file_download=$path_extract.$filename;
}
