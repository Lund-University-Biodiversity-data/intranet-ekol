<?php
// format: caracters spearated by '-'. Example: xxxx-xxxx-xxxx-xxxxxxxxx
// generates an unique ID with hexa digits.
function generate_uniqId_format ($format) {
    $format_arr=explode("-", $format);
    $uniqid_str=array();
    foreach ($format_arr as $part) {
        // 13 digits random uniqid
        //$uniqid=uniqid();
        $uniqid="";
        for ($i=0;$i<strlen($part);$i++) {
            $uniqid.=dechex(rand(0,15));
        }

        $uniqid_str[]=substr($uniqid, strlen($uniqid)-strlen($part), strlen($part));

        //echo "uniqueID:".$uniqid."|part:".substr($uniqid, strlen($uniqid)-strlen($part), strlen($part))."\n";
    }

    return implode("-", $uniqid_str);
}



function consoleMessage($type, $message) {
	$msg="[".date("Y-m-d H:i:s")."] ".strtoupper($type)." : ".$message."\n";

	return $msg;
}

function getArraySitesFromMongo ($protocol, $projectId) {

    global $mongoConnection;
    
    $array_sites=array();
    
    /**************************** connection to mongoDB   ***/
    $mng = new MongoDB\Driver\Manager($mongoConnection["url"]); // Driver Object created

    if ($mng) echo consoleMessage("info", "Connection to mongoDb ok");

    //$filter = [];
    $filter = ['projects' => $projectId];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options); 

    //db.site.find({"projects":"dab767a5-929e-4733-b8eb-c9113194201f"}, {"projects":1, "name":1}).pretty()
    // 
    $rows = $mng->executeQuery("ecodata.site", $query);

    foreach ($rows as $row){
        
        if ($protocol=="natt") {
            if (isset($row->kartaTx))
                $indexSite=$row->kartaTx;
            else {
                echo consoleMessage("info", "No kartaTx for site ".$row->name);
                $indexSite=$row->name;
            } 
        }
        elseif ($protocol=="kust") {
            if (isset($row->name))
                $indexSite=$row->name;
            else {
                echo consoleMessage("info", "No name for site ".$row->name);
                $indexSite=$row->name;
            } 
        }
        elseif ($protocol=="punkt" || $protocol=="vinter" || $protocol=="sommar") {
            if (isset($row->adminProperties->internalSiteId))
                $indexSite=$row->adminProperties->internalSiteId;
            else {
                echo consoleMessage("info", "No internalSiteId for site ".$row->name);
                $indexSite=$row->name;
            } 
        }
        else {
            if (isset($row->karta))
                $indexSite=$row->karta;
            else {
                echo consoleMessage("error", "No karta for site ".$row->name);
                $indexSite=$row->name;
            } 

        }   
        $array_sites[$indexSite]=array();

        $array_sites[$indexSite]["locationID"]=$row->siteId;
        $array_sites[$indexSite]["locationName"]=$indexSite;
        $array_sites[$indexSite]["decimalLatitude"]=$row->extent->geometry->decimalLatitude;
        $array_sites[$indexSite]["decimalLongitude"]=$row->extent->geometry->decimalLongitude;

        //$array_sites_req[]="'".$indexSite."'";
    }

    /**************************** connection to mongoDB   ***/

    return $array_sites;
}

// convert a string with HHMM to
// HH:MM AM/PM (IF mode AMPM)
// examples: 
// 2342 => 11:42 PM
// 1114 => 11:14 AM
// 754 => 07:54 AM
// 15 => 00:15 AM, 
// ELSE (mode 24H) HH:MM

function convertTime($time, $mode="AMPM") {

    if ($mode=="AMPM") {
        if ($time>1200){
            $time-=1200;
            $time=str_pad($time, 4, '0', STR_PAD_LEFT);
            $time=substr($time, 0, 2).":".substr($time, 2, 2)." PM";
        }
        else {
            $time=str_pad($time, 4, '0', STR_PAD_LEFT);
            $time=substr($time, 0, 2).":".substr($time, 2, 2)." AM";
        }
    }
    else {
        $time=str_pad($time, 4, '0', STR_PAD_LEFT);
        $time=substr($time, 0, 2).":".substr($time, 2, 2);
    }
    
    return $time;
}

?>