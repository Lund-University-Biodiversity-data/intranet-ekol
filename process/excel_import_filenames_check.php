<?php
// 2** FILENAME CHECK


// date now
$micro_date = microtime();
$date_array = explode(" ",$micro_date);
$micsec=number_format($date_array[0]*1000, 0, ".", "");
$micsec=str_pad($micsec,3,"0", STR_PAD_LEFT);
if ($micsec==1000) $micsec=999;
$date_now_tz = date("Y-m-d",$date_array[1])."T".date("H:i:s",$date_array[1]).".".$micsec."Z";
//echo "Date: $date_now_tz\n";

$path_excel=PATH_INPUT_EXCEL.$database."/".$protocol."/";

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
                $templateFileName="Vin20";
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


        $templateFileName="Vin20";

        if ($prefixFN!=$templateFileName) {
            $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$templateFileName. "'. '".$prefixFN."' instead");
        }
        elseif(isset($tabSitesPeriod[$siteIdFN])) {
            if (in_array($periodFN, $tabSitesPeriod[$siteIdFN])) {
                $consoleTxt.=consoleMessage("error", $file. " can't be processed, period '".$periodFN. "' already existing for site '".$siteIdFN."'");
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