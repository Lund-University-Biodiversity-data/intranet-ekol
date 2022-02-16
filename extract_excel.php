<?php
require("lib/config.php");

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN


$result="";
$file_download="";
$protocol="";
$inputDataObject="data";

$inputYearStart="";
$inputYearEnd="";

if (isset($_POST["execFormExtract"]) && $_POST["execFormExtract"]=="OK") {

	$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	$inputDataObject = (isset($_POST["inputDataObject"]) ? $_POST["inputDataObject"] : "");

	if ($protocol) {
		$output=null;
		$retval=null;

		switch ($inputDataObject) {
			case "persons":

				$rangeYear="_";
				if (isset($_POST["inputYearStart"]) && is_numeric($_POST["inputYearStart"])) {
					$rangeYear.=$_POST["inputYearStart"];
					$inputYearStart=$_POST["inputYearStart"];
				}
				$rangeYear.="_";
				if (isset($_POST["inputYearEnd"]) && is_numeric($_POST["inputYearEnd"])) {
					$rangeYear.=$_POST["inputYearEnd"];
					$inputYearEnd=$_POST["inputYearEnd"];
				}
				$rangeYear.="_";

				$cmd=PATH_PHP." ".PATH_CONVERT_DATA."create_extract_excel_".$inputDataObject.".php ".$protocol." ".$rangeYear;
				//echo $cmd;
				break;
			case "sites":
				$cmd=PATH_PHP." ".PATH_CONVERT_DATA."create_extract_excel_".$inputDataObject.".php ".$protocol;
				break;
			case "data":
				$cmd=PATH_PHP." ".PATH_CONVERT_DATA."create_extract_excel.php ".$protocol;
				break;
		}
		
		exec($cmd, $output, $retval);

		//print_r(str_replace("\n", "<br>", $output));
		//var_dump($output);
		$result="Returned with status $retval and output:\n\n";
		foreach($output as $line) {
			$result.=$line."\n";
		}

		if (strpos($line, ".csv") !== FALSE) {

			$lineExpl=explode(" ", $line);
			$file_download=$lineExpl[count($lineExpl)-1];

			if (file_exists($file_download)) {
				$file_download='<a href="'.str_replace(PATH_CONVERT_DATA."extract/", URL_WEBSITE_EXTRACTMONGO, $file_download).'">DOWNLOADABLE FILE</a>';
			}
			else {

			}
		}
	}
	else {
		$rt="No protocol specified";
		$err=-1;

		echo $rt;
	}
	

}



include ("views/header.html");

include ("views/extract_excel.php");

?>

