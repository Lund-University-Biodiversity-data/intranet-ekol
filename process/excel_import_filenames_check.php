<?php
// 2** FILENAME CHECK




$filesSurveys = scandir($path_excel);

$listFiles=array();


foreach($filesSurveys as $file) {
    if ($file!=".." && $file!="." && (substr($file, strlen($file)-4, 4)==".xls" ||substr($file, strlen($file)-5, 5)==".xlsx")) {


        //extract the filename without the extension
        if (substr($file, strlen($file)-4, 4)==".xls") {
            $filename=substr($file, 0, strlen($file)-4);
        }
        elseif (substr($file, strlen($file)-5, 5)==".xlsx") {
            $filename=substr($file, 0, strlen($file)-5)==".xlsx";
        }

        $explodeFilename=explode("-", $filename);
        $prefixFN=$explodeFilename[0];
        $siteIdFN1=$explodeFilename[1];//persnr
        $siteIdFN2=$explodeFilename[2];//indice
        $siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
        $siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;
        $periodFN=str_replace("P", "", $explodeFilename[4]);

        switch($protocol) {
            case "std":
                break;
            case "vinter":
                $internalSiteId=$siteIdFN;
                $templateFileName="Vin";
                $yearStudied=substr($prefixFN, strlen($templateFileName), 2);
                $yearFull="20".$yearStudied;
                break;
            case "natt":
                break;
            case "kust":
                break;
        }

        $infoFile=array();
        $infoFile["filename"]=$file;
        $infoFile["internalSiteId"]=$internalSiteId;
        $infoFile["period"]=$periodFN;

        /*
        echo "Year Full:".$yearFull."<br>";
        echo $siteIdFN."<br>";
        print_r($tabSitesPeriod[$siteIdFN]);
        echo "<br>";
        */

        if ($prefixFN!=$templateFileName.$yearStudied) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$templateFileName. "'. '".$prefixFN."' instead");
            $infoFile["status"]="NO => wrong filename, does not start with correct template.";
        }

        elseif(isset($array_sites[$siteIdFN])) {
            if (in_array($yearFull."-".$periodFN, $tabSitesPeriod[$siteIdFN])) {
                $consoleTxt.=consoleMessage("error", $file. " can't be processed, period '".$periodFN. "' already existing for site '".$siteIdFN."' and year ".$yearFull);
                $infoFile["status"]="NO => period already exists in MongoDb";

            }
            else {
                $consoleTxt.=consoleMessage("info", $file. " OK to be processed for period '".$periodFN. "' and site '".$siteIdFN."'");
                $infoFile["status"]="OK";
                
                $listHiddenOkFiles.=$file.FILENAME_SEPARATOR;
            }
        }
        else {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed because site '".$siteIdFN."' does not exist");
            $infoFile["status"]="NO => site does not exist in MongoDb";
        }

        $listFiles[]=$infoFile;

    }


}


$consoleTxt.=consoleMessage("info", count($listFiles)." files found.");


// END 2** FILENAME CHECK

?>