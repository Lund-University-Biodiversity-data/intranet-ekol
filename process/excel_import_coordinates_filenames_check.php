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
                $explodeFilename=explode("_", $filename);

                $prefixFN=$explodeFilename[0];
                $kartaTx=(isset($explodeFilename[1]) ? $explodeFilename[1] :"");

                $siteIdFN=$kartaTx;

                $expectedPrefix="koord";

                break;

            case "punkt":

                $explodeFilename=explode("_", $filename);

                $prefixFN=$explodeFilename[0];

                $siteIdFN=(isset($explodeFilename[1]) ? str_replace("#", "", $explodeFilename[1]) :"");

                $expectedPrefix="koord";

                break;
        }

        $infoFile=array();
        $infoFile["filename"]=$file;
        $infoFile["internalSiteId"]=$siteIdFN;
        $infoFile["period"]="-";

        $okTempFile=true;
        if($protocol=="punkt" && count($explodeFilename)!=2) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, filename with wrong format. Must be 'koord_internalSiteId'");
            $infoFile["status"]="NO => filename with wrong format. Must be 'koord_internalSiteId'";
            $okTempFile=false;
        }
        elseif($protocol=="natt" && (count($explodeFilename)!=2)) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, filename with wrong format. Must be 'koord_KARTATX'");
            $infoFile["status"]="NO => filename with wrong format. Must be 'koord_KARTATX'";
            $okTempFile=false;
        }

        if ($okTempFile) {
            if ($prefixFN!=$expectedPrefix) {
                $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$expectedPrefix. "'. '".$prefixFN."' instead");
                $infoFile["status"]="NO => wrong filename, does not start with correct template.";
            }
            elseif(isset($array_sites[$siteIdFN])) {   
    
                if (count($array_sites[$siteIdFN]["transectParts"])!=0) {
                    
                    if ($server=="PROD") $link=$linkBioSite["PROD"];
                    else $link=$linkBioSite["DEV"];
    
                    $consoleTxt.=consoleMessage("error", $file. " can't be processed, Transect data already exists for site ".$siteIdFN."");
                        $infoFile["status"]='NO => <a href="'.$link.$array_sites[$siteIdFN]["locationID"].'" target="_blank" >transectParts already exists in MongoDb</a>';
    
                }
                else {
                    $consoleTxt.=consoleMessage("info", $file. " OK to be processed for site '".$siteIdFN."'");
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