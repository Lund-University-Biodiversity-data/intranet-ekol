<?php

$okList=true;

$arrSpeciesNotFound=array();
$speciesNotFound=0;
$speciesFound=0;
$observationFieldName="observations";

$array_species_guid=array();
$array_species_art=array();
// GET the list of species


foreach ($list_id as $animals => $listId) {

	$consoleTxt.=consoleMessage("info", "Get info from species list ".$animals.", ".$listId);

	$url=URL_LISTS_ITEMS.$list_id[$animals].URL_LISTS_INCLUDE_KBV;
	$obj = json_decode(file_get_contents($url), true);

	foreach($obj as $iObj => $sp) {

		//$array_species_guid[$sp["scientificName"]]=$sp["lsid"];
		$array_species_guid[$animals][$sp["name"]]["lsid"]=$sp["lsid"];

		if ($sp["lsid"]!="") {

			$art="";
			$nameSWE="";
			$rank="";

			$nbEltSp=$sp["kvpValues"];
			$continue=true;
			$iSp=0;

			foreach($sp["kvpValues"] as $iSp => $eltSp) {

				if (isset($eltSp["key"]) && $eltSp["key"]=="art") {
					$art=$eltSp["value"];
					$art=str_pad($art, 3, '0', STR_PAD_LEFT);
				}	

				if (isset($eltSp["key"]) && $eltSp["key"]=="rank") {
					$rank=$eltSp["value"];
				}	

				if (isset($eltSp["key"]) && $eltSp["key"]=="arthela") {
					$nameSWE=$eltSp["value"];
				}

			}

			if ($art=="") {
				$consoleTxt.=consoleMessage("error", "No art for ".$sp["name"]."/".$sp["lsid"]." (".$iObj.")");
				$art="-";
				$okList=false;
			}
			if ($nameSWE=="") {
				$consoleTxt.=consoleMessage("error", "No name for ".$sp["name"]."/".$sp["lsid"]." (".$iObj.")");
				$nameSWE="-";
				$okList=false;
			}
			if ($rank=="") {
				$consoleTxt.=consoleMessage("error", "No rank for ".$sp["name"]."/".$sp["lsid"]." (".$iObj.")");
				$rank="-";
				$okList=false;
			}

			$array_species_art[$art]["lsid"]=$sp["lsid"];
			$array_species_art[$art]["sn"]=$sp["name"];
			$array_species_art[$art]["nameSWE"]=$nameSWE;
			$array_species_art[$art]["rank"]=$rank;

			/*
			$url="https://lists.bioatlas.se/ws/species/".$sp["lsid"];
			$species = json_decode(file_get_contents($url), true);

			if (isset($species[count($species)-1]["kvpValues"][0]["key"]) && $species[count($species)-1]["kvpValues"][0]["key"]=="art"){
				$art=$species[count($species)-1]["kvpValues"][0]["value"];
				$art=str_pad($art, 3, '0', STR_PAD_LEFT);
				$nameSWE=$species[count($species)-1]["kvpValues"][1]["value"];
			}
			elseif (isset($species[count($species)-2]["kvpValues"][0]["key"]) && $species[count($species)-2]["kvpValues"][0]["key"]=="art"){
				$art=$species[count($species)-2]["kvpValues"][0]["value"];
				$art=str_pad($art, 3, '0', STR_PAD_LEFT);
				$nameSWE=$species[count($species)-2]["kvpValues"][1]["value"];
			}
			else {
				$art="-";
				$nameSWE="-";
				$consoleTxt.=consoleMessage("error", "No art for ".$sp["name"]."/".$sp["lsid"]);
			}

			$array_species_art[$art]["lsid"]=$sp["lsid"];
			$array_species_art[$art]["sn"]=$sp["name"];
			$array_species_art[$art]["nameSWE"]=$nameSWE;

			$line=$art."#".$array_species_art[$art]["lsid"]."#".$array_species_art[$art]["sn"]."#".$array_species_art[$art]["nameSWE"]."\n";
			fwrite($fp, $line);

			*/
		}
		//else $consoleTxt.=consoleMessage("error", "No lsid for ".$sp["name"]);		
	}

}

?>