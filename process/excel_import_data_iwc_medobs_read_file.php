<?php

$fileRefused=false;

$arr_medobs=array();
//$arr_json_person=array();

try {

	$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($uploadFilePath);
	$consoleTxt.=consoleMessage("info", "Excel type ".$inputFileType);
	$excelObj = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
	$excelObj->setReadDataOnly(true);
	$spreadsheet = $excelObj->load($uploadFilePath);
	$worksheet = $spreadsheet->getSheet(0);
	
}
catch (\Exception $exception) {
	$consoleTxt.=consoleMessage("error", "Caught exception : ".$exception->getMessage());
	$consoleTxt.=consoleMessage("error", "can't read Excel file ".$uploadFilePath);

	$fileRefused=true;
}


if (!$fileRefused) {

	$arrAntiDoublon=array();

	$iRow=2;

	while ($worksheet->getCell("A".$iRow)->getValue() != "") {

		$persnr=$worksheet->getCell("A".$iRow)->getValue();
		$internalSiteId=$worksheet->getCell("B".$iRow)->getValue();
		$year=$worksheet->getCell("C".$iRow)->getValue();
		$period=$worksheet->getCell("D".$iRow)->getValue();
		$method=$worksheet->getCell("E".$iRow)->getValue();

		// check if persnr exists in MongoDatabase
		if (!isset($array_persons[$persnr])) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown persnr in Mongodb, row #".$iRow." : ".$persnr);
		}

		// check if the site exists in MongoDb
		if (!isset($array_sites[$internalSiteId]) || trim($internalSiteId) == "") {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown site in MongoDb, row #".$iRow." : ".$internalSiteId);
		}

		// check if method is among the accepted list
		$validMethod=array("båt","land","flyg");
		if (!in_array($method, $validMethod)) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown method, row #".$iRow." : ".$method);			
		}

		// check format of the year
		if (!is_numeric($year) || strlen($year)!=4 || trim($year) == "") {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "wrong format for the year (should be YYYY) in row #".$iRow." : ".$datum);
		}

		// check if period is among the accepted list
		$validPeriod=array("Januari", "September");
		if (!in_array($period, $validPeriod)) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown period, row #".$iRow." : ".$period);			
		}


		// check if the output exists (siteId + period + method + year(date))
		$options = [];
		$filter = [
			"data.period" => $period,
			"data.observedFrom" => $method,
			"data.location" => $array_sites[$internalSiteId]["locationID"],
			"status" => "active"
		];
		$query = new MongoDB\Driver\Query($filter, $options); 

		$rows = $mng->executeQuery("ecodata.output", $query);
		$nbAdd=0;
		foreach ($rows as $output){
			//var_dump($output);exit();
			//echo $output->outputId." => ".$array_persons[$persnr]." / ".$output->data->surveyDate."<br>";
			// check the date

			if (substr($output->data->surveyDate, 0, 4)==$year) {
				$nbAdd++;
				$arr_medobs[$output->activityId]["helpers"][]=$array_persons[$persnr]["personId"];

				if ($output->data->helpers!="") {
					$consoleTxt.=consoleMessage("error", "Helpers already set in MongoDb for row #".$iRow." (period ".$period."/ method ".$method." / year ".$year." / site ".$internalSiteId."). ActivityId : ".$output->activityId);
					$fileRefused=true;					
				}
			}
			
		}

		if ($nbAdd==0) {
			$consoleTxt.=consoleMessage("error", "No activity found in Mongo for row #".$iRow." (period ".$period."/ method ".$method." / year ".$year." / site ".$internalSiteId.")");
			$fileRefused=true;
		}

		if ($nbAdd>1) {
			$consoleTxt.=consoleMessage("error", "More than 1 activity found in Mongo for row #".$iRow." (period ".$period."/ method ".$method." / year ".$year." / site ".$internalSiteId.")");
			$fileRefused=true;
		}

		$iRow++;
	}

	$consoleTxt.=consoleMessage("info", count($arr_medobs)." activitie(s) with helper(s) to add");

}
