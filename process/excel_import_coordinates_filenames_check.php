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
            case "natt":
                $explodeFilename=explode("-", $filename);

                $prefixFN=$explodeFilename[0];
                $kartaTx=(isset($explodeFilename[1]) ? $explodeFilename[1] :"");

                $siteIdFN=$kartaTx;

                // no prefix expected
                $expectedFileName="koord";

                break;

            case "punkt":

                $explodeFilename=explode("-", $filename);
                $prefixFN=$explodeFilename[0];
                $siteIdFN1=$explodeFilename[1];//persnr
                $siteIdFN2=$explodeFilename[2];//indice
                $siteIdFN3=str_replace("#", "", $explodeFilename[3]);//rnr
                $siteIdFN=$siteIdFN1."-".$siteIdFN2."-".$siteIdFN3;

                $templateFileName="Som";
                $yearStudied=substr($prefixFN, strlen($templateFileName), 2);
                $yearFull="20".$yearStudied;

                $expectedFileName=$templateFileName.$yearStudied;

                $checkPeriodInd=$yearFull;

                break;
        }

        $infoFile=array();
        $infoFile["filename"]=$file;
        $infoFile["internalSiteId"]=$siteIdFN;
        $infoFile["period"]="-";

        if ($protocol=="vinter" || $protocol=="natt")
            $infoFile["period"]=$periodFN;

        $okTempFile=true;
        if($protocol=="punkt" && count($explodeFilename)!=2) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, filename with wrong format. Must be 'KARTA YEAR'");
            $infoFile["status"]="NO => filename with wrong format. Must be 'KARTA YEAR'";
            $okTempFile=false;
        }
        elseif($protocol=="natt" && (count($explodeFilename)!=2)) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, filename with wrong format. Must be 'koord-KARTATX'");
            $infoFile["status"]="NO => filename with wrong format. Must be 'koord-KARTATX'";
            $okTempFile=false;
        }

        if ($okTempFile) {
            if ($prefixFN!=$expectedFileName) {
                $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$expectedFileName. "'. '".$prefixFN."' instead");
                $infoFile["status"]="NO => wrong filename, does not start with correct template.";
            }
            elseif(isset($array_sites[$siteIdFN])) {   
    
                if (count($array_sites[$siteIdFN]["transectParts"])!=0) {
                    
                    if ($server=="PROD") $link=$linkBioSite["PROD"];
                    else $link=$linkBioSite["DEV"];
    
                    $consoleTxt.=consoleMessage("error", $file. " can't be processed, Transect data already exists for site ".$siteIdFN."");
    
                        //" '.$periodFN. "' already existing for site '".$siteIdFN."' and year ".$yearFull.' => activityId : '.$tabSitesPeriod[$siteIdFN][$checkPeriodInd]);
                    $infoFile["status"]='NO => <a href="'.$link.$array_sites[$siteIdFN]["locationID"].'" target="_blank" >transectParts already exists in MongoDb</a>';
    
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
    
        }
        
        $listFiles[]=$infoFile;

    }


}


$consoleTxt.=consoleMessage("info", count($listFiles)." file(s) found.");


// END 2** FILENAME CHECK

?>