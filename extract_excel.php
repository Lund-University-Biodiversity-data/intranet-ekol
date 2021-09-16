<?php
require("lib/config.php");

$result="";
$file_download="";
$protocol="std";
$inputDataObject="data";

if (isset($_POST["execFormExtract"]) && $_POST["execFormExtract"]=="OK") {

	$protocol = (isset($_POST["inputProtocol"]) ? $_POST["inputProtocol"] : "");
	$inputDataObject = (isset($_POST["inputDataObject"]) ? $_POST["inputDataObject"] : "");

	if ($protocol) {
		$output=null;
		$retval=null;

		switch ($inputDataObject) {
			case "persons":
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
				$file_download='<a href="'.str_replace(PATH_CONVERT_DATA."extract/", URL_WEBSITE, $file_download).'">DOWNLOADABLE FILE</a>';
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

