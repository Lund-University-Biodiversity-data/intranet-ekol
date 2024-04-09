<?php

require_once PATH_SHARED_FUNCTIONS . 'CoordinateTransformationLibrary/src/coordinatetransformation/positions/RT90Position.php';
require_once PATH_SHARED_FUNCTIONS . 'CoordinateTransformationLibrary/src/coordinatetransformation/positions/WGS84Position.php';

use MongoDB\BSON\UTCDateTime;

// 4** PROCESS FOR OK FILES

$nbPts=getNpPtsForProtocol ($protocol);


$fileRefused=false;

$arrActivityId=array();

/*
$arr_json_activity=array();
$arr_json_output=array();
$arr_json_record=array();
$arr_json_person=array();
*/
$arr_json_sites=array();

$initDate = new \DateTime();
$stampedDate = $initDate->getTimestamp() * 1000;
$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

foreach($listFilesOk as $file) {

	$userId=$commonFields["userId"];

	switch ($protocol) {

		case "std":
		case "natt":

			if (substr($file, strlen($file)-4, 4)==".xls") {
	            $filename=substr($file, 0, strlen($file)-4);
	        }
	        elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
	            $filename=substr($file, 0, strlen($file)-5);
	        }

			$explodeFilename=explode("-", $filename);
			$siteKeyFilename=$explodeFilename[1];

			
			break;
		case "sommar":

			if (substr($file, strlen($file)-4, 4)==".xls") {
	            $filename=substr($file, 0, strlen($file)-4);
	        }
	        elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
	            $filename=substr($file, 0, strlen($file)-5);
	        }
			$explodeFilename=explode("-", $filename);
			$siteIdFN1=$explodeFilename[1];//persnr
			$siteIdFN2=$explodeFilename[2];//indice
			$siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
			$siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;

			$siteKeyFilename = $siteIdFN;

			$inventerareCheck=$siteIdFN1."-".$siteIdFN2;

			break;
		case "vinter":
			
		    if (substr($file, strlen($file)-4, 4)==".xls") {
	            $filename=substr($file, 0, strlen($file)-4);
	        }
	        elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
	            $filename=substr($file, 0, strlen($file)-5);
	        }
			$explodeFilename=explode("-", $filename);
			$siteIdFN1=$explodeFilename[1];//persnr
			$siteIdFN2=$explodeFilename[2];//indice
			$siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
			$siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;
			$periodFN=str_replace("P", "", $explodeFilename[4]);

			$siteKeyFilename = $siteIdFN;

			$inventerareCheck=$siteIdFN1."-".$siteIdFN2;

			break;
	}

	$tmpfname = PATH_INPUT_EXCEL_COORDINATES.$database."/".$protocol."/".$file;
	$consoleTxt.=consoleMessage("info", "Opens file ".$tmpfname);

	try {

		$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($tmpfname);
		//$consoleTxt.=consoleMessage("info", "Excel type ".$inputFileType);
		$excelObj = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
		$excelObj->setReadDataOnly(true);
		$spreadsheet = $excelObj->load($tmpfname);
		$worksheet = $spreadsheet->getSheet(0);
		
	}
	catch (\Exception $exception) {
		$consoleTxt.=consoleMessage("error", "Caught exception : ".$exception->getMessage());
		$consoleTxt.=consoleMessage("error", "can't read Excel file ".$tmpfname);

		$fileRefused=true;
	}


	if (!$fileRefused) {

		switch($protocol) {
			case "natt":

				$lineFirstPoint=12;

				$kartakod=$worksheet->getCell('C3')->getValue();
				$ruttname=$worksheet->getCell('C4')->getValue(); // not used
				$recorder_name=$worksheet->getCell('C5')->getValue();
				$inventerare = $worksheet->getCell('C6')->getValue();

				$siteKey=$kartakod;	

				break;


			case "std":

				$datumExpectedFormat="YYYYMMDD";

				$kartakod=$worksheet->getCell('A9')->getValue();
				$ruttname=$worksheet->getCell('D9')->getValue(); // not used
				$datum=$worksheet->getCell('K9')->getValue();

				$inventerare = $worksheet->getCell('C13')->getValue();

				$recorder_name=$worksheet->getCell('B15')->getValue();
				$adress=$worksheet->getCell('B16')->getValue(); // not used
				$post=$worksheet->getCell('B17')->getValue(); // not used
				$city=$worksheet->getCell('F17')->getValue(); // not used
				$email=$worksheet->getCell('N15')->getValue(); // not used
				$address1=$recorder_name; // not used
				$address2=$worksheet->getCell('B16')->getValue(); // not used
				$tel=$worksheet->getCell('N16')->getValue(); // not used
				$mobile=$worksheet->getCell('N17')->getValue(); // not used

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A32')->getValue());
				$notes=str_replace('"', "'", $worksheet->getCell('A35')->getValue());
				$comments=$notes;


				$rowStartTime=23;

				// get the start_time/finish_time + timeOfObservation

				// find the start time and finish time
				$start_time=2359;
				$finish_time=0;
				$timeOfObservation=array();

				$iInd=1;
				for ($iCol="B";$iCol<="I";$iCol++) {
					$val=$worksheet->getCell($iCol.$rowStartTime)->getValue();

					if ($val!="" && !is_numeric($val)) {
						$consoleTxt.=consoleMessage("error", "TimeOfObservation: wrong format for cell ".$iCol.$rowStartTime);
						$fileRefused=true;
					}

					$timeOfObservation["TidP".str_pad($iInd, 2, '0', STR_PAD_LEFT)]=strval($val);
					
					if ($val!="") {
						$val=intval($val);

						if ($val<0 || $val>2400) {
							$consoleTxt.=consoleMessage("error", "ERROR time value ".$val." for P".$i);
							$fileRefused=true;
						}
						if ($val<$start_time)
							$start_time=$val;

						// add 24 hours to the night tmes, to help comparing
						if ($val<1200)
							$val+=2400;

						if ($val>$finish_time)
							$finish_time=$val;

						if ($val>2400) $val-=2400;
					}

					$iInd++;
				}


				if ($start_time>2400) $start_time-=2400;
				if ($finish_time>2400) $finish_time-=2400;

				$start_time_brut=$start_time;

				// add extra minutes (std: 35)
				$finish_time=addTimeToFinish($finish_time, 35);

				$minutesSpentObserving=array();
				$iInd=1;
				for ($iCol="K";$iCol<="R";$iCol++) {
					$val=$worksheet->getCell($iCol.$rowStartTime)->getValue();
					if ($val!="" && !is_numeric($val)) {
						$consoleTxt.=consoleMessage("error", "MinutesSpentObserving: wrong format for cell ".$iCol.$rowStartTime);
						$fileRefused=true;
					}
					$minutesSpentObserving["TidL".str_pad($iInd, 2, '0', STR_PAD_LEFT)]=strval($val);
					$iInd++;
				}

				$rowDistance=27;

				$distanceCovered=array();
				$iInd=1;
				for ($iCol="B";$iCol<="I";$iCol++) {
					$val=$worksheet->getCell($iCol.$rowDistance)->getValue();
					if ($val!="" && !is_numeric($val)) {
						$consoleTxt.=consoleMessage("error", "DistanceCovered: wrong format for cell ".$iCol.$rowDistance);
						$fileRefused=true;
					}
					$distanceCovered["distanceOnL".str_pad($iInd, 2, '0', STR_PAD_LEFT)]=strval($val);
					$iInd++;
				}
				

				$iRowSpecies=39;
				$colSpeciesCode="R";
				$colTotObs="V";
				
				$arrJaGps=["X", "x", "ja", "Ja", "JA"];
				$isGpsUsed=(in_array($worksheet->getCell('T31')->getValue(), $arrJaGps)  ? "ja" : "nej");

				$siteKey=$kartakod;				
				
				$checkTotalData[0]["animals"]='birds';
				$checkTotalData[0]["textNumberSpeciesFound"]="Antal fågelarter totalt";
				$checkTotalData[0]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[0]["columnValueNumberSpeciesFound"]='D';

				$checkTotalData[1]["animals"]='mammals';
				$checkTotalData[1]["textNumberSpeciesFound"]="Antal däggdjursarter totalt";
				$checkTotalData[1]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[1]["columnValueNumberSpeciesFound"]='E';

				break;

			case "sommar":
			case "vinter":

				$datumExpectedFormat="YYMMDD";

				//$vinter=$worksheet->getCell('A1')->getValue();
				$period=$worksheet->getCell('C1')->getValue();				

				$inventerare=$worksheet->getCell('A9')->getValue();
				$rnr=str_pad($worksheet->getCell('D9')->getValue(), 2, '0', STR_PAD_LEFT);
				$datum=$worksheet->getCell('H9')->getValue();
				$startTime=$worksheet->getCell('L9')->getValue();
				$endTime=$worksheet->getCell('O9')->getValue();

				$ruttname=$worksheet->getCell('A12')->getValue();
				//$kartakod=$worksheet->getCell('G10')->getValue();
				$distance=$worksheet->getCell('L12')->getValue();
				$transport=$worksheet->getCell('O12')->getValue();

				$recorder_name=$worksheet->getCell('B14')->getValue();
				$adress=$worksheet->getCell('B15')->getValue();
				$post=$worksheet->getCell('B16')->getValue();
				$city=$worksheet->getCell('F16')->getValue();
				$email=$worksheet->getCell('N14')->getValue();
				$address1=$recorder_name;
				$address2="";
				$tel=$worksheet->getCell('N15')->getValue();
				$mobile=$worksheet->getCell('N16')->getValue();

				$notes="";
				//str_replace('"', "'", $worksheet->getCell('A21')->getValue());

				$newRoute=$worksheet->getCell('X9')->getValue();
				$mapAttached=$worksheet->getCell('X10')->getValue();
				$coordAttached=$worksheet->getCell('X11')->getValue();

				$siteKey=$inventerare."-".$rnr;

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A21')->getValue());
				$notes="";
				$comments=$notes;

				if ($protocol=="vinter") 
					$iRowSpecies=30;
				elseif ($protocol=="sommar")  {
					//$iRowSpecies=41;
					$iRowSpecies=30;
				}
				
				$colSpeciesCode="V";
				$colTotObs="X";

				if ($inventerare!=$inventerareCheck) {
					$consoleTxt.=consoleMessage("error", "Not the same Personummer in the filename and file content ! ".$inventerare." VS ".$inventerareCheck);
					$fileRefused=true;
				}

				if ($protocol=="vinter") {

					$snow=$worksheet->getCell('R12')->getValue();

					if ($period!=$periodFN) {
						$consoleTxt.=consoleMessage("error", "Not the same period in the filename and file content ! ".$period." VS ".$periodFN);
						$fileRefused=true;
					}
					elseif ($period>5 || $period<0) {
						$consoleTxt.=consoleMessage("error", "Period field must be a number between 1 and 5");
						$fileRefused=true;
					}
				}

				$checkTotalData[0]["animals"]='birds';
				$checkTotalData[0]["textNumberSpeciesFound"]="Antal arter totalt";
				$checkTotalData[0]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[0]["columnValueNumberSpeciesFound"]='D';

				break;
				
			
			case "kust":
				$nbPts=1;
				break;
		}


		// get person from MongoDb
		
		$consoleTxt.=consoleMessage("info", "Inventerare : ".$inventerare); 
		$person= getPersonFromInternalId ($inventerare, $server) ;
		if (!isset($person["personId"])) {
			$consoleTxt.=consoleMessage("error", "Can't find one person in MongoDb for ".$inventerare);
			$fileRefused=true;
		}
		elseif (mb_strtoupper($recorder_name)!= mb_strtoupper($person["firstName"]." ".$person["lastName"])) {
			$consoleTxt.=consoleMessage("error", "Not the same surveyor name in the file (".mb_strtoupper($recorder_name).") and in the database (".mb_strtoupper($person["firstName"]." ".$person["lastName"]).")");
			$fileRefused=true;
		}
		else {
			$personId=$person["personId"];
			if (isset($person["userId"]) && $person["userId"]!="") $userId=$person["userId"];
		}

		if ($siteKeyFilename!=$siteKey) {
			$consoleTxt.=consoleMessage("error", "Filename site id (".$siteKeyFilename.") and site id in the file (".$siteKey.") don't match ");
			$fileRefused=true;
		}

		$consoleTxt.=consoleMessage("info", "siteKey / ruttnamn : ".$siteKey." / ".$ruttname);

		// check if the site is in the Mongo db
		if (isset($array_sites[$siteKey])) {

			// check if some points data already exists in the database
			if (count($array_sites[$siteKey]["transectParts"])!=0) {
				$consoleTxt.=consoleMessage("error", "Transect data already exists for site ".$siteKey);
				$fileRefused=true;
			}
			else {
				$dataCoordPts=array();

				for ($iP=0; $iP<$nbPts ; $iP++) {

					$dataCoordPts[$iP]["lat"]=$worksheet->getCell("B".($lineFirstPoint+$iP))->getValue();
					$dataCoordPts[$iP]["lon"]=$worksheet->getCell("C".($lineFirstPoint+$iP))->getValue();
					
					if (trim($dataCoordPts[$iP]["lat"])=="" || trim($dataCoordPts[$iP]["lon"])=="") {
						$consoleTxt.=consoleMessage("error", "One coordinate is missing at row ".($lineFirstPoint+$iP)." for ".$siteKey);
						$fileRefused=true;

					}
					else {

						// RT90 if 1000000 < X < 7000000
						// RT90 if 1000000 < Y < 2000000
						// conversion
						if ($dataCoordPts[$iP]["lat"]>1000000 && $dataCoordPts[$iP]["lat"]<7000000 && $dataCoordPts[$iP]["lon"]>1000000 && $dataCoordPts[$iP]["lon"]<2000000) {

							// store the RT90 data in a separate variable
							$dataCoordPts[$iP]["RT90_lat"]=$dataCoordPts[$iP]["lat"];
							$dataCoordPts[$iP]["RT90_lon"]=$dataCoordPts[$iP]["lon"];

							// display only for the 1st point
							if ($iP==0)
								$consoleTxt.=consoleMessage("info", "RT90 coordinates for P1 (".$dataCoordPts[$iP]["lat"]."/".$dataCoordPts[$iP]["lon"].") found  for row ".($lineFirstPoint+$iP)." for ".$siteKey);

							$swePos = new RT90Position($dataCoordPts[$iP]["RT90_lat"], $dataCoordPts[$iP]["RT90_lon"]);
							$wgsPos = $swePos->toWGS84();

							$dataCoordPts[$iP]["lat"]=round($wgsPos->getLatitude(), 6);
							$dataCoordPts[$iP]["lon"]=round($wgsPos->getLongitude(), 6);

							// display only for the 1st point
							if ($iP==0)
								$consoleTxt.=consoleMessage("info", "Coordinates conversion for P1 from RT90 to WGS84 (".$dataCoordPts[$iP]["lat"]."/".$dataCoordPts[$iP]["lon"].") for row ".($lineFirstPoint+$iP)." for ".$siteKey);

						}
						else {
							$consoleTxt.=consoleMessage("info", "WGS84 coordinates (".$dataCoordPts[$iP]["lat"]."/".$dataCoordPts[$iP]["lon"].") found  for row ".($lineFirstPoint+$iP)." for ".$siteKey);
						}



						//print_r($wgsPos);
					}

					if ($worksheet->getCell("D".($lineFirstPoint+$iP))->getCalculatedValue()!=$inventerare) {
						$consoleTxt.=consoleMessage("error", "Not the same personnummer in header and in line ".($lineFirstPoint+$iP))." for ".$siteKey;
						$fileRefused=true;
					}

					if ($worksheet->getCell("E".($lineFirstPoint+$iP))->getCalculatedValue()!=$kartakod) {
						$consoleTxt.=consoleMessage("error", "Not the same kartakod in header and in line ".($lineFirstPoint+$iP))." for ".$siteKey;
						$fileRefused=true;
					}

					if ($worksheet->getCell("F".($lineFirstPoint+$iP))->getCalculatedValue()!=$ruttname) {
						$consoleTxt.=consoleMessage("error", "Not the same ruttnamn in header and in line ".($lineFirstPoint+$iP))." for ".$siteKey;
						$fileRefused=true;
					}
				}
			}
			
			if (!$fileRefused && count($dataCoordPts)!=0) {
				$allPointsJson=array();
				foreach ($dataCoordPts as $iP => $coord) {
					$json=[
						"internalName" => 'P'.($iP+1),
						"name" => 'P'.($iP+1),
						"geometry" => [
							"type" => "Point",
							"decimalLongitude" => $coord["lon"],
							"decimalLatitude" => $coord["lat"],
							"coordinates" => [$coord["lat"], $coord["lon"]]
						]
					];
					if (isset($coord["RT90_lat"]) && isset($coord["RT90_lat"]) ) {
						$json["otherCRS"]["coords_3021"] = [$coord["RT90_lat"], $coord["RT90_lon"]] ;
					}
					$allPointsJson[]=$json;
				}
				$arr_json_sites[$array_sites[$siteKey]["locationID"]]=$allPointsJson;
			}
			//echo "<pre>";
			//print_r($arr_json_sites);
			//echo "</pre>";
		}
		else {
			$consoleTxt.=consoleMessage("error", "Can't get site data from Mongo for site ".$siteKey);
			$fileRefused=true;
		}

	}



	if ($fileRefused) break;
}


// replace last comma by 
//$arr_json_output[strlen($arr_json_output)-1]=' ';
//$arr_json_output.=']';
//$arr_json_activity[strlen($arr_json_activity)-1]=' ';
//$arr_json_activity.=']';
//$arr_json_record[strlen($arr_json_record)-1]=' ';
//$arr_json_record.=']';
//$arr_json_person[strlen($arr_json_person)-1]=' ';
//$arr_json_person.=']';

//echo $arr_json_output;
//echo "\n";

?>
