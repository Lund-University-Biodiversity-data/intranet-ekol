<?php

define("PATH_PHP", "/usr/bin/php");

define ("URL_WEBSITE", "http://localhost/");
define ("URL_WEBSITE_EXTRACTMONGO", URL_WEBSITE."extractmongo/");
define ("URL_WEBSITE_SURVEYS", URL_WEBSITE."intranet/excel-surveys/");
define ("URL_WEBSITE_COORDINATES", URL_WEBSITE."intranet/excel-coordinates/");
define ("URL_WEBSITE_CSV", URL_WEBSITE."url/csv/");

define ("URL_LOGOUT", "wp-login.php?action=logout");

define ("PATH_WORDPRESS_LOGIN", "/usr/share/wordpress/wp-load.php");

define("EMAIL_PROBLEM", "XXXXX@biol.lu.se");

define("PATH_SHARED_FUNCTIONS", "/XXXXX/repos/shared-functions");

define("PATH_WEBSERVER_ROOT", "/home/XXXXX/");
define("PATH_CONVERT_DATA", PATH_WEBSERVER_ROOT."intranet-ekol/extract/");
define("PATH_OUTPUT_JSON", PATH_WEBSERVER_ROOT."intranet-ekol/json/");
define("PATH_OUTPUT_JSON", PATH_WEBSERVER_ROOT."intranet-ekol/csv/");

define ("PATH_INPUT_EXCEL_SURVEYS", PATH_WEBSERVER_ROOT."intranet-ekol/excel-surveys/");
define ("PATH_INPUT_EXCEL_COORDINATES", PATH_WEBSERVER_ROOT."intranet-ekol/excel-coordinates/");

define("PATH_SAVE_BOOKINGS", PATH_WEBSERVER_ROOT."intranet-ekol/bookings-save/");

define("DEFAULT_SERVER", "PROD");
define("ENVIRONMENT", "PROD");

define("URL_LISTS_WS", "https://lists.biodiversitydata.se/ws/");
define("URL_LISTS_ITEMS", URL_LISTS_WS."speciesListItems/");
define("URL_LISTS_INCLUDE_KBV", "?includeKVP=true&max=1000");

define("FILENAME_SEPARATOR", "|");

define("URL_BIOCOLLECT_DEV", "http://devt.biodiversitydata.se:8087/");
define("URL_BIOCOLLECT_PROD", "https://biocollect.biodiversitydata.se/");
define("URL_BIOCOLLECT_ENVIRONMENT", "sft/");
//$postgres DATABASE TO STORE 

$linkBioActivity["TEST"]=URL_BIOCOLLECT_DEV.URL_BIOCOLLECT_ENVIRONMENT."/bioActivity/index/";
$linkBioActivity["DEV"]=URL_BIOCOLLECT_DEV.URL_BIOCOLLECT_ENVIRONMENT."/bioActivity/index/";
$linkBioActivity["PROD"]=URL_BIOCOLLECT_PROD.URL_BIOCOLLECT_ENVIRONMENT."/bioActivity/index/";

$linkBioSite["TEST"]=URL_BIOCOLLECT_DEV.URL_BIOCOLLECT_ENVIRONMENT."/site/editSystematic/";
$linkBioSite["DEV"]=URL_BIOCOLLECT_DEV.URL_BIOCOLLECT_ENVIRONMENT."/site/editSystematic/";
$linkBioSite["PROD"]=URL_BIOCOLLECT_PROD.URL_BIOCOLLECT_ENVIRONMENT."/site/editSystematic/";


$list_id["birds"]="dr627";
$list_id["mammals"]="dr159";
$list_id["amphibians"]="dr160";
$list_id["owls"]="dr630";

$database="SFT";

define("MONGO_DBNAME", "ecodata");

$mongoConnection["TEST"]="mongodb://localhost";
$mongoConnection["DEV"]="mongodb://canmove-dev.ekol.lu.se";
$IP_PROD="XX.XX.XX.XX";
$mongoConnection["PROD"]="mongodb://".$IP_PROD;

$commonFields["userId"]=5;
$commonFields["status"]="unverified";
$commonFields["recordedBy"]="Mathieu Blanchet";
$commonFields["rightsHolder"]="Lund University";
$commonFields["institutionID"]=$commonFields["rightsHolder"];
$commonFields["institutionCode"]=$commonFields["rightsHolder"];
$commonFields["basisOfRecord"]="HumanObservation";
$commonFields["multimedia"]="[ ]";
$commonFields["licence"]="https://creativecommons.org/publicdomain/zero/1.0/";

switch ($database) {
	case "SFT":

		$DB["host"]="localhost";
		$DB["username"]="postgres";
		$DB["database"]="sft_migration";
		$DB["password"]="";

		// STANDARDRUTTERNA
		$commonFields["std"]["projectId"]="89383d0f-9735-4fe7-8eb4-8b2e9e9b7b5c";
		$commonFields["std"]["projectActivityId"]="a14cf615-a26b-48a7-87fd-00360f3d03d6";
		$commonFields["std"]["datasetId"]=$commonFields["std"]["projectActivityId"];
		$commonFields["std"]["datasetName"]="Standardrutt"; // survey name
		//$commonFields["datasetName"]="Second STD survey";
		$commonFields["std"]["type"]="Standardrutt"; // activity form
		$commonFields["std"]["name"]="Standardrutt"; // form section

		// NATTRUTTERNA
		$commonFields["natt"]["projectId"]="d0b2f329-c394-464b-b5ab-e1e205585a7c";
		$commonFields["natt"]["projectActivityId"]="eb7e3708-f1ff-4114-b1c3-84ed93ec7a8d";
		$commonFields["natt"]["datasetId"]=$commonFields["natt"]["projectActivityId"];
		$commonFields["natt"]["datasetName"]="Nattrutt";
		$commonFields["natt"]["type"]="Nattrutt";
		$commonFields["natt"]["name"]="Nattrutt";

		// PUNKTRUTTERNA
		$commonFields["punkt"]["projectId"]="b7eee643-d5fe-465e-af38-36b217440bd2";

		// VINTERRUTTERNA
		$commonFields["vinter"]["projectId"]=$commonFields["punkt"]["projectId"];
		$commonFields["vinter"]["projectActivityId"]="ccace44f-c37a-44de-a586-7880128046d3";
		$commonFields["vinter"]["datasetId"]=$commonFields["vinter"]["projectActivityId"];
		$commonFields["vinter"]["datasetName"]="Vinterrutt";
		$commonFields["vinter"]["type"]="Vinterrutt";
		$commonFields["vinter"]["name"]="Vinterrutt";

		// SOMMARRUTTERNA
		$commonFields["sommar"]["projectId"]=$commonFields["punkt"]["projectId"];
		$commonFields["sommar"]["projectActivityId"]="f4baa9fa-cfd4-4bc7-85bf-9a2c0f482504";
		$commonFields["sommar"]["datasetId"]=$commonFields["sommar"]["projectActivityId"];
		$commonFields["sommar"]["datasetName"]="Sommarrutt";
		$commonFields["sommar"]["type"]="Sommarrutt";
		$commonFields["sommar"]["name"]="Sommarrutt";

		// KUSTFÅGLAR
		$commonFields["kust"]["projectId"]="49f55dc1-a63a-4ebf-962b-4d486db0ab16";
		$commonFields["kust"]["projectActivityId"]="d47b0d4e-6353-4bb8-94cb-400a5f07f21d";
		$commonFields["kust"]["datasetId"]=$commonFields["kust"]["projectActivityId"];
		$commonFields["kust"]["datasetName"]="Kustfågelrutor";
		$commonFields["kust"]["type"]="Kustfagelrutor"; // NO å
		$commonFields["kust"]["name"]="Kustfagelrutor"; // NO å

		// SJÖFÅGLAR januari/september
		$commonFields["iwc"]["projectId"]="50b1cb29-cf33-4d43-a805-b07ae4de1750";
		$commonFields["iwc"]["projectActivityId"]="60c68058-425a-483f-a934-9675c28fb3c5";
		$commonFields["iwc"]["datasetId"]=$commonFields["iwc"]["projectActivityId"];
		$commonFields["iwc"]["datasetName"]="Sjöfåglar, januari/september";
		$commonFields["iwc"]["type"]="Sjofagel"; // NO ö
		$commonFields["iwc"]["name"]="Sjofagel"; // NO ö
		
		break;

	case "SEBMS":
		break;
}
