<?php

// From X points with latitude/longitude, calculates the centroid (with the average method)
// only if all the points are filled in properly
// input :
// listPoints : array of points [latitude, longitude]
// output : one array of one pont [latitude,longitude]
/* NEVER USED
function calculateCentroid($listPoints) {

	$centroid=array();
	$centroid["latitude"]=0;
	$centroid["longitude"]=0;
	$allpointsok=true;
	$totLat=0;
	$totLong=0;
	if (is_array($listPoints) && count($listPoints)>0) {
		foreach ($listPoints as $point) {
			if (!isset($point["latitude"]) || isset($point["longitude"]) || trim($point["latitude"])=="" || trim($point["longitude"])==""  || $point["latitude"]==0 || $point["longitude"]==0)
				$allpointsok=false;
			else {
				$totLat+=$point["latitude"];
				$totLong+=$point["longitude"];
			}
		}
	}

	if ($allpointsok) {
		$centroid["latitude"]=$totLat/count($listPoints);
		$centroid["longitude"]=$totLong/count($listPoints);

		return $centroid;
	}
	else return false;
}
*/

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