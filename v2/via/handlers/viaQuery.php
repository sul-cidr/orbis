<?php
echo("hello!");
header('Content-Type: text/html; charset=utf-8');
include("../../../conn/conn_webapp.php");
$source = $_GET['s'];
/*
$target = $_GET['t'];
$vehicle = $_GET['v'] ;
$pathtype = $_GET['p'];
$coastal = $_GET['c'];
$modelist = $_GET['ml'];
*/

if (!$vehicle) {
$vehicle = "foot";
$pathtype = 1;
$coastal = "false";
}

$link = pg_connect($connectString_orbis);

$sql="
SELECT

t.label as linkname,
t.objectid as linkid,
o_routing.type as linktype,
(st_length(Geography(ST_Transform(o_routing.the_geom,4326))) / 1000)::Numeric(8,0) as dist

FROM

o_routing,
o_sites as t

WHERE

t.objectid = o_routing.target

AND

o_routing.source = ".$source." AND month IN (0,1)

AND

type NOT IN ('fastup','fastdown')
";

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
// echo $result;	

	$sorted = array();
	$r = 0;
	while ($row = pg_fetch_row($result)) {
	  	array_push($trimmed, $row);
	  $r++;
	}

echo '{
    "sites": [
        {
            "name": "PartII",
            "scale": "Big",
            "xcoord": 10.9089330404277,
            "ycoord": 47.9091467290384,
            "netdist": 13.09
        }
    ],';
$t = 0;
foreach($sorted as $s){
		$t++;
		echo "{";
		echo '"linkname": "'.$s[0].'",';
		echo '"linkid": '.$s[1] .',';
		echo '"linktype": "'.$s[2].'",';
		echo '"linkdist": '.$s[3].'';
		echo "}";	
	if($t <> $r) {
		echo ",";
		}
}
echo "]";
pg_close($link);
?>