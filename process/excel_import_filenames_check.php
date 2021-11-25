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
            $filename=substr($file, 0, strlen($file)-5);
        }


        switch($protocol) {
            case "std":
                break;
            case "sommar":

                $explodeFilename=explode("-", $filename);
                $prefixFN=$explodeFilename[0];
                $siteIdFN1=$explodeFilename[1];//persnr
                $siteIdFN2=$explodeFilename[2];//indice
                $siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
                $siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;

                $internalSiteId=$siteIdFN;
                $templateFileName="Som";
                $yearStudied=substr($prefixFN, strlen($templateFileName), 2);
                $yearFull="20".$yearStudied;

                $expectedFileName=$templateFileName.$yearStudied;

                $checkPeriodInd=$yearFull;

                break;

            case "vinter":

                $explodeFilename=explode("-", $filename);
                $prefixFN=$explodeFilename[0];
                $siteIdFN1=$explodeFilename[1];//persnr
                $siteIdFN2=$explodeFilename[2];//indice
                $siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
                $siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;
                $periodFN=str_replace("P", "", $explodeFilename[4]);

                $internalSiteId=$siteIdFN;
                $templateFileName="Vin";
                $yearStudied=substr($prefixFN, strlen($templateFileName), 2);
                $yearFull="20".$yearStudied;

                $expectedFileName=$templateFileName.$yearStudied;

                $checkPeriodInd=$yearFull."-".$periodFN;

                break;
            case "natt":
                break;
            case "kust":
                break;
        }

        $infoFile=array();
        $infoFile["filename"]=$file;
        $infoFile["internalSiteId"]=$internalSiteId;
        $infoFile["period"]="-";

        if ($protocol=="vinter")
            $infoFile["period"]=$periodFN;


        if ($prefixFN!=$expectedFileName) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$expectedFileName. "'. '".$prefixFN."' instead");
            $infoFile["status"]="NO => wrong filename, does not start with correct template.";
        }
        elseif(isset($array_sites[$siteIdFN])) {   

            if (isset($tabSitesPeriod[$siteIdFN][$checkPeriodInd])) {
                
                if ($server=="PROD") $link=$linkBioActivity["PROD"];
                else $link=$linkBioActivity["DEV"];

                $consoleTxt.=consoleMessage("error", $file. " can't be processed, activity already exists for these specific site (".$siteIdFN.") - year (".$yearFull.") and period (".(isset($periodFN) ? $periodFN : "none") .")");

                    //" '.$periodFN. "' already existing for site '".$siteIdFN."' and year ".$yearFull.' => activityId : '.$tabSitesPeriod[$siteIdFN][$checkPeriodInd]);
                $infoFile["status"]='NO => period <a href="'.$link.$tabSitesPeriod[$siteIdFN][$checkPeriodInd].'" target="_blank" >already exists in MongoDb</a>';

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


$consoleTxt.=consoleMessage("info", count($listFiles)." file(s) found.");


// END 2** FILENAME CHECK

?>