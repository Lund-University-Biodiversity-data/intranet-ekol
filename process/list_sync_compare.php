<?php

$listData=array();
foreach ($listIdsToCompare as $listId) {
  $listData[$listId]=getListSpeciesFromModule(null, $listId, "art");

  $consoleTxt.=consoleMessage("info", count($listData[$listId])." element(s) in list ".$listId);
}

$tabArtRef=array();

// Get the eurolist table as base
$db_connection = pg_connect("host=".$DB["host"]." dbname=".$DB["database"]." user=".$DB["username"]." password=".$DB["password"])  or die("CONNECT:" . consoleMessage("error", pg_result_error()));

$qArt='SELECT art, dyntaxa_id, arthela, latin, rank FROM eurolist ORDER BY art';
$rArt = pg_query($db_connection, $qArt);
while ($rowArt = pg_fetch_array($rArt)) {
  $tabArtRef[]=$rowArt;
}
$consoleTxt.=consoleMessage("info", count($tabArtRef)." element(s) in eurolist");

$tabRecap=array();

foreach ($tabArtRef as $rowArt) {
  
  foreach ($listData as $listId => $oneList) {

    if (isset($oneList[$rowArt["art"]])) {

      $errorsRow=array();

      if ($rowArt["dyntaxa_id"]!=$oneList[$rowArt["art"]]["dyntaxaId"]) {
        $consoleTxt.=consoleMessage("error", "Different dyntaxaId for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["dyntaxa_id"]."/".$oneList[$rowArt["art"]]["dyntaxaId"]);
        $errorsRow[]="dyntaxa_id";
      }
      if ($rowArt["latin"]!=$oneList[$rowArt["art"]]["name"]) {
        $consoleTxt.=consoleMessage("error", "Different scientificName for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["latin"]."/".$oneList[$rowArt["art"]]["name"]);
        $errorsRow[]="latin";
      }
      if ($rowArt["arthela"]!=$oneList[$rowArt["art"]]["nameSWE"]) {
        $consoleTxt.=consoleMessage("error", "Different swedishName for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["arthela"]."/".$oneList[$rowArt["art"]]["nameSWE"]);
        $errorsRow[]="arthela";
      }
      if ($rowArt["rank"]!=$oneList[$rowArt["art"]]["rank"]) {
        $consoleTxt.=consoleMessage("error", "Different rank for art ".$rowArt["art"]." in ".$listId." : ".$rowArt["rank"]."/".$oneList[$rowArt["art"]]["rank"]);
        $errorsRow[]="rank";
      }

      if (count($errorsRow)>0) {
        if (!isset($tabRecap[$rowArt["art"]])) {
          $tabRecap[$rowArt["art"]]=array();

          $tabRecap[$rowArt["art"]]["eurolist"]=array(
            "dyntaxa_id" => $rowArt["dyntaxa_id"],
            "latin" => $rowArt["latin"],
            "arthela" => $rowArt["arthela"],
            "rank" => $rowArt["rank"],
            "errors" => []
          );
        }

        $tabRecap[$rowArt["art"]][$listId]=array(
          "dyntaxa_id" => $oneList[$rowArt["art"]]["dyntaxaId"],
          "latin" => $oneList[$rowArt["art"]]["name"],
          "arthela" => $oneList[$rowArt["art"]]["nameSWE"],
          "rank" => $oneList[$rowArt["art"]]["rank"],
          "errors" => $errorsRow
        );
      }

    }
  }

}
