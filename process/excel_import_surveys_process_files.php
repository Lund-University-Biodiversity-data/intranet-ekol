<?php

use MongoDB\BSON\UTCDateTime;

// 4** PROCESS FOR OK FILES

switch($protocol) {
	case "std":
		$nbPts=8;
		break;
	case "sommar":
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

	$userId=$commonFields["userId"];

	switch ($protocol) {

		case "std":
		case "natt":
			$siteKeyFilename = substr($file, 0, 5);

			// get period from filename
			$explodeFilename=explode(" ", $file);
			$kartaPeriod=$explodeFilename[0];
			//$yearFull=$explodeFilename[1];
			$explodeKP=explode("-", $kartaPeriod);
			//$kartaTx=$explodeKP[0];
			$periodFN=$explodeKP[1];
			
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

	$tmpfname = PATH_INPUT_EXCEL_SURVEYS.$database."/".$protocol."/".$file;
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

		//$recordReadyAdded=0;
		//$lastRow = $worksheet->getHighestRow();

		$arrAntiDoublonArt=array();

		switch($protocol) {
			case "natt":

				$datumExpectedFormat="YYYYMMDD";

				$kartakod=$worksheet->getCell('A9')->getValue();
				$ruttname=$worksheet->getCell('D9')->getValue(); // not used
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
				$adress=$worksheet->getCell('B16')->getValue(); // not used
				$post=$worksheet->getCell('B17')->getValue(); // not used
				$city=$worksheet->getCell('F17')->getValue(); // not used
				$email=$worksheet->getCell('M15')->getValue(); // not used
				$address1=$recorder_name; // not used
				$address2=$worksheet->getCell('B16')->getValue(); // not used
				$tel=$worksheet->getCell('M16')->getValue(); // not used
				$mobile=$worksheet->getCell('M17')->getValue(); // not used

				$cloudStart=$worksheet->getCell('B21')->getValue();
				$tempStart=$worksheet->getCell('C21')->getValue();
				$windStart=$worksheet->getCell('D21')->getValue();
				$precipStart=$worksheet->getCell('E21')->getValue();

				$cloudEnd=$worksheet->getCell('F21')->getValue();
				$tempEnd=$worksheet->getCell('G21')->getValue();
				$windEnd=$worksheet->getCell('H21')->getValue();
				$precipEnd=$worksheet->getCell('I21')->getValue();

				$eventRemarks=str_replace('"', "'", $worksheet->getCell('A165')->getValue());
				$notes="";
				$comments=$notes;

				$rowStartTime=25;
				$iRowSpecies=30;
				$colSpeciesCode="V";
				$colTotObs="Z";

				$siteKey=$kartakod;

				// check the period
				if ($per!=$periodFN) {
					$consoleTxt.=consoleMessage("error", "Not the same period in the filename and file content ! ".$per." VS ".$periodFN);
					$fileRefused=true;
				}

				// compare the period format and the consistency with the month
				if ($per>=1 && $per<=3) {
					if (isset($datum[5])) {
						$month=$datum[5];
						if (
							($month==3 && $per==1) ||
							($month==4 && $per==2) ||
							($month==6 && $per==3)
						) {}
						else {
							$consoleTxt.=consoleMessage("error", "Period/Month inconsistent. Must be P1/M3 or P2/M4 or P3/M6 ! Got instead ".$per."/".$month);
							$fileRefused=true;
						}
					}
					
				}
				else {
					$consoleTxt.=consoleMessage("error", "Wrong period, must be 1, 2, 3. Data in the file : ".$per);
					$fileRefused=true;
				}

				$rowStartTime=25;

				// get the start_time/finish_time + timeOfObservation

				// find the start time and finish time
				$start_time=2359;
				$finish_time=0;
				$timeOfObservation=array();

				$iInd=1;
				for ($iCol="B";$iCol<="U";$iCol++) {
					$val=$worksheet->getCell($iCol.$rowStartTime)->getValue();

					if ($val!="" && !is_numeric($val)) {
						$consoleTxt.=consoleMessage("error", "TimeOfObservation: wrong format for cell ".$iCol.$rowStartTime);
						$fileRefused=true;
					}

					$timeOfObservation["TidP".str_pad($iInd, 2, '0', STR_PAD_LEFT)]=strval($val);
					
					if ($val!="") {
						$val=intval($val);

						if ($val<0 || $val>2400) {
							$consoleTxt.=consoleMessage("error", "time value in cell ".$iCol.$rowStartTime." (".$val.") must be between 0 and 2400");
							$fileRefused=true;
						}
						// add 24 hours to the night times, to help comparing
						if ($val<1200)
							$val+=2400;

						if ($val<$start_time)
							$start_time=$val;

						if ($val>$finish_time)
							$finish_time=$val;

						if ($val>2400) $val-=2400;
					}

					$iInd++;
				}


				if ($start_time>2400) $start_time-=2400;
				if ($finish_time>2400) $finish_time-=2400;

				$start_time_brut=$start_time;

				// add extra minutes (natt : 5)
				$finish_time=addTimeToFinish($finish_time, 5);

				$rowDisturbance=146;
				$disturbances=array();
				// check the header, because it has to be in that line
				if ($worksheet->getCell("A".($rowDisturbance-1))->getValue()!="LJUDFÖRORENINGAR"){
					$consoleTxt.=consoleMessage("error", "Can't find the data for LJUDFÖRORENINGAR in expected cell (A".($rowDisturbance-1).")");
					$fileRefused=true;
				}
				else {
					$iInd=1;
					$disturbancesSum=0;
					for ($iCol="B";$iCol<="U";$iCol++) {
						$val=$worksheet->getCell($iCol.$rowDisturbance)->getValue();
						if ($val!="" && $val!=1) {
							$consoleTxt.=consoleMessage("error", "LJUDFÖRORENINGAR/disturbances only '1' allowed in cell ".$iCol.$rowDisturbance);
							$fileRefused=true;
						}
						else $disturbancesSum+=$val;
						if ($val=="") $val=0;
						$disturbances["P".str_pad($iInd, 2, '0', STR_PAD_LEFT)]=strval($val);
						$iInd++;
					}
					if (!$fileRefused) {
						$consoleTxt.=consoleMessage("info", "LJUDFÖRORENINGAR/disturbances sum is ".$disturbancesSum);
					}
					
				}

				$checkTotalData[0]["animals"]='birds';
				$checkTotalData[0]["textNumberSpeciesFound"]="Antal fågelarter totalt";
				$checkTotalData[0]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[0]["columnValueNumberSpeciesFound"]='K';

				$checkTotalData[1]["animals"]='mammals';
				$checkTotalData[1]["textNumberSpeciesFound"]="Antal däggdjursarter totalt på punkter";
				$checkTotalData[1]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[1]["columnValueNumberSpeciesFound"]='K';

				$checkTotalData[2]["animals"]='mammalsOnRoad';
				$checkTotalData[2]["textNumberSpeciesFound"]="Antal däggdjursarter totalt på transportsträckor";
				$checkTotalData[2]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[2]["columnValueNumberSpeciesFound"]='K';

				$checkTotalData[3]["animals"]='amphibians';
				$checkTotalData[3]["textNumberSpeciesFound"]="Antal groddjursarter totalt på punkter";
				$checkTotalData[3]["columnTextNumberSpeciesFound"]='A';
				$checkTotalData[3]["columnValueNumberSpeciesFound"]='K';


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

		if (isset($transport) && trim($transport)!="") {
			if (!is_numeric($transport) || $transport<0 || $transport>4 ) {
				$consoleTxt.=consoleMessage("error", "Transport field is supposed to be a number between 1 and 4, instead of : ".$transport);
				$fileRefused=true;
			}
		}

		if (isset($snow) && trim($snow)!="") {
			if (!is_numeric($snow) || $snow<0 || $snow>3 ) {
				$consoleTxt.=consoleMessage("error", "Snow field is supposed to be a number between 1 and 3, instead of : ".$snow);
				$fileRefused=true;
			}
		}

		if (isset($distance) && trim($distance)!="") {
			if (!is_numeric($distance)) {
				$consoleTxt.=consoleMessage("error", "Distance field is supposed to be a number, instead of : ".$distance);
				$fileRefused=true;
			}
		}

		if (isset($startTime) && (!is_numeric($startTime) || strpos($startTime, ".")!==false)) {
			$consoleTxt.=consoleMessage("error", "Expected format for startTime : HHMM. Obtained : ".$startTime);
			$fileRefused=true;
		}
		if (isset($endTime) && (!is_numeric($endTime)  || strpos($endTime, ".")!==false)) {
			$consoleTxt.=consoleMessage("error", "Expected format for endTime : HHMM. Obtained : ".$endTime);
			$fileRefused=true;
		}

		if (!isset($datum) || trim($datum)=="" || (strlen($datum)!=strlen($datumExpectedFormat))) {
			$consoleTxt.=consoleMessage("error", "Wrong format for datum (".$datumExpectedFormat.") : ".$datum);
			$fileRefused=true;
		}

		if ($siteKeyFilename!=$siteKey) {
			$consoleTxt.=consoleMessage("error", "Filename site id (".$siteKeyFilename.") and site id in the file (".$siteKey.") don't match ");
			$fileRefused=true;
		}

		// in case of a 6 digit date, we add 20
		if (strlen($datum)==6) $datum="20".$datum;

		$date_survey=date("Y-m-d", strtotime($datum))."T00:00:00Z";

		$consoleTxt.=consoleMessage("info", "siteKey / ruttnamn / datum : ".$siteKey." / ".$ruttname." / ".$datum." / ".$date_survey);

		if (isset($array_sites[$siteKey])) {

			
			if ($protocol=="vinter" || $protocol=="sommar") {
				$start_time=$startTime;
				$finish_time=$endTime;
			}

			if ($start_time!="N/A" && $start_time!="") {
				$start_time=convertTime($start_time, "24H");
				$eventDate=date("Y-m-d", strtotime($datum))."T".$start_time.":00Z";
			}
			else {
				$eventDate=date("Y-m-d", strtotime($datum))."T00:00:00Z";

			}

			// when finish_time == 0 ! (midnight)
			// for some reason this test has to be split, if not, the finishtime=0 is considered as empty and N/A (WTF??????)
			if (is_numeric($finish_time) && $finish_time==0)
				$finish_time=convertTime($finish_time, "24H");
			else {

				if ($finish_time!="N/A" && $finish_time!="")
					$finish_time=convertTime($finish_time, "24H");
				else 
					$consoleTxt.=consoleMessage("warn", "finish_time not specified :".$finish_time);
			}


			//$eventTime=date("H:i", strtotime($rtEvents["datum"]));
					
			$activityId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
			$eventID=$activityId;

			$arrActivityId[$file]=$activityId;

			$outputId=generate_uniqId_format("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");

			$helpers=array();

			foreach ($list_id as $animals => $listId) {
				//$data_field[$animals]="";
				$data_field[$animals]=array();
				$recordReadyAdded[$animals]=0;
			}
			//$data_field["mammalsOnRoad"]="";
			$data_field["mammalsOnRoad"]=array();
			$recordReadyAdded["mammalsOnRoad"]=0;


			$listId = $list_id["birds"];

			// loop on the rows
			while ($worksheet->getCell($colTotObs.$iRowSpecies)->getValue() != "") {

				$dataAnimal=array();

				//echo "V".$iRowSpecies.":".$worksheet->getCell("V".$iRowSpecies)->getOldCalculatedValue()."(".$worksheet->getCell("S".$iRowSpecies)->getOldCalculatedValue()." - ".$worksheet->getCell("T".$iRowSpecies)->getOldCalculatedValue().")\n";

				// criteria to analyze the row
				$okAnalyzeRow=false;
				switch ($protocol) {
					case "std":
					case "natt":
						if ($worksheet->getCell($colTotObs.$iRowSpecies)->getOldCalculatedValue()==1) $okAnalyzeRow=true;
						break;
					case "sommar":
					case "vinter":
						if ($worksheet->getCell($colTotObs.$iRowSpecies)->getOldCalculatedValue()>0) {
							$okAnalyzeRow=true;
						}
						break;
				}
				
				if ($okAnalyzeRow) {

					$art=$worksheet->getCell($colSpeciesCode.$iRowSpecies)->getValue();

					// store the uncut art to avoid doublons in files
					$artUncut=$art;

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

						case "sommar":
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

						if (!is_numeric($arrP[$iP])) {
							$consoleTxt.=consoleMessage("error", "Non-numeric value in cell ".$colP.$iRowSpecies);
							$fileRefused=true;
						}
						$dataAnimal["P".str_pad($iP, 2, '0', STR_PAD_LEFT)]=$arrP[$iP];

						//$data_field[$animalsDataField].='"P'.str_pad($iP, 2, '0', STR_PAD_LEFT).'": "'.$arrP[$iP].'",
						//';


						if ($arrP[$iP]>0) {
							$nbSpP++;
						}

						if ($protocol=="std") {
							$arrL[$iP]=($worksheet->getCell($colL.$iRowSpecies)->getValue()!="" ? $worksheet->getCell($colL.$iRowSpecies)->getValue() : 0);

							if (!is_numeric($arrL[$iP])) {
								$consoleTxt.=consoleMessage("error", "Non-numeric value in cell ".$colL.$iRowSpecies);
								$fileRefused=true;
							}

							$dataAnimal["L".str_pad($iP, 2, '0', STR_PAD_LEFT)]=$arrL[$iP];

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

							$dataAnimal["pointCount"]=$arrP[0];
							$dataAnimal["lineCount"]=$arrL[0];

							//$data_field[$animalsDataField].='"pointCount" : '.$arrP[0].',
							//';
							//$data_field[$animalsDataField].='"lineCount" : '.$arrL[0].',
							//';
							break;
						case "sommar":
						case "vinter":

							$dataAnimal["pk"]=$nbSpP;
							//$data_field[$animalsDataField].='"pk" : "'.$nbSpP.'",
							//';

							$IC=$worksheet->getCell("X".$iRowSpecies)->getOldCalculatedValue();
							break;
						case "natt":
							$IC=$worksheet->getCell("X".$iRowSpecies)->getOldCalculatedValue();

							break;
					}

					// if the art has already been found in the file => error
					if (isset($arrAntiDoublonArt[$artUncut]) && is_numeric($arrAntiDoublonArt[$artUncut])) {
						$consoleTxt.=consoleMessage("error", "The species number ".$artUncut. ", line#$iRowSpecies, has already been found in the file at line#".$arrAntiDoublonArt[$artUncut].". Doublon !");
						$fileRefused=true;
					}
					elseif (isset($array_species_art[$art])) {
						// store the line where the species were found. Te be done now and before the cut of "xxxP" eller "xxxT"
						$arrAntiDoublonArt[$artUncut]=$iRowSpecies;

						$speciesFound++;
						$listId=$list_id[$animals];
						$sn=$array_species_art[$art]["sn"];
						$guid=$array_species_art[$art]["lsid"];
						$name=$array_species_art[$art]["nameSWE"];
						$rank=$array_species_art[$art]["rank"];
					}
					else {
						if (trim($art)=="") {
							$consoleTxt.=consoleMessage("error", "No art specified in row ".$iRowSpecies);
						}else {
							$consoleTxt.=consoleMessage("error", "No species guid for art ".$art.", in row ".$iRowSpecies);
						}
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


					$dataAnimal["swedishRank"]=$rank;
					//$data_field[$animalsDataField].=
					//	'"swedishRank":"'.$rank.'",';

					$dataAnimal[$speciesFieldName]=array(
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

					$dataAnimal["individualCount"]=$IC;
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
						"individualCount" => strval($IC),
						"outputId" => $outputId,
						"outputSpeciesId" => $outputSpeciesId,
						"projectActivityId" => $commonFields[$protocol]["projectActivityId"],
						"projectId" => $commonFields[$protocol]["projectId"],
						"userId" => strval($userId)
					);
					$recordReadyAdded[$animalsDataField]++;

					$data_field[$animalsDataField][]=$dataAnimal;
				}

				$iRowSpecies++;
			}

			foreach($checkTotalData as $iCTD => $CTD) {
				// at the end of the loop for processing the species rows, we try to find the "Antal arter totalt" to verify the number of rows expected
				// we check 200 rows max, should me more than enough
				$lineNumberSpeciesFound=false;
				$valueCheckNumberSpecies=0;
				for ($iCheckNumberRows=$iRowSpecies; $iCheckNumberRows<=$iRowSpecies+200; $iCheckNumberRows++) {

					if ($worksheet->getCell($CTD["columnTextNumberSpeciesFound"].$iCheckNumberRows)->getValue() == $CTD["textNumberSpeciesFound"]) {
						
						if (is_numeric($worksheet->getCell($CTD["columnValueNumberSpeciesFound"].$iCheckNumberRows)->getOldCalculatedValue())) {

							$valueCheckNumberSpecies=$worksheet->getCell($CTD["columnValueNumberSpeciesFound"].$iCheckNumberRows)->getOldCalculatedValue();
							$lineNumberSpeciesFound=true;
							$iCheckNumberRows=$iRowSpecies+200;

						}
						elseif (is_numeric($worksheet->getCell($CTD["columnValueNumberSpeciesFound"].$iCheckNumberRows)->getValue())) {

							$valueCheckNumberSpecies=$worksheet->getCell($CTD["columnValueNumberSpeciesFound"].$iCheckNumberRows)->getValue();
							$lineNumberSpeciesFound=true;
							$iCheckNumberRows=$iRowSpecies+200;

						}
					}
				}
				if (!$lineNumberSpeciesFound || trim($valueCheckNumberSpecies)=="") {
					$consoleTxt.=consoleMessage("error", "Something is wrong with the text/value for '".$CTD["textNumberSpeciesFound"]."' (numeric value ?)");
					$fileRefused=true;
				}
				else {

					if ($protocol=="natt" && $CTD["animals"]=="birds") {
						$recordCounted=$recordReadyAdded[$CTD["animals"]]+$recordReadyAdded["owls"];
					} 	
					else $recordCounted=$recordReadyAdded[$CTD["animals"]];

					if ($recordCounted != $valueCheckNumberSpecies) {
						$consoleTxt.=consoleMessage("error", "Missing records ! ".$valueCheckNumberSpecies." rows expected according to '".$CTD["textNumberSpeciesFound"]."' but ".$recordCounted." ready to be added");
						$fileRefused=true;
					}
					else {
						$consoleTxt.=consoleMessage("info", $recordCounted." records ready to be added for '".$CTD["animals"]."'. The control value is ".$valueCheckNumberSpecies." => OK");
					}
				}
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

					$specific_fields["timeOfObservation"]=array($timeOfObservation);
					$specific_fields["distanceCovered"]=array($distanceCovered);
					$specific_fields["minutesSpentObserving"]=array($minutesSpentObserving);
					$specific_fields["mammalObservations"]=$data_field["mammals"];
					$specific_fields["isGpsUsed"]=$isGpsUsed;

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

				case "sommar":
					$specific_fields["transport"]=$transport;
					$specific_fields["distance"]=$distance;
					$specific_fields["period"]=$periodFN;

					break;

				case "vinter":
					$specific_fields["transport"]=$transport;
					$specific_fields["snow"]=$snow;
					$specific_fields["distance"]=$distance;
					$specific_fields["period"]=$periodFN;

					break;

				case "natt":

					$specific_fields["cloudsStart"]=$cloudStart;
					$specific_fields["temperatureStart"]=$tempStart;
					$specific_fields["windStart"]=$windStart;
					$specific_fields["precipitationStart"]=$precipStart;
					$specific_fields["cloudsEnd"]=$cloudEnd;
					$specific_fields["temperatureEnd"]=$tempEnd;
					$specific_fields["windEnd"]=$windEnd;
					$specific_fields["precipitationEnd"]=$precipEnd;
					$specific_fields["period"]=$per;
					$specific_fields["timeOfObservation"]=array($timeOfObservation);
					$specific_fields["mammalsCounted"]=$mammalsCounted;
					$specific_fields["mammalObservations"]=$data_field["mammals"];
					$specific_fields["mammalObservationsOnRoad"]=$data_field["mammalsOnRoad"];
					$specific_fields["youngOwlObservations"]=$data_field["owls"];
					$specific_fields["amphibianObservations"]=$data_field["amphibians"];
					$specific_fields["amphibiansCounted"]=$amphibiansCounted;
					$specific_fields["disturbances"]=array($disturbances);
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
				"userId" => strval($userId),
				"personId" => $personId,
				"mainTheme" => "",
				"verificationStatus" => "approved",
				"excelFile" => $file
			);

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
				"observations" => $data_field["birds"],
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


		}
		else {
			$consoleTxt.=consoleMessage("error", "UNKNOWN SITE. Maybe not the correct cell in the file ? ");
			$fileRefused=true;
		}
	}



	if ($fileRefused) break;
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
