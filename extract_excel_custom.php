<?php
require("lib/config.php");

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN

require PATH_SHARED_FUNCTIONS."generic-functions.php";


$file_download="";
$protocol="";
$queryExtract="data";

$inputYearStart="";
$inputYearEnd="";


if (isset($_POST["execFormExtract"]) && $_POST["execFormExtract"]=="OK") {

	$timeStart=time();

	$queryExtract = (isset($_POST["queryExtract"]) ? $_POST["queryExtract"] : "");

	if ($queryExtract) {

		$rtFile="";

		switch ($queryExtract) {
			case "stdRecpaComments":

				$consoleTxt.=consoleMessage("info", "2) Get list sites/year");

				include "process/extract_excel_std_recap_comments_year.php";

				if (count($tabSitesPeriod)>0){
					$consoleTxt.=consoleMessage("info", "2) Create csv");
					
					include "process/extract_excel_std_recap_comments_year_csv.php";

					$consoleTxt.=consoleMessage("info", "file created : ".$file_download);
				}
				break;
		}

		if (file_exists($file_download)) {
			$file_download='<a href="'.str_replace(PATH_CONVERT_DATA."extract/", URL_WEBSITE_EXTRACTMONGO, $file_download).'">DOWNLOADABLE FILE</a>';
		}
		else {

		}
	}
	else {
		$rt="No query specified";
		$err=-1;

		echo $rt;
	}
	
	$timeEnd=time();

	$processTime=$timeEnd-$timeStart;
	$consoleTxt.=consoleMessage("info", "Processed in ".$processTime." second(s)");

}



include ("views/header.html");

include ("views/extract_excel_custom.php");

?>

