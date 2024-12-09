<?php


$filename=date("Ymd-His")."_extract_punkt20pts.csv";
$path_extract=PATH_OUTPUT_CSV."punkt20pts/";

$headers=array("id", "internalSiteId", "pointNumber", "pointName", "anonymousPointID", "countyName", "countySCB", "municipality", "StnRegOSId", "StnRegPPId", );


if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($arrPoint as $pt ) {
    	fputcsv($fp, $pt, ";");
    }

    $file_download=$path_extract.$filename;
}
