<?php

header('Content-Type: text/html; charset=utf-8');
$source = $_GET['s'];
$month = $_GET['m'];
$vehicle = $_GET['v'];
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
ST_AsGeoJson(ST_simplify(the_geom,.005))
||', \"properties\": {\"source\":'||source||
',\"target\":'||
target||
',\"duration\": '||o_speed('".$vehicle."',type,the_geom,cost)::numeric(8,2)||
',\"length\": '||(st_length(Geography(ST_Transform(o_routing.the_geom,4326))) / 1000)::numeric(8,0)||
',\"type\": \"'||
type||'\"}}'

FROM

o_routing

WHERE

month IN (0,".$month.")

AND

(source = ".$source.")

AND

type IN
('downstream','road','upstream','slowcoast','ferry','slowover')"
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
