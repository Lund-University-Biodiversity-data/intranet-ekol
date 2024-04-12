<?php

function getNpPtsForProtocol ($protocol) {
	

	switch($protocol) {
		case "std":
			$nbPts=8;
			break;
		case "punkt":
		case "sommar":
		case "vinter":
			$nbPts=20;
			break;
		case "natt":
			$nbPts=20;
			break;
		case "kust":
			$nbPts=1;
			break;
		default:
			$nbPts=0;
			break;
	}

	return $nbPts;
}



?>