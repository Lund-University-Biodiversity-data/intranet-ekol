<?php


$filename=date("Ymd-His")."_extract_surveyorsYears.csv";
$path_extract=PATH_OUTPUT_CSV."surveyorsYears/";

$headers=array("site", "yr", "person", "huvud", "med");

if ($fp = fopen($path_extract.$filename, 'w')) {

    fputcsv($fp, $headers, ";");

    foreach($tabSurveyorsYears as $data ) {
    	fputcsv($fp, $data, ";");
    }


    $file_download=$path_extract.$filename;
}