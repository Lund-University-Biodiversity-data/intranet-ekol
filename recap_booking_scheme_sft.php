<?php
$database="SFT";
$dataOrigin="scriptExcel";
require "lib/config.php";

// IMPORTANT CONTROL OF THE WORDPRESS LOGIN
include "lib/check_login.php";
if (!$okConWordPress) exit;
// END CHECK LOGIN


require PATH_SHARED_FUNCTIONS."generic-functions.php";
require PATH_SHARED_FUNCTIONS."mongo-functions.php";

$county=getCountyLanArray();
$province=getProvinceLskArray();

$consoleTxt="";
$server=DEFAULT_SERVER;

$protocol="std";


$arrRecap=array();
$arrPersons=array();
$arrPersonsDetails=array();
$arrSites=array();
$arrSitesInternal=array();
$arrSitesDetails=array();
$arrExcelReceived=array();

$pathInputFiles=PATH_INPUT_EXCEL.$database."/".$protocol."/received/";

$mng = new MongoDB\Driver\Manager($mongoConnection[$server]);

if (isset($_POST["execFormRecapBookingScheme"]) && $_POST["execFormRecapBookingScheme"]=="OK") {

	if (isset($_POST["protocol"])) $protocol=$_POST["protocol"];
	if (isset($_POST["server"])) $server=$_POST["server"];

	include "process/recap_booking_sft_get_sites.php";

	if (count($arrPersons)>0) {
		include "process/recap_booking_sft_get_persons.php";
	}

	include "process/recap_booking_sft_get_lastsurvey.php";

	include "process/recap_booking_sft_get_excelreceived.php";

	ksort($arrRecap);



	$arrHeader=array();
	$arrContent=array();

	switch ($protocol) {
		case "std":
		case "natt":

			$arrHeader[]="#";
			$arrHeader[]="Internal Site Id";
			$arrHeader[]="Ruttnamn";
			$arrHeader[]="Bokad ?";
			$arrHeader[]="Lan";
			$arrHeader[]="Reported OK?";
			$arrHeader[]="Reported BC";
			$arrHeader[]="Status BC";
			$arrHeader[]="Reported Excel";
			$arrHeader[]="Reported Paper";
			$arrHeader[]="Booking comment";
			$arrHeader[]="Name";
			$arrHeader[]="Email";

			/*
			<!--
            <th data-sortable="true" scope="col">#</th>
            <th data-field="internalSiteId" data-sortable="true" scope="col">internalSiteId</th>
            <th data-field="ruttnamn" data-sortable="true" scope="col">Ruttnamn</th>
            <th data-sortable="true" scope="col">Booked?</th>
            <th data-field="lan" data-sortable="true" scope="col">Lan</th>
            <th data-sortable="true" scope="col">Reported BC</th>
            <th data-sortable="true" scope="col">Status</th>
            <th data-sortable="true" scope="col">Reported Excel</th>
            <th data-sortable="true" scope="col">Booking comment</th>
            <th data-sortable="true" scope="col">Name</th>
            <th data-sortable="true" scope="col">Email</th>
            -->
            */
			$idS=0;
			foreach ($arrRecap as $siteId => $dataSite) { 
				$idS++;
				$line=array();

				$line[]=$idS;
				$line[]='<a href="'.$dataSite["urlBioCollect"].'" target="_blank">'.$siteId.'</a>';
				$line[]=$dataSite["commonName"];
				$line[]=$dataSite["booked"];
				$line[]=$dataSite["county"];

				if (
					(is_numeric($dataSite["lastYearSurveyed"]) && $dataSite["lastYearSurveyed"]==date("Y"))
					|| (is_numeric($dataSite["excelReceived"])  && $dataSite["excelReceivedYear"]==date("Y"))
					|| (is_numeric($dataSite["paperSurveySubmitted"])  && $dataSite["paperSurveySubmitted"]==date("Y") )
				) {
					$line[]="Ja";
				}
				else $line[]="Nej";

				$line[]=$dataSite["lastYearSurveyed"];
				$line[]=$dataSite["lastYearSurveyedStatus"];
				$line[]='<div class="tooltipCustom">'.$dataSite["excelReceivedYear"].'
                <span class="tooltiptext">'.$dataSite["excelReceived"].'</span></div>';
				$line[]=$dataSite["paperSurveySubmitted"];
				$line[]=$dataSite["bookingComment"];
				$line[]=($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["name"] : "");
				$line[]=($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["email"] : "");

				$arrContent[]=$line;


				/*
				<?php $idS=0; foreach ($arrRecap as $siteId => $dataSite) { $idS++;?>
		            <tr>
		              <th scope="row"><?= $idS ?></th>
		              <td><a href="<?= $dataSite["urlBioCollect"] ?>" target="_blank"><?= $siteId ?></a></td>
		              <td><?= $dataSite["commonName"] ?></td>
		              <td><?= $dataSite["booked"] ?></td>
		              <td><?= $dataSite["county"] ?></td>
		              <td><?= $dataSite["lastYearSurveyed"] ?></td>
		              <td><?= $dataSite["lastYearSurveyedStatus"] ?></td>
		              <td><div class="tooltipCustom"><?= $dataSite["excelReceivedYear"] ?>
		                <span class="tooltiptext"><?= $dataSite["excelReceived"] ?></span>
		              </div></td>
		              <td><?= $dataSite["bookingComment"] ?></td>
		              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["name"] : "") ?></td>
		              <td><?= ($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["email"] : "") ?></td>
		            </tr>
		          <?php } ?>
		          */

			}

			
			break;
		case "kust":

			$arrHeader[]="#";
			$arrHeader[]="Internal Site Id";
			$arrHeader[]="Ruttnamn";
			$arrHeader[]="Ruttyp";
			$arrHeader[]="Bokad ?";
			$arrHeader[]="Lan";
			$arrHeader[]="Reported BC";
			$arrHeader[]="Status BC";
			$arrHeader[]="Reported summary";
			$arrHeader[]="Booking comment";
			$arrHeader[]="Name";
			$arrHeader[]="Email";

			$idS=0;
			foreach ($arrRecap as $siteId => $dataSite) { 
				$idS++;
				$line=array();

				$line[]=$idS;
				$line[]='<a href="'.$dataSite["urlBioCollect"].'" target="_blank">'.$siteId.'</a>';
				$line[]=$dataSite["commonName"];
				$line[]=$dataSite["routetype"];
				$line[]=$dataSite["booked"];
				$line[]=$dataSite["county"];
				$line[]=$dataSite["lastYearSurveyed"];
				$line[]=$dataSite["lastYearSurveyedStatus"];
				$line[]=$dataSite["summarySurveySubmitted"];
				$line[]=$dataSite["bookingComment"];
				$line[]=($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["name"] : "");
				$line[]=($dataSite["booked"]=="yes" ? $arrPersonsDetails[$dataSite["bookedBy"]]["email"] : "");

				$arrContent[]=$line;

			}
	}

}

include ("views/header.html");

include ("views/recap_booking_scheme_sft.php");


?>
