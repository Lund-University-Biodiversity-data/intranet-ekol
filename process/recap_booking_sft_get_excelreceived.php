<?php

$consoleTxt.=consoleMessage("info", "4) Get existing excel files ".$protocol);


if (file_exists($pathInputFiles)) {

	$filesSurveysReceived = scandir($pathInputFiles);
	foreach($filesSurveysReceived as $file) {

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
	            	$explodeFilename=explode("-", $filename);

	            	if (!isset($explodeFilename[1])) {
	            		$consoleTxt.=consoleMessage("error", "Wrong filename template for file ".$file.". Must be karta-YEAR.xls(x)");
	            	}
	            	else {
		            	$kartaFilename=$explodeFilename[0];
		            	$yearFull=$explodeFilename[1];
		            	$internalSiteId=$kartaFilename;
	            	}

	            	$prefixFN="";
	            	$expectedFileName="";

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
	                
	                break;
	            case "natt":
	                break;
	            case "kust":
	                break;
	        }

	        
	        if ($prefixFN!=$expectedFileName) {
	            $consoleTxt.=consoleMessage("error", $file. " can't be processed, wrong prefix. Must start with '".$expectedFileName. "'. '".$prefixFN."' instead");
	        }
	        else {
	        	if (isset($internalSiteId)) {
	        		$arrExcelReceived[$internalSiteId][]=$file;

	        		if (isset($arrRecap[$internalSiteId])) {
	        			$arrRecap[$internalSiteId]["excelReceived"].=$file."<br>";
	        			$arrRecap[$internalSiteId]["excelReceivedYear"].=$yearFull."<br>";
	        		}
	        	}
	        }

	        
	    }
	}
	$consoleTxt.=consoleMessage("info", count($arrExcelReceived)." site(s) with excel file(s) found.");
}
else {
	$consoleTxt.=consoleMessage("error", "Impossible to read Excel folder ".$pathInputFiles);
}
?>
