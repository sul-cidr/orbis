<?php

header('Content-Type: text/html; charset=utf-8');
$source = $_GET['s'];
$target = $_GET['t'];

/*
$pathtype = $_GET['p'];
$coastal = $_GET['c'];
$modelist = $_GET['ml'];
*/

/* $link = pg_connect("host=geodata.stanford.edu dbname=orbis user=webapp password=sl1ppy"); 
$link = pg_connect("host=geoserver.stanford.edu dbname=orbis user=webapp password=sl1ppy");*/
$link = pg_connect("host=orbis-prod.stanford.edu dbname=orbis user=webapp password=sl1ppy");

$sql="
SELECT DISTINCT ON (pleiades1.name)

'{\"nodeid\":'||pleiades1.gid||
',\"link\": \"'||
pleiades1.path||'\",\"label\": \"'||
pleiades1.name||'\"}'

FROM

pleiades1,
pl_to_orbis,
o_roads

WHERE
pl_to_orbis.pl_id = pleiades1.gid

AND

o_roads.gid = pl_to_orbis.road_id

AND
(
(
o_roads.o_source = ".$source."

AND

o_roads.o_target = ".$target."
)

OR

(
o_roads.o_target = ".$source."

AND

o_roads.o_source = ".$target."
)
)

LIMIT 10
"
;

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
echo '
[
        ';
foreach($tough as $s){
	  	$t++;
		echo $s[0];
		if(count($tough) <> $t) {
		echo ",";
		}

}

echo "]
";

pg_close($link);
?>