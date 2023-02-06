<?php

$listAvailable=array();

$url="https://lists.biodiversitydata.se/ws/speciesList";
$obj = json_decode(file_get_contents($url), true);

$arrOkUserNames=array("aleksandra.magdziarek@biol.lu.se", "mathieu.blanchet@biol.lu.se");

if (isset($obj["lists"])) {
  foreach($obj["lists"] as $iObj => $sp) {

    if (in_array($sp["username"], $arrOkUserNames)) {
    
      $listAvailable[$sp["dataResourceUid"]]=$sp["listName"];
    }


  }
}
else {
  $consoleTxt.=consoleMessage("error", "No lists available on ".$url);
}
