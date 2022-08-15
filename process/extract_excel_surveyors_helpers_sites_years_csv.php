<?php


$filename=date("Ymd-His")."_".$protocol."_extract_surveyorsYears.csv";
$path_extract=PATH_OUTPUT_CSV."surveyorsYears/";

$headers=array("person", "site", "yr", "month", "period", "method", "huvud", "med");

if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($tabSurveyorsYears as $data ) {
    	fputcsv($fp, $data, ";");
    }

    $file_download=$path_extract.$filename;
}