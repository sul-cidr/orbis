<?php

header('Content-Type: text/html; charset=utf-8');
/*
$source = $_GET['s'];
$month = $_GET['m'];
$vehicle = $_GET['v'] ;
$target = $_GET['t'];
$pathtype = $_GET['p'];
$coastal = $_GET['c'];
$modelist = $_GET['ml'];
*/


$link = pg_connect("host=geodata.stanford.edu dbname=orbis user=webapp password=sl1ppy");

$sql="
SELECT

'{\"type\": \"Feature\", \"geometry\": '||
ST_AsGeoJson(o_sites.the_geom,2)
||', \"properties\": {\"nodeid\":'||objectid||
',\"rank\":'||
rank||',\"label\": \"'||
o_sites.label||'\",\"modes\": [\"Start\"]
,\"routeTime\": [{\"Start\": 0.0}]}}'

FROM

o_sites

WHERE

o_sites.rank > 85

GROUP BY
o_sites.the_geom,
o_sites.rank,
o_sites.objectid,
o_sites.label
"
;

//echo('<p>The query sent: '.$sql.'</p><p>The link: '.$link.'</p><h3>The official fubar PHP report is: </h3>');

if (!$link) {
    echo "error, didn't make the pg_connect()";
} else {
	$result = pg_query($link, $sql);
	if (!$result) {
	  echo "error, no result!<br>";
      print pg_last_error($link);	  
	  exit;
	}
}
// echo $result;	

	$tough = array();
	$r = 0;
	$lastobjectid = 0;
	while ($row = pg_fetch_row($result)) {
	  	array_push($tough, $row);
	}
	$t = 0;
	$id_hash = array();

	$b = 0;
echo '{
    "type": "FeatureCollection",
    "features": [
        ';
foreach($tough as $s){
	  	$t++;
		echo $s[0];
		if(count($tough) <> $t) {
		echo ",";
		}

}

echo "]
}";

pg_close($link);
?>