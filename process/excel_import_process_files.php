<?php

use MongoDB\BSON\UTCDateTime;



// 4** PROCESS FOR OK FILES

switch($protocol) {
	case "std":
		$nbPts=8;
		break;
	case "vinter":
		$nbPts=20;
		break;
	case "natt":
		$nbPts=20;
		break;
	case "kust":
		$nbPts=1;
		break;
}


$nbFiles=0;

$fileRefused=false;

$arrActivityId=array();

$arr_json_activity=array();
$arr_json_output=array();
$arr_json_record=array();
$arr_json_person=array();


$initDate = new \DateTime();
$stampedDate = $initDate->getTimestamp() * 1000;
$nowISODate = new MongoDB\BSON\UTCDateTime($stampedDate);

foreach($listFilesOk as $file) {


	$nbFiles++;

	switch ($protocol) {

		case "std":
		case "natt":
			$siteKeyFilename = substr($file, 0, 5);
			break;
		case "vinter":
			
		    if (substr($file, strlen($file)-4, 4)==".xls") {
	            $filename=substr($file, 0, strlen($file)-4);
	        }
	        elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
	            $filename=substr($file, 0, strlen($file)-5)==".xlsx";
	        }
			$explodeFilename=explode("-", $filename);
			$siteIdFN1=$explodeFilename[1];//persnr
			$siteIdFN2=$explodeFilename[2];//indice
			$siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
			$siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;
			$periodFN=str_replace("P", "", $explodeFilename[4]);

			$siteKeyFilename = $siteIdFN;

			break;
	}

	if (isset($limitFiles) && $limitFiles<$nbFiles) break;

	$tmpfname = PATH_INPUT_EXCEL.$database."/".$protocol."/".$file;
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


		$lastRow = $worksheet->getHighestRow();

		switch($protocol) {
			case "std":
				$kartakod=$worksheet->getCell('A9')->getValue();
				$ruttname=$worksheet->getCell('D9')->getValue();
				$datum=$worksheet->getCell('K9')->getValue();
				$inventerare = $worksheet->getCell('C13')->getValue();

				$recorder_name=$worksheet->getCell('B15')->getValue();
				$adress=$worksheet->getCell('B16')->getValue();
				$post=$worksheet->getCell('B17')->getValue();
				$city=$worksheet->getCell('F17')->getValue();
				$email=$worksheet->getCell('N15')->getValue();
				$address1=$recorder_name;
				$address2=$worksheet->getCell('B16')->getValue();
				$tel=$worksheet->getCell('N16')->getValue();
				$mobile=$worksheet->getCell('N17')->getValue();

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A32')->getValue());
				$notes=str_replace('"', "'", $worksheet->getCell('A35')->getValue());
				$comments=$notes;

				$rowStartTime=23;
				$iRowSpecies=39;
				$colSpeciesCode="R";
				$colTotObs="V";

				
				$arrJaGps=["X", "x", "ja", "Ja", "JA"];
				$isGpsUsed=(in_array($worksheet->getCell('T31')->getValue(), $arrJaGps)  ? "ja" : "nej");

				$siteKey=$kartakod;

				break;

			case "vinter":

				$vinter=$worksheet->getCell('A1')->getValue();
				$period=$worksheet->getCell('C1')->getValue();				

				$inventerare=$worksheet->getCell('A7')->getValue();
				$rnr=str_pad($worksheet->getCell('D7')->getValue(), 2, '0', STR_PAD_LEFT);
				$datum=$worksheet->getCell('H7')->getValue();
				$startTime=$worksheet->getCell('L7')->getValue();
				$endTime=$worksheet->getCell('O7')->getValue();

				$ruttname=$worksheet->getCell('A10')->getValue();
				$kartakod=$worksheet->getCell('G10')->getValue();
				$length=$worksheet->getCell('L10')->getValue();
				$transport=$worksheet->getCell('O10')->getValue();
				$snow=$worksheet->getCell('R10')->getValue();

				$recorder_name=$worksheet->getCell('B12')->getValue();
				$adress=$worksheet->getCell('B13')->getValue();
				$post=$worksheet->getCell('B14')->getValue();
				$city=$worksheet->getCell('F14')->getValue();
				$email=$worksheet->getCell('N12')->getValue();
				$address1=$recorder_name;
				$address2="";
				$tel=$worksheet->getCell('N13')->getValue();
				$mobile=$worksheet->getCell('N14')->getValue();

				$notes=str_replace('"', "'", $worksheet->getCell('A19')->getValue());

				$newRoute=$worksheet->getCell('X7')->getValue();
				$mapAttached=$worksheet->getCell('X8')->getValue();
				$coordAttached=$worksheet->getCell('X9')->getValue();

				$siteKey=$inventerare."-".$rnr;

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A19')->getValue());
				$notes="";
				$comments=$notes;

				$iRowSpecies=39;
				$colSpeciesCode="V";
				$colTotObs="X";

				break;
				
			case "natt":
				$kartakod=$worksheet->getCell('A9')->getValue();
				$ruttname=$worksheet->getCell('D9')->getValue();
				$datum=$worksheet->getCell('L9')->getValue();
				$inventerare = $worksheet->getCell('C13')->getValue();
				$per = $worksheet->getCell('L13')->getValue();

				if (trim($worksheet->getCell('U10')->getValue())=="X" || trim($worksheet->getCell('U10')->getValue())=="x")
					$mammalsCounted="ja";
				else $mammalsCounted="nej";
				if (trim($worksheet->getCell('U11')->getValue())=="X" || trim($worksheet->getCell('U11')->getValue())=="x")
					$amphibiansCounted="ja";
				else $amphibiansCounted="nej";

				$recorder_name=$worksheet->getCell('B15')->getValue();
				$adress=$worksheet->getCell('B16')->getValue();
				$post=$worksheet->getCell('B17')->getValue();
				$city=$worksheet->getCell('F17')->getValue();
				$email=$worksheet->getCell('M15')->getValue();
				$address1=$recorder_name;
				$address2=$worksheet->getCell('B16')->getValue();
				$tel=$worksheet->getCell('M16')->getValue();
				$mobile=$worksheet->getCell('M17')->getValue();

				$cloudStart=$worksheet->getCell('B21')->getValue();
				$tempStart=$worksheet->getCell('C21')->getValue();
				$windStart=$worksheet->getCell('D21')->getValue();
				$precipStart=$worksheet->getCell('E21')->getValue();

				$cloudEnd=$worksheet->getCell('F21')->getValue();
				$tempEnd=$worksheet->getCell('G21')->getValue();
				$windEnd=$worksheet->getCell('H21')->getValue();
				$precipEnd=$worksheet->getCell('I21')->getValue();

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A164')->getValue());
				$notes="";
				$comments=$notes;

				$rowStartTime=25;
				$iRowSpecies=30;
				$colSpeciesCode="V";
				$colTotObs="Z";

				$siteKey=$kartakod;

				break;
			case "kust":
				$nbPts=1;
				break;
		}


		if (isset($startTime) &&!is_numeric($startTime)) {
			$consoleTxt.=consoleMessage("error", "Start time not numeric : ".$startTime);
			$fileRefused=true;
		}
		if (isset($endTime) &&!is_numeric($endTime)) {
			$consoleTxt.=consoleMessage("error", "End time not numeric : ".$endTime);
			$fileRefused=true;
		}

		if ($siteKeyFilename!=$siteKey) {
			$consoleTxt.=consoleMessage("error", "Filename site id (".$siteKeyFilename.") and site id in the file (".$siteKey.") don't match ");
			$fileRefused=true;
		}
		$consoleTxt.=consoleMessage("info", "Inveterare : ".$inventerare); 

		// in case of a 6 digit date, we add 20
		if (strlen($datum)==6) $datum="20".$datum;

		$date_survey=date("Y-m-d", strtotime($datum))."T00:00:00Z";

		$consoleTxt.=consoleMessage("info", "siteKey / ruttnamn / datum : ".$siteKey." / ".$ruttname." / ".$datum." / ".$date_survey);

		if (isset($array_sites[$siteKey])) {

			// check if this person exists
			$explInv=explode("-", $inventerare);
			if (!isset($explInv[0]) || strlen($explInv[0])!=6) {
				$consoleTxt.=consoleMessage("error", "Birthdate not valid ".$inventerare);
				$fileRefused=true;
			}
			else {
				$birthdate=$explInv[0];
				$year=substr($birthdate, 0, 2);

				$birthdate_format=($year<=20 ? "20".$year : "19".$year)."-".substr($birthdate, 2, 2)."-".substr($birthdate, 4, 2);
			}
			
			$filter = ['internalPersonId' => $inventerare];
			//$filter = [];
			$options = [];
			$query = new MongoDB\Driver\Query($filter, $options); 
			//db.site.find({"projects":"dab767a5-929e-4733-b8eb-c9113194201f"}, {"projects":1, "name":1}).pretty()
			// 
		    $mng = new MongoDB\Driver\Manager($mongoConnection[$server]); // Driver Object created

			$rows = $mng->executeQuery("ecodata.person", $query);

			$rowsToArray=$rows->toArray();

			if (count($rowsToArray)!=0) {
				$userId = (isset($rowsToArray[0]->userId) ? $rowsToArray[0]->userId : $commonFields["userId"]);
				$personId = $rowsToArray[0]->personId;
				$consoleTxt.=consoleMessage("info", "Person exists in database: ".$personId);
			}
			else {

				$userId = $commonFields["userId"];
				$personId = generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
				$explFN=explode(" ", $recorder_name);

				$arr_json_person[]=array(
					"dateCreated" => $nowISODate,
					"lastUpdated" => $nowISODate,
					"personId" => $personId,
					"firstName" => $explFN[0],
					"lastName" => $explFN[1],
					"birthdate" => $birthdate_format,
					"email" => $email,
					"phoneNum" => $tel,
					"mobileNum" => $mobile,
					"address1" => $address1,
					"address2" => $address2,
					"postCode" => $post,
					"town" => $city,
					"projects"=> array($commonFields[$protocol]["projectId"]),
					"internalPersonId" => $inventerare
				);
				/*
				$arr_json_person.='{
					"dateCreated" : ISODate("'.$date_now_tz.'"),
					"lastUpdated" : ISODate("'.$date_now_tz.'"),
					"personId" : "'.$personId.'",
					"firstName" : "'.$explFN[0].'",
					"lastName" : "'.$explFN[1].'",
					"birthdate" : "'.$birthdate_format.'",
					"email" : "'.$email.'",
					"phoneNum" : "'.$tel.'",
					"mobileNum" : "'.$mobile.'",
					"address1" : "'.$address1.'",
					"address2" : "'.$address2.'",
					"postCode" : "'.$post.'",
					"town" : "'.$city.'",
					"projects": [ "'.$commonFields[$protocol]["projectId"].'" ],
					"internalPersonId" : "'.$inventerare.'"
				},';
				*/
				$consoleTxt.=consoleMessage("info", "Person to be created with personid: ".$personId);
			}



			if ($protocol=="vinter") {
				$start_time=$startTime;
				$finish_time=$endTime;
			}
			else {
				// find the start time and finish time
				$start_time=2359;
				$finish_time=0;

				$minutesSpentObserving=array();
				$timeOfObservation=array();


				/*$minutesSpentObserving='"minutesSpentObserving" : [
						{';
				$timeOfObservation='"timeOfObservation" : [
						{';*/

				$colStartTime="B";
				$colStartMin="K";

				for ($i=1; $i<=$nbPts; $i++) {
					$timeofObs=$worksheet->getCell($colStartTime++.$rowStartTime)->getValue();
					$minSpentObs=$worksheet->getCell($colStartMin++.$rowStartTime)->getValue();

					switch($protocol) {
						case "std":
							$ind="p".$i;
							
							if ($timeofObs!="") {
								$timeOfObservation["TidP".str_pad($i, 2, '0', STR_PAD_LEFT)]=$timeofObs;

								//$timeOfObservation.='
								//"TidP'.str_pad($i, 2, '0', STR_PAD_LEFT).'" : "'.$timeofObs.'",';
							}
							if ($minSpentObs!="") {
								$minutesSpentObserving["TidL".str_pad($i, 2, '0', STR_PAD_LEFT)]=$minSpentObs;

								//$minutesSpentObserving.='
								//"TidL'.str_pad($i, 2, '0', STR_PAD_LEFT).'" : "'.$minSpentObs.'",';
							}
							break;
						case "natt":

							$ind="p".str_pad($i, 2, '0', STR_PAD_LEFT);

							$timeOfObservation["TidP".str_pad($i, 2, '0', STR_PAD_LEFT)]=$timeofObs;

							//$timeOfObservation.='
							//"TidP'.str_pad($i, 2, '0', STR_PAD_LEFT).'" : "'.$timeofObs.'",';

							break;
					}
					
					if ($timeofObs!="") {
						$timeofObs=intval($timeofObs);

						if ($timeofObs>2400) {
							$consoleTxt.=consoleMessage("error", "ERROR time value ".$timeofObs." for P".$i);
							$fileRefused=true;
						}
						if ($timeofObs<$start_time)
							$start_time=$timeofObs;

						// add 24 hours to the night tmes, to help comparing
						if ($timeofObs<1200)
							$timeofObs+=2400;

						if ($timeofObs>$finish_time)
							$finish_time=$timeofObs;

						if ($timeofObs>2400) $timeofObs-=2400;
					}
				}


				if ($start_time>2400) $start_time-=2400;
				if ($finish_time>2400) $finish_time-=2400;

				$start_time_brut=$start_time;


				// add 5 minutes 
				$finish_time_5min=str_pad($finish_time, 4, '0', STR_PAD_LEFT);
				$hours=intval(substr($finish_time_5min, 0, 2));
				$minutes=intval(substr($finish_time_5min, 2, 2));
				if ($minutes>=55) {
					$minutes=str_pad(($minutes+5)-60, 2, '0', STR_PAD_LEFT);

					if ($hours==23) $hours=0;
					else $hours++;
				}
				else {
					$minutes+=5;
				}
				$finish_time=intval($hours.$minutes);


				$distanceCovered=array();

				//$distanceCovered='"distanceCovered" : [
				//		{
				//			';

				$iL="B";
				for ($i=1; $i<=$nbPts; $i++) {
					$distanceCovered["distanceOnL".str_pad($i, 2, '0', STR_PAD_LEFT)]=$worksheet->getCell($iL."27")->getValue();

					//$distanceCovered.='
					//"distanceOnL'.str_pad($i, 2, '0', STR_PAD_LEFT).'" : "'.$worksheet->getCell($iL."27")->getValue().'",';
					$iL++;
				}

				//$distanceCovered[strlen($distanceCovered)-1]=' ';
				//$distanceCovered.='}],';

				
				
				//$minutesSpentObserving[strlen($minutesSpentObserving)-1]=' ';
				//$minutesSpentObserving.='}],';
				//$timeOfObservation[strlen($timeOfObservation)-1]=' ';
				//$timeOfObservation.='}],';

			}



			$start_time=convertTime($start_time, "24H");
			$finish_time=convertTime($finish_time, "24H");

			$eventDate=date("Y-m-d", strtotime($datum))."T".$start_time.":00Z";

			//$eventTime=date("H:i", strtotime($rtEvents["datum"]));

			
			
			$activityId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
			$eventID=$activityId;

			$arrActivityId[$file]=$activityId;

			$outputId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");

			$helpers=array();

			foreach ($list_id as $animals => $listId) {
				//$data_field[$animals]="";
				$data_field[$animals]=array();
			}
			//$data_field["mammalsOnRoad"]="";
			$data_field["mammalsOnRoad"]=array();

			$listId = $list_id["birds"];

			// loop on the rows
			while ($worksheet->getCell($colTotObs.$iRowSpecies)->getValue() != "") {

				//echo "V".$iRowSpecies.":".$worksheet->getCell("V".$iRowSpecies)->getOldCalculatedValue()."(".$worksheet->getCell("S".$iRowSpecies)->getOldCalculatedValue()." - ".$worksheet->getCell("T".$iRowSpecies)->getOldCalculatedValue().")\n";

				// criteria to analyze the row
				$okAnalyzeRow=false;
				switch ($protocol) {
					case "std":
					case "natt":
						if ($worksheet->getCell($colTotObs.$iRowSpecies)->getOldCalculatedValue()==1) $okAnalyzeRow=true;
						break;
					case "vinter":
						if ($worksheet->getCell($colTotObs.$iRowSpecies)->getOldCalculatedValue()>0) {
							$okAnalyzeRow=true;
						}
						break;
				}
				
				if ($okAnalyzeRow) {

					$art=$worksheet->getCell($colSpeciesCode.$iRowSpecies)->getValue();

					switch($protocol) {
						case "std":
							if ($art>="700" && $art<="799") {
								$animals="mammals";
								$speciesFieldName="speciesMammals";
							}
							else {
								$animals="birds";
								$speciesFieldName="species";
							}
							$animalsDataField=$animals;

							break;

						case "vinter":
							
							$animals="birds";
							$speciesFieldName="species";
							$animalsDataField=$animals;

							break;

						case "natt":
							$mammalsMode="";
							if(strlen($art)>3) {
								$mammalsMode=$art[3];
								$art=substr($art, 0, 3);
							}

							if ($art>="128" && $art<="138" && $mammalsMode=="k") {
								$animals="owls";
								$animalsDataField=$animals;
								$speciesFieldName="speciesYoungOwl";
							}
							elseif ($art>="001" && $art<="699") {
								$animals="birds";
								$animalsDataField=$animals;
								$speciesFieldName="species";
							}
							elseif ($art>="700" && $art<="799") {
								$animals="mammals";
								if ($mammalsMode=="T") {
									$animalsDataField="mammalsOnRoad";
									$speciesFieldName="speciesMammalsOnRoad";
								}
								else {
									$animalsDataField="mammals";
									$speciesFieldName="speciesMammals";
								}

							}
							elseif ($art>="800" && $art<="899") {
								$animals="amphibians";
								$animalsDataField=$animals;
								$speciesFieldName="speciesAmphibians";
							}

							break;
					}



					//$data_field[$animalsDataField].='{';

					$arrP=array();
					$arrL=array();

					$colP="B";
					$colL="J";

					$nbSpP=0;

					for ($iP=1; $iP<=$nbPts ; $iP++) {

						$arrP[$iP]=($worksheet->getCell($colP.$iRowSpecies)->getValue()!="" ? $worksheet->getCell($colP.$iRowSpecies)->getValue() : 0);

						$data_field[$animalsDataField]["P".str_pad($iP, 2, '0', STR_PAD_LEFT)]=$arrP[$iP];

						//$data_field[$animalsDataField].='"P'.str_pad($iP, 2, '0', STR_PAD_LEFT).'": "'.$arrP[$iP].'",
						//';


						if ($arrP[$iP]>0) {
							$nbSpP++;
						}

						if ($protocol!="vinter") {
							$arrL[$iP]=($worksheet->getCell($colL.$iRowSpecies)->getValue()!="" ? $worksheet->getCell($colL.$iRowSpecies)->getValue() : 0);

							$data_field[$animalsDataField]["L".str_pad($iP, 2, '0', STR_PAD_LEFT)]=$arrL[$iP];

							//$data_field[$animalsDataField].='"L'.str_pad($iP, 2, '0', STR_PAD_LEFT).'": "'.$arrL[$iP].'",
							//';
							$colL++;
						}

						$colP++;
					}

					switch($protocol) {
						case "std":
							// total - ind
							$arrP[0]=$worksheet->getCell("S".$iRowSpecies)->getOldCalculatedValue();
							$arrL[0]=$worksheet->getCell("T".$iRowSpecies)->getOldCalculatedValue();
							$IC=$arrP[0]+$arrL[0];

							$data_field[$animalsDataField]["pointCount"]=$arrP[0];
							$data_field[$animalsDataField]["lineCount"]=$arrL[0];

							//$data_field[$animalsDataField].='"pointCount" : '.$arrP[0].',
							//';
							//$data_field[$animalsDataField].='"lineCount" : '.$arrL[0].',
							//';
							break;
						case "vinter":

							$data_field[$animalsDataField]["pk"]=$nbSpP;
							//$data_field[$animalsDataField].='"pk" : "'.$nbSpP.'",
							//';

							$IC=$worksheet->getCell("X".$iRowSpecies)->getOldCalculatedValue();
							break;
						case "natt":
							$IC=$worksheet->getCell("X".$iRowSpecies)->getOldCalculatedValue();

							break;
					}


					if (isset($array_species_art[$art])) {
						$speciesFound++;
						$listId=$list_id[$animals];
						$sn=$array_species_art[$art]["sn"];
						$guid=$array_species_art[$art]["lsid"];
						$name=$array_species_art[$art]["nameSWE"];
						$rank=$array_species_art[$art]["rank"];
					}
					else {
						if ($debug) $consoleTxt.=consoleMessage("error", "No species guid for art ".$art);
						$speciesNotFound++;
						$listId="error-unmatched";
						$guid="";
						$rank="";
						$name=$worksheet->getCell('A'.$iRowSpecies)->getValue();
						$sn=$worksheet->getCell('A'.$iRowSpecies)->getValue();

						$fileRefused=true;

						/*
						$array_species_guid[$animals][$rtRecords["scientificname"]]="-1";

						if (isset($arrSpeciesNotFound[$rtRecords["scientificname"]]) && $arrSpeciesNotFound[$rtRecords["scientificname"]]>0)
							$arrSpeciesNotFound[$rtRecords["scientificname"]]++;
						else $arrSpeciesNotFound[$rtRecords["scientificname"]]=1;
						*/
					}
					$outputSpeciesId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");


					$data_field[$animalsDataField]["swedishRank"]=$rank;
					//$data_field[$animalsDataField].=
					//	'"swedishRank":"'.$rank.'",';

					$data_field[$animalsDataField][$speciesFieldName]=array(
						"listId" => $listId,
						"commonName" => "",
						"outputSpeciesId" => $outputSpeciesId,
						"scientificName" => $sn,
						"name" => $name,
						"guid" => $guid
					);

					/*$data_field[$animalsDataField].='
						"'.$speciesFieldName.'" : {
							"listId" : "'.$listId.'",
							"commonName" : "",
							"outputSpeciesId" : "'.$outputSpeciesId.'",
							"scientificName" : "'.$sn.'",
							"name" : "'.$name.'",
							"guid" : "'.$guid.'"
						},
						';*/

					$data_field[$animalsDataField]["individualCount"]=$IC;
					//$data_field[$animalsDataField].=
					//	'"individualCount" : '.$IC.'
					//},';


					$occurenceID=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");


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
						"locationID" => $array_sites[$siteKey]["locationID"],
						"locationName" => $array_sites[$siteKey]["locationName"],
						"locationRemarks" => "",
						"eventID" => $eventID,
						"eventTime" => $start_time,
						"eventRemarks" => $eventRemarks,
						"notes" => $notes,
						"guid" => $guid,
						"name" => $name,
						"scientificName" => $sn,
						"multimedia" => array(),
						"activityId" => $activityId,
						"decimalLatitude" => $array_sites[$siteKey]["decimalLatitude"],
						"decimalLongitude" => $array_sites[$siteKey]["decimalLongitude"],
						"eventDate" => $eventDate,
						"individualCount" => $IC,
						"outputId" => $outputId,
						"outputSpeciesId" => $outputSpeciesId,
						"projectActivityId" => $commonFields[$protocol]["projectActivityId"],
						"projectId" => $commonFields[$protocol]["projectId"],
						"userId" => $commonFields["userId"]
					);
					/*
					$arr_json_record.='{
						"dateCreated" : ISODate("'.$date_now_tz.'"),
						"lastUpdated" : ISODate("'.$date_now_tz.'"),
						"occurrenceID" : "'.$occurenceID.'",
						"status" : "active",
						"recordedBy" : "'.$recorder_name.'",
						"rightsHolder" : "'.$commonFields["rightsHolder"].'",
						"institutionID" : "'.$commonFields["institutionID"].'",
						"institutionCode" : "'.$commonFields["institutionCode"].'",
						"basisOfRecord" : "'.$commonFields["basisOfRecord"].'",
						"datasetID" : "'.$commonFields[$protocol]["projectActivityId"].'",
						"datasetName" : "'.$commonFields[$protocol]["datasetName"].'",
						"licence" : "'.$commonFields["licence"].'",
						"locationID" : "'.$array_sites[$siteKey]["locationID"].'",
						"locationName" : "'.$array_sites[$siteKey]["locationName"].'",
						"locationRemarks" : "",
						"eventID" : "'.$eventID.'",
						"eventTime" : "'.$start_time.'",
						"eventRemarks" : "'.$eventRemarks.'",
						"notes" : "'.$notes.'",
						"guid" : "'.$guid.'",
						"name" : "'.$name.'",
						"scientificName" : "'.$sn.'",
						"multimedia" : [ ],
						"activityId" : "'.$activityId.'",
						"decimalLatitude" : '.$array_sites[$siteKey]["decimalLatitude"].',
						"decimalLongitude" : '.$array_sites[$siteKey]["decimalLongitude"].',
						"eventDate" : "'.$eventDate.'",
						"individualCount" : '.$IC.',
						"outputId" : "'.$outputId.'",
						"outputSpeciesId" : "'.$outputSpeciesId.'",
						"projectActivityId" : "'.$commonFields[$protocol]["projectActivityId"].'",
						"projectId" : "'.$commonFields[$protocol]["projectId"].'",
						"userId" : "'.$commonFields["userId"].'"
					},';
					*/
				}

				$iRowSpecies++;

			}

			/*
			// replace last comma by 
			foreach ($list_id as $animals => $listId) {
				if (strlen($data_field[$animals])>0)
					$data_field[$animals][strlen($data_field[$animals])-1]=' ';
			}
			if (isset($data_field["mammalsOnRoad"]) && strlen($data_field["mammalsOnRoad"])>0) $data_field["mammalsOnRoad"][strlen($data_field["mammalsOnRoad"])-1]=' ';
			//echo "data_field: ".$data_field[$animals]."\n";
			*/

			$specific_fields=array();

			//$specific_fields="";

			switch ($protocol) {

				case "std":

					$specific_fields["timeOfObservation"]=$timeOfObservation;
					$specific_fields["distanceCovered"]=$distanceCovered;
					$specific_fields["minutesSpentObserving"]=$minutesSpentObserving;
					$specific_fields["minutesSpentObserving"]=$minutesSpentObserving;
					$specific_fields["mammalObservations"]=$data_field["mammals"];

					/*$specific_fields.='
					'.$timeOfObservation.'
					'.$distanceCovered.'
					'.$minutesSpentObserving.'
					"isGpsUsed" : "'.$isGpsUsed.'",
					"mammalObservations" : [
						'.$data_field["mammals"].'
					],
					'
					;*/

				break;

				case "vinter":
					/*
					switch ($transport){
						case 1:
						case 2:
						case 3:
						default:
							$transport_text="cykel eller moped";
							break;
					}

					switch ($snow){
						case 1:
						case 2:
						case 3:
						default:
							$snow_text="cykel eller moped";
							break;
					}
					*/
					$specific_fields["transport"]=$transport;
					$specific_fields["snow"]=$snow;
					$specific_fields["period"]=$periodFN;

					/*
					$specific_fields.=
					'"transport" : "'.$transport.'",
					"snow" : "'.$snow.'",
					"period" : "'.$periodFN.'",';
					*/
					break;

				case "natt":

					$specific_fields["cloudsStart"]=$cloudsStart;
					$specific_fields["temperatureStart"]=$tempStart;
					$specific_fields["windStart"]=$windStart;
					$specific_fields["precipitationStart"]=$precipStart;
					$specific_fields["cloudsEnd"]=$cloudsEnd;
					$specific_fields["temperatureEnd"]=$tempEnd;
					$specific_fields["windEnd"]=$windEnd;
					$specific_fields["precipitationEnd"]=$precipEnd;
					$specific_fields["period"]=$per;
					$specific_fields["timeOfObservation"]=$timeOfObservation;
					$specific_fields["mammalsCounted"]="ja";
					$specific_fields["mammalObservations"]=$data_field["mammals"];
					$specific_fields["mammalObservationsOnRoad"]=$data_field["mammalsOnRoad"];
					$specific_fields["youngOwlObservations"]=$data_field["owls"];
					$specific_fields["amphibianObservations"]=$data_field["amphibians"];
					$specific_fields["amphibiansCounted"]="ja";
					/*
					$specific_fields.=
					'"cloudsStart" : "'. $cloudStart.'",
					"temperatureStart" : "'. $tempStart.'",
					"windStart" : "'. $windStart.'",
					"precipitationStart" : "'. $precipStart.'",
					"cloudsEnd" : "'. $cloudEnd.'",
					"temperatureEnd" : "'. $tempEnd.'",
					"windEnd" : "'. $windEnd.'",
					"precipitationEnd" : "'. $precipEnd.'",
					"period" : "'.$per.'",'.
					$timeOfObservation.
					'
					"mammalObservations" : [
						'.$data_field["mammals"].'
					],
					"mammalsCounted" : "ja",
					"mammalObservationsOnRoad" : [
						'.$data_field["mammalsOnRoad"].'
					],
					"youngOwlObservations" : [
						'.$data_field["owls"].'
					],
					"amphibiansCounted" : "ja",
					"amphibianObservations" : [
						'.$data_field["amphibians"].'
					],';
					*/
				break;


			}

			$arr_json_activity[]=array(
				"activityId" => $activityId,
				"assessment" => false,
				"dateCreated" => $nowISODate,
				"lastUpdated" => $nowISODate,
				"progress" => "planned",
				"projectActivityId" => $commonFields[$protocol]["projectActivityId"],
				"projectId" => $commonFields[$protocol]["projectId"],
				"projectStage" => "",
				"siteId" => $array_sites[$siteKey]["locationID"],
				"status" => "active",
				"type" => $commonFields[$protocol]["type"],
				"userId" => $commonFields["userId"],
				"personId" => $personId,
				"mainTheme" => "",
				"verificationStatus" => "not verified",
				"excelFile" => $file
			);

			/*
			$arr_json_activity.='{
				"activityId" : "'.$activityId.'",
				"assessment" : false,
				"dateCreated" : ISODate("'.$date_now_tz.'"),
				"lastUpdated" : ISODate("'.$date_now_tz.'"),
				"progress" : "planned",
				"projectActivityId" : "'.$commonFields[$protocol]["projectActivityId"].'",
				"projectId" : "'.$commonFields[$protocol]["projectId"].'",
				"projectStage" : "",
				"siteId" : "'.$array_sites[$siteKey]["locationID"].'",
				"status" : "active",
				"type" : "'.$commonFields[$protocol]["type"].'",
				"userId" : "'.$commonFields["userId"].'",
				"personId" : "'.$personId.'",
				"mainTheme" : "",
				"verificationStatus" : "not verified",
				"excelFile" : "'.$file.'"
			},';
			*/

			$data_array=array (
				"eventRemarks" => $eventRemarks,
				"surveyFinishTime" => $finish_time,
				"locationAccuracy" => 50,
				"comments" => $comments,
				"surveyDate" => $date_survey,
				"locationHiddenLatitude" => $array_sites[$siteKey]["decimalLatitude"],
				"locationLatitude" => $array_sites[$siteKey]["decimalLatitude"],
				"locationSource" => "Google maps",
				"recordedBy" => $recorder_name,
				"helpers" => $helpers,
				"surveyStartTime" => $start_time,
				"locationCentroidLongitude" => null,
				"observations" => array(
					$data_field["birds"]
				),
				"location" => $array_sites[$siteKey]["locationID"],
				"locationLongitude" => $array_sites[$siteKey]["decimalLongitude"],
				"locationHiddenLongitude" => $array_sites[$siteKey]["decimalLongitude"],
				"locationCentroidLatitude" => null
			);
			$data_array=array_merge($specific_fields, $data_array);

			$arr_json_output[]=array(
				"activityId" => $activityId,
				"dateCreated" => $nowISODate,
				"lastUpdated" => $nowISODate,
				"outputId" => $outputId,
				"status" => "active",
				"outputNotCompleted" => false,
				"data" => $data_array,
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

			/*
			$arr_json_output.='{
				"activityId" : "'.$activityId.'",
				"dateCreated" : ISODate("'.$date_now_tz.'"),
				"lastUpdated" : ISODate("'.$date_now_tz.'"),
				"outputId" : "'.$outputId.'",
				"status" : "active",
				"outputNotCompleted" : false,
				"data" : {
					"eventRemarks" : "'.$eventRemarks.'",
					"surveyFinishTime" : "'.$finish_time.'",
					"locationAccuracy" : 50,
					"comments" : "'.$comments.'",
					"surveyDate" : "'.$date_survey.'",
					'.$specific_fields.'
					"locationHiddenLatitude" : '.$array_sites[$siteKey]["decimalLatitude"].',
					"locationLatitude" : '.$array_sites[$siteKey]["decimalLatitude"].',
					"locationSource" : "Google maps",
					"recordedBy" : "'.$recorder_name.'",
					"helpers" : '.$helpers.',
					"surveyStartTime" : "'.$start_time.'",
					"locationCentroidLongitude" : null,
					"observations" : [
						'.$data_field["birds"].'
					],
					"location" : "'.$array_sites[$siteKey]["locationID"].'",
					"locationLongitude" : '.$array_sites[$siteKey]["decimalLongitude"].',
					"locationHiddenLongitude" : '.$array_sites[$siteKey]["decimalLongitude"].',
					"locationCentroidLatitude" : null
				},
				"selectFromSitesOnly" : true,
				"_callbacks" : {
					"sitechanged" : [
						null
					]
				},
				"mapElementId" : "locationMap",
				"checkMapInfo" : {
					"validation" : true
				},
				"name" : "'.$commonFields[$protocol]["name"].'",
				"dataOrigin" : "'.$dataOrigin.'"
			},';
			*/




		}
		else {
			$consoleTxt.=consoleMessage("error", "UNKNOWN SITE");
			$fileRefused=true;
		}
	}




}



$ratioSpecies = $speciesFound / ($speciesFound+$speciesNotFound);

$consoleTxt.=consoleMessage("info", "Species ratio found in the species lists : ".$speciesFound." / ".($speciesFound+$speciesNotFound)." = ".number_format($ratioSpecies*100, 2)."%");

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
