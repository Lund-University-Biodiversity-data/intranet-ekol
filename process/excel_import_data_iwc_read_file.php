<?php

use MongoDB\BSON\UTCDateTime;

$fileRefused=false;

$arrActivityId=array();

$arr_json_activity=array();
$arr_json_output=array();
$arr_json_record=array();
$arr_json_obs_for_output=array();
//$arr_json_person=array();


$initDate = new \DateTime();
$stampedDate = $initDate->getTimestamp() * 1000;
$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

$userId=$commonFields["userId"];

try {

	$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($uploadFilePath);
	//$consoleTxt.=consoleMessage("info", "Excel type ".$inputFileType);
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
	$arrAntiDoublonArt=array();

	$iRow=2;

	// init key fields
	$oldPeriod="";
	$oldInternalSiteId="";
	$oldPersnr="";
	$oldDatum="";
	$oldMethod="";

	while ($worksheet->getCell("A".$iRow)->getValue() != "") {

		$persnr=$worksheet->getCell("A".$iRow)->getValue();
		$arthela=$worksheet->getCell("B".$iRow)->getValue();
		$lat5=$worksheet->getCell("C".$iRow)->getValue();
		$art=$worksheet->getCell("D".$iRow)->getValue();
		$antal=$worksheet->getCell("E".$iRow)->getValue();
		$internalSiteId=$worksheet->getCell("F".$iRow)->getValue();
		$year=$worksheet->getCell("G".$iRow)->getValue();
		$method=$worksheet->getCell("H".$iRow)->getValue();
		$datum=$worksheet->getCell("I".$iRow)->getValue();
		$period=$worksheet->getCell("J".$iRow)->getValue();
		$ice=$worksheet->getCell("K".$iRow)->getValue();
		//$biocollectId=$worksheet->getCell("L".$iRow)->getValue();

		// check if persnr exists in MongoDatabase
		if (!isset($array_persons[$persnr])) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown persnr in Mongodb, row #".$iRow." : ".$persnr);
		}

		// check if the art number exists in the list module
		if ($art!="000" && !isset($array_species_art[$art])) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown art in list module, row #".$iRow." : ".$art);
		}
		
		// check if the indidivudalCOunt is ok
		if ($art!="000" && (!is_numeric($antal) || trim($antal)=="")) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "missing individualCount column E, row #".$iRow." : ".$antal);
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

		// check format of the date
		if (!is_numeric($datum) || strlen($datum)!=8 || trim($datum) == "") {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "wrong format for the date in row #".$iRow." : ".$datum);
		}

		// check if period is among the accepted list
		$validPeriod=array("Januari", "September");
		if (!in_array($period, $validPeriod)) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown period, row #".$iRow." : ".$period);			
		}


		// check if ice is among the accepted list
		$validIce=array("", "0-10", "10-50", "50-99", "100");
		if (!in_array($ice, $validIce)) {
			$fileRefused=true;
			$consoleTxt.=consoleMessage("error", "unknown ice, row #".$iRow." : ".$ice);			
		}
		else {
			// add the % to fit  BioCollect enum
			if ($ice != "") $ice.="%";
		}





		// create the activity/event based on the art=000 line
		if ($art=="000") {

			$checkDoublons=$internalSiteId."#".$method."#".$year;
			// reinit the list of species
			$arrAntiDoublonArt=array();

			if (!in_array($checkDoublons, $arrAntiDoublon)) {


				// check if such an output exist (siteId + period + method + year(date))
				// check if the output exists ()
				$options = [];
				$filter = [
					"data.period" => $period,
					"data.observedFrom" => $method,
					"data.location" => $array_sites[$internalSiteId]["locationID"],
					"status" => "active",
					"data.surveyDate" => array('$regex'=>'^'.$year)
				];
				$query = new MongoDB\Driver\Query($filter, $options); 

				$rows = $mng->executeQuery("ecodata.output", $query);
				$nbActAlready=array();
				foreach ($rows as $row){
					$nbActAlready[]=$row->activityId;
				}
				if (count($nbActAlready)>0) {
					$fileRefused=true;
					$consoleTxt.=consoleMessage("error", "Already ".count($nbActAlready)." activity in MongoDb for site ".$internalSiteId." method ".$method." year ".$year." and period ".$period.". ActivityId : ".$linkBioActivity[$server].$nbActAlready[0]);
				}
				else {
					$activityId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
					$outputId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");

					$date_survey=date("Y-m-d", strtotime($datum))."T00:00:00Z";
					$recorder_name=$array_persons[$persnr]["firstName"]." ".$array_persons[$persnr]["lastName"];

					$start_time=convertTime("0000", "24H");
					$eventDate=date("Y-m-d", strtotime($datum))."T".$start_time.":00Z";

					$arr_json_activity[]=array(
						"activityId" => $activityId,
						"assessment" => false,
						"dateCreated" => $nowISODate,
						"lastUpdated" => $nowISODate,
						"progress" => "planned",
						"projectActivityId" => $commonFields[$protocol]["projectActivityId"],
						"projectId" => $commonFields[$protocol]["projectId"],
						"projectStage" => "",
						"siteId" => $array_sites[$internalSiteId]["locationID"],
						"status" => "active",
						"type" => $commonFields[$protocol]["type"],
						"userId" => strval($userId),
						"personId" => $array_persons[$persnr]["personId"],
						"mainTheme" => "",
						"verificationStatus" => "approved",
						"excelFile" => $uploadFile
					);			

					$arrAntiDoublon[]=$checkDoublons;

					// use the outputId as key to be able to add the observations later
					$arr_json_output[$outputId]=array(
						"activityId" => $activityId,
						"dateCreated" => $nowISODate,
						"lastUpdated" => $nowISODate,
						"outputId" => $outputId,
						"status" => "active",
						"outputNotCompleted" => false,
						"data" => array (
							"eventRemarks" => "",
							"surveyFinishTime" => $start_time,
							"locationAccuracy" => 50,
							"comments" => "",
							"surveyDate" => $date_survey,
							"observedFrom" => $method,
							"period" => $period,
							"istäcke" => $ice,
							"windSpeedKmPerHourCategorical" => "",
							"windDirectionCategorical" => "",
							"noSpecies" => "ja",
							"locationHiddenLatitude" => $array_sites[$internalSiteId]["decimalLatitude"],
							"locationLatitude" => $array_sites[$internalSiteId]["decimalLatitude"],
							"locationSource" => "Google maps",
							"recordedBy" => $recorder_name,
							"helpers" => "",
							"surveyStartTime" => $start_time,
							"locationCentroidLongitude" => null,
							"observations" => null,
							"location" => $array_sites[$internalSiteId]["locationID"],
							"locationLongitude" => $array_sites[$internalSiteId]["decimalLongitude"],
							"locationHiddenLongitude" => $array_sites[$internalSiteId]["decimalLongitude"],
							"locationCentroidLatitude" => null
						),
						"selectFromSitesOnly" => true,
						"_callbacks" => array(
							"sitechanged" => array(null)
						),
						"mapElementId" => "locationMap",
						"checkMapInfo" => array(
							"validation" => true
						),
						"name" => $commonFields[$protocol]["name"],
						"dataOrigin" => $dataOrigin
					);
				}
			}
			else {
				$fileRefused=true;
				$consoleTxt.=consoleMessage("error", "doublon on row #".$iRow." : ".$checkDoublons);			
			}

			// reinit key fields
			$oldPeriod="";
			$oldInternalSiteId="";
			$oldPersnr="";
			$oldDatum="";
			$oldMethod="";
		}
		else {

			//either the same key fields, or reinitiatilized
			if ( ($oldPeriod=="" && $oldInternalSiteId=="" && $oldPersnr=="" && $oldDatum=="" && $oldMethod=="" )
						|| 
					 ($oldPeriod==$period && $oldInternalSiteId==$internalSiteId && $oldPersnr==$persnr && $oldDatum==$datum && $oldMethod==$method )
					) {

				if (in_array($art, $arrAntiDoublonArt)) {
					$fileRefused=true;
					$consoleTxt.=consoleMessage("error", "Species doubon : art is already listed for this survey, at row #".$iRow.", art=".$art);	
				}
				else {
					$arrAntiDoublonArt[]=$art;

					$occurenceID=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
					$outputSpeciesId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");

					$notes="";

					$speciesFound++;
					$listId=$list_id[$animals];
					$sn=$array_species_art[$art]["sn"];
					$guid=$array_species_art[$art]["lsid"];
					$name=$array_species_art[$art]["nameSWE"];
					$rank=$array_species_art[$art]["rank"];

					if (mb_strtoupper($name)!=mb_strtoupper($arthela)){
						$fileRefused=true;
						$consoleTxt.=consoleMessage("error", "Different species name in the list module and in the excel file at row #".$iRow." : ".$name." (module list) / ".$arthela." (excel file)");			
					}

					$dataAnimal=array();

					$dataAnimal["swedishRank"]=$rank;
					$dataAnimal["individualCount"]=$antal;
					$dataAnimal["species"]=array(
						"listId" => $listId,
						"commonName" => "",
						"outputSpeciesId" => $outputSpeciesId,
						"scientificName" => $sn,
						"name" => $name,
						"guid" => $guid
					);

					if (!isset($arr_json_obs_for_output[$outputId])) {
						$arr_json_obs_for_output[$outputId]=array();
					}
					$arr_json_obs_for_output[$outputId][]=$dataAnimal;

					$arr_json_record[]=array(
						"dateCreated" => $nowISODate,
						"lastUpdated" => $nowISODate,
						"occurrenceID" => $occurenceID,
						"status" => "active",
						"recordedBy" => $recorder_name,
						"rightsHolder" => $commonFields["rightsHolder"],
						"institutionID" => $commonFields["institutionID"],
						"institutionCode" => $commonFields["institutionCode"],
						"basisOfRecord" => $commonFields["basisOfRecord"],
						"datasetID" => $commonFields[$protocol]["projectActivityId"],
						"datasetName" => $commonFields[$protocol]["datasetName"],
						"licence" => $commonFields["licence"],
						"locationID" => $array_sites[$internalSiteId]["locationID"],
						"locationName" => $array_sites[$internalSiteId]["locationName"],
						"locationRemarks" => "",
						"eventID" => $activityId,
						"eventTime" => $start_time,
						"eventRemarks" => "",
						"notes" => $notes,
						"guid" => $guid,
						"name" => $name,
						"scientificName" => $sn,
						"multimedia" => array(),
						"activityId" => $activityId,
						"decimalLatitude" => $array_sites[$internalSiteId]["decimalLatitude"],
						"decimalLongitude" => $array_sites[$internalSiteId]["decimalLongitude"],
						"eventDate" => $eventDate,
						"individualCount" => strval($antal),
						"outputId" => $outputId,
						"outputSpeciesId" => $outputSpeciesId,
						"projectActivityId" => $commonFields[$protocol]["projectActivityId"],
						"projectId" => $commonFields[$protocol]["projectId"],
						"userId" => strval($userId)
					);
				}
				

			}
			else {
				$fileRefused=true;
				$consoleTxt.=consoleMessage("error", "One key-data changed from the row before to the new one (".$iRow."). Missing line with art=000 ?");	
			}

			$oldPeriod=$period;
			$oldInternalSiteId=$internalSiteId;
			$oldPersnr=$persnr;
			$oldDatum=$datum; 
			$oldMethod=$method; 


		}

		$iRow++;
	}


}

// add the observations in the correct part of the output data
if (!$fileRefused) {
	foreach($arr_json_obs_for_output as $outputId => $obs) {
		$arr_json_output[$outputId]["data"]["observations"]=$obs;
		// and switch nospecies to nej
		$arr_json_output[$outputId]["data"]["noSpecies"]="nej";
	}

	/*
	echo "<pre>";
	print_r($arr_json_output);
	echo "</pre>";
	*/

	$consoleTxt.=consoleMessage("info", count($arr_json_activity)." activitie(s) found");
	$consoleTxt.=consoleMessage("info", count($arr_json_output)." output(s) found");
	$consoleTxt.=consoleMessage("info", count($arr_json_record)." record(s) found");

	//final row = number of records+activity - 2 (header + extra iRow++)
	if ($iRow - 2 != count($arr_json_activity) + count($arr_json_record)) {
		$fileRefused=true;
		$consoleTxt.=consoleMessage("error", "Total lines is wrong : ".($iRow-2). " in the file but ".(count($arr_json_activity) + count($arr_json_record))." to be added");	
	}
	if (count($arr_json_activity)!=count($arr_json_output)) {
		$fileRefused=true;
		$consoleTxt.=consoleMessage("error", "Not the same number of output/activity. Missing line with art=000 ?");	
	}
}