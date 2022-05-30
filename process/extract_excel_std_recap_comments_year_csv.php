<?php


$filename=date("Ymd-His")."_extract_stdRecapComments.csv";
$path_extract=PATH_OUTPUT_CSV."stdRecapComments/";

$headers=array("karta", "yr", "gps", "Birdinfo", "Praktisk info", "activityId");

if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($tabSitesPeriod as $site => $dataSite ) {
	    foreach($dataSite as $year => $dataYear ) {
	    	$line=array();

	    	$line["karta"]=$site;
	    	$line["yr"]=$year;
	    	$line["gps"]=$dataYear["isGpsUsed"];
	    	$line["events"]=$dataYear["eventRemarks"];
	    	$line["comments"]=$dataYear["comments"];
	    	$line["activityId"]=$dataYear["activityId"];
	    	fputcsv($fp, $line, ";");
	    }
    }


    $file_download=$path_extract.$filename;
}