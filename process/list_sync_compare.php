<?php

$listData=array();
foreach ($listIdsToCompare as $listId) {
  $listData[$listId]=getListSpeciesFromModule(null, $listId, "art");

  $consoleTxt.=consoleMessage("info", count($listData[$listId])." element(s) in list ".$listId);
}

$tabArtRef=array();

// Get the eurolist table as base
$db_connection = pg_connect("host=".$DB["host"]." dbname=".$DB["database"]." user=".$DB["username"]." password=".$DB["password"])  or die("CONNECT:" . consoleMessage("error", pg_result_error()));

$qArt='SELECT * FROM eurolist ORDER BY art';
$rArt = pg_query($db_connection, $qArt);
while ($rowArt = pg_fetch_array($rArt)) {
  $tabArtRef[]=$rowArt;
}

$tabRecap=array();

foreach ($tabArtRef as $rowArt) {
  
  foreach ($listData as $listId => $oneList) {

    if (isset($oneList[$rowArt["art"]])) {

      /*if (!isset($tabRecap[$rowArt["art"]])) {
        $tabRecap[$rowArt["art"]]=array();

        $tabRecap[$rowArt["art"]]["eurolist"]=array(
          "dyntaxa_id" => $rowArt["dyntaxa_id"],
          "latin" => $rowArt["latin"]
        );
      }

      $tabRecap[$rowArt["art"]][$listId]=array(
        "dyntaxa_id" => $oneList[$rowArt["art"]]["dyntaxaId"],
        "latin" => $oneList[$rowArt["art"]]["name"]
      );*/

      if ($rowArt["dyntaxa_id"]!=$oneList[$rowArt["art"]]["dyntaxaId"]) {
        $consoleTxt.=consoleMessage("error", "Different dyntaxaId for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["dyntaxa_id"]."/".$oneList[$rowArt["art"]]["dyntaxaId"]);
      }
      if ($rowArt["latin"]!=$oneList[$rowArt["art"]]["name"]) {
        $consoleTxt.=consoleMessage("error", "Different scientificName for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["latin"]."/".$oneList[$rowArt["art"]]["name"]);
      }
    }
  }

}
