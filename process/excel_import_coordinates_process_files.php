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

	if (substr($file, strlen($file)-4, 4)==".xls") {
        $filename=substr($file, 0, strlen($file)-4);
    }
    elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
        $filename=substr($file, 0, strlen($file)-5);
    }

	switch ($protocol) {

		case "std":
		case "natt":

			$explodeFilename=explode("_", $filename);
			$siteKeyFilename=$explodeFilename[1];

			break;
		case "punkt":
		    
			$explodeFilename=explode("_", $filename);

            $siteKeyFilename=(isset($explodeFilename[1]) ? str_replace("#", "", $explodeFilename[1]) :"");

			$siteIdFN=explode("-", $explodeFilename[1]);

			$inventerareCheck=$siteIdFN[0]."-".$siteIdFN[1];

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

				$colX="B";
				$colY="C";

				break;

			case "punkt":

				$lineFirstPoint=10;

				$ruttrnr=$worksheet->getCell('C5')->getValue();
				$ruttname=$worksheet->getCell('C6')->getValue();
				$recorder_name=$worksheet->getCell('C3')->getValue();
				$inventerare = $worksheet->getCell('C4')->getValue();

				if ($inventerare!=$inventerareCheck) {
					$consoleTxt.=consoleMessage("error", "Not the same personnr in the filename (".($inventerareCheck).") and in the file (".$inventerare." for file ".$filename);
					$fileRefused=true;
				}

				$siteKey=$siteKeyFilename;	

				$colX="C";
				$colY="B";				

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

				$isWgs84=true;

				for ($iP=0; $iP<$nbPts ; $iP++) {

					$dataCoordPts[$iP]["lat"]=$worksheet->getCell($colX.($lineFirstPoint+$iP))->getValue();
					$dataCoordPts[$iP]["lon"]=$worksheet->getCell($colY.($lineFirstPoint+$iP))->getValue();
					
					if (trim($dataCoordPts[$iP]["lat"])=="" || trim($dataCoordPts[$iP]["lon"])=="") {
						$consoleTxt.=consoleMessage("error", "One coordinate is missing at row ".($lineFirstPoint+$iP)." for ".$siteKey);
						$fileRefused=true;

					}
					else {

						// RT90 if 1000000 < X < 7000000
						// RT90 if 1000000 < Y < 2000000
						// conversion
						if ($dataCoordPts[$iP]["lat"]>1000000 && $dataCoordPts[$iP]["lat"]<7000000 && $dataCoordPts[$iP]["lon"]>1000000 && $dataCoordPts[$iP]["lon"]<2000000) {

							
							// if it's not the first point and it was WGS84 before => error !
							if ($iP!=0 && $isWgs84) {
								$consoleTxt.=consoleMessage("error", "Coordinates is RT90 but was not before row ".($lineFirstPoint+$iP)." for ".$siteKey);
								$fileRefused=true;
							}

							$isWgs84=false;

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

							// if it's not the first point and it was not WGS84 before => error !
							if (!$isWgs84) {
								$consoleTxt.=consoleMessage("error", "Coordinates is WGS84 but was not before row ".($lineFirstPoint+$iP)." for ".$siteKey);
								$fileRefused=true;
							}
							
							if ($iP==0)
								$consoleTxt.=consoleMessage("info", "WGS84 coordinates (".$dataCoordPts[$iP]["lat"]."/".$dataCoordPts[$iP]["lon"].") found  for row ".($lineFirstPoint+$iP)." for ".$siteKey);
						}



						//print_r($wgsPos);
					}

					if ($protocol=="natt" && $worksheet->getCell("D".($lineFirstPoint+$iP))->getCalculatedValue()!=$inventerare) {
						$consoleTxt.=consoleMessage("error", "Not the same personnummer in header and in line ".($lineFirstPoint+$iP))." for ".$siteKey;
						$fileRefused=true;
					}

					if ($protocol=="natt" && $worksheet->getCell("E".($lineFirstPoint+$iP))->getCalculatedValue()!=$kartakod) {
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

				// calcul centroid
				$totLat=0;
				$totLong=0;

				$allPointsJson=array();
				foreach ($dataCoordPts as $iP => $coord) {
					$json=[
						"internalName" => 'P'.($iP+1),
						"name" => 'P'.($iP+1),
						"geometry" => [
							"type" => "Point",
							"decimalLongitude" => $coord["lon"],
							"decimalLatitude" => $coord["lat"],
							"coordinates" => [$coord["lon"], $coord["lat"]]
						]
					];
					if (isset($coord["RT90_lat"]) && isset($coord["RT90_lat"]) ) {
						$json["otherCRS"]["coords_3021"] = [$coord["RT90_lat"], $coord["RT90_lon"]] ;
					}
					$allPointsJson[]=$json;

					$totLong+=$coord["lon"];
					$totLat+=$coord["lat"];
				}
				$arr_json_sites[$array_sites[$siteKey]["locationID"]]["data"]=$allPointsJson;
				$arr_json_sites[$array_sites[$siteKey]["locationID"]]["siteInternal"]=$siteKey;

				$centroidLat=$totLat/count($dataCoordPts);
				$centroidLon=$totLong/count($dataCoordPts);
				$arr_json_sites[$array_sites[$siteKey]["locationID"]]["centroidLat"]=$centroidLat;
				$arr_json_sites[$array_sites[$siteKey]["locationID"]]["centroidLon"]=$centroidLon;

				$consoleTxt.=consoleMessage("info", "new WGS84 centroid calculated coordinates (".$centroidLat."/".$centroidLon.")  for ".$siteKey);

			}
			/*
			echo "<pre>";
			print_r($arr_json_sites);
			echo "</pre>";
			*/
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
