<?php

header('Content-Type: text/html; charset=utf-8');
$source = $_GET['s'];
$month = $_GET['m'];
$vehicle = $_GET['v'] ;
/*
$target = $_GET['t'];
$pathtype = $_GET['p'];
$coastal = $_GET['c'];
$modelist = $_GET['ml'];
*/


include '../conn.php';
$link = pg_connect($connection_string);

$sql="
SELECT

'{\"type\": \"Feature\", \"geometry\": '||
ST_AsGeoJson(o_sites.the_geom)
||', \"properties\": {\"nodeid\":'||objectid||
',\"rank\":'||
rank||',\"label\": \"'||
o_sites.label||'\",\"modes\": ['||string_agg('\"'||o_routing.type||'\"', ',')||']
,\"routeTime\": ['||string_agg('{\"'||o_routing.type||'\": '||o_speed('".$vehicle."',o_routing.type,o_routing.the_geom,o_routing.cost)::numeric(5,2)||'}', ',')||']}}'

FROM

o_sites

LEFT JOIN o_routing ON o_routing.target = o_sites.objectid

WHERE
o_routing.source = ".$source."

AND

(o_sites.displayed = 1

OR

o_sites.label = 'x')

AND

o_routing.type IN
('downstream','road','upstream','slowcoast','ferry','slowover')

AND

o_routing.month IN (0,".$month.")

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
