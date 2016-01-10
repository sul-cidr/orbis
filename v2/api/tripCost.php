<?php
/* http://orbis-dev.stanford.edu/orbisapi/tripCost.php?s=50012&t=50112&m=6&v=foot&p=1&ml=ferry,road,fastup,fastdown,overseas,coastal*/

header('Content-Type: text/html; charset=utf-8');
include("../../../conn/conn_webapp.php");
include("util.php");

$month = $_GET['m'] ;
$source = $_GET['s'];
$target = $_GET['t'];
$vehicle = $_GET['v'] ;
$pathtype = $_GET['p'];
$modelist = $_GET['ml'];

if (!$vehicle) {
$vehicle = "foot";
$pathtype = 1;
$coastal = "false";
}

/*$month = 1;
$source = 50129;
$target = 50128;
*/
$link = pg_connect($connectString_orbis);

$sql_base="
SELECT 
source_id,
target_id,
source,
target,
length as len,
source_rank,
target_rank,
duration,
(  case
when type = 'road' then 'road'
when type = 'upstream' then 'river'
when type = 'downstream' then 'river'
when type = 'fastup' then 'river'
when type = 'fastdown' then 'river'
when type = 'coastal' then 'coastal'
when type = 'overseas' then 'open sea'
when type = 'slowcoast' then 'coastal'
when type = 'slowover' then 'open sea'
when type = 'ferry' then 'coastal'
when type = 'dayfast' then 'coastal'
when type = 'dayslow' then 'coastal'
else 'unknown'
end) as type
";

if ($pathtype == 0){
$sql = $sql_base.
"
FROM o_routing_cheapest(".$source.",".$target.",".$month.",'".$vehicle."','".$modelist."')
";
}
else if ($pathtype == 1){
$sql = $sql_base.
"
FROM o_routing_fastest(".$source.",".$target.",".$month.",'".$vehicle."','".$modelist."')
";
}
else {
$sql = $sql_base.
"
FROM o_routing_shortest(".$source.",".$target.",".$month.",'".$vehicle."','".$modelist."')
";
}
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

	$trimmed = array();
	$r = 0;
	//$arr = pg_fetch_object($result);
	while ($row = pg_fetch_row($result)) {
	  	array_push($trimmed, $row);
	  $r++;
	}
	$sorted = array();
	$currentpoint = $source;
	$finalpoint = $target;
	$maxdistance = 0;
	$maxduration = 0;
	while (count($sorted) < count($trimmed)) {
		foreach ($trimmed as $t){
			if ($t[0] == $currentpoint){
			array_push($sorted, $t);
			$currentpoint = $t[1];
		    $maxdistance = $maxdistance + $t[4];			    
		    $maxduration = $maxduration + $t[7];			    
		}
	}
}
}
echo indent(json_encode($sorted));
//echo $result;
pg_close($link);
?>