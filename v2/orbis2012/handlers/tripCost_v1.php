<?php
header('Content-Type: text/html; charset=utf-8');
include("conn_webapp.php");

$month = $_GET['m'] ;
$source = $_GET['s'];
$target = $_GET['t'];
$vehicle = $_GET['v'] ;
$pathtype = $_GET['p'];
//$coastal = $_GET['c'];
$modelist = $_GET['ml'];

if (!$vehicle) {
$vehicle = "foot";
$pathtype = 1;
$coastal = "false";
}

$modeList = $modeList."transferctransferftransferotransferr";

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
when type = 'road' then 'SaddleBrown'
when type = 'upstream' then 'LightSlateGray'
when type = 'downstream' then 'LightSlateGray'
when type = 'fastup' then 'LightSlateGray'
when type = 'fastdown' then 'LightSlateGray'
when type = 'coastal' then 'LightSeaGreen'
when type = 'overseas' then 'DarkSeaGreen'
when type = 'slowcoast' then 'LightSeaGreen'
when type = 'slowover' then 'DarkSeaGreen'
when type = 'ferry' then 'LightSeaGreen'
when type = 'dayfast' then 'LightSeaGreen'
when type = 'dayslow' then 'LightSeaGreen'
else 'black'
end) as color,
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
echo "[";
	$distancetally = 0;
	$durationtally = 0;	
	$currentduration = $maxduration / 15;
foreach($sorted as $s){
	if ($s[0] == $source) {
	    echo "{";
		echo '"name": "'.$s[2].'",';
		echo '"scale": '.$s[5] / 10 .',';
		echo '"length": 0.0,';
		echo '"distance": 0.0,';
		echo '"start": 0.0,';
		echo '"duration": 0.0,';
		echo '"color": "SaddleBrown",';
		echo '"type": "road"';
		echo "},";
	}
		$startpoint = $distancetally;
		$distancetally = $distancetally + $s[4];
		$durationtally = $durationtally + $s[7];
		if (round($durationtally,0) == round($currentduration)) {
			$displayduration = 0;
		}
		else if ((($durationtally - ($maxduration / 15))) < $currentduration) {
			$displayduration = 0;
		}
		else {
		$currentduration = $durationtally;
		$displayduration = $durationtally;
		}

		echo "{";
		echo '"name": "'.$s[3].'",';
		echo '"scale": '.$s[6] / 10 .',';
		echo '"length": '.$s[4]/1000 .',';
		echo '"distance": '.$distancetally / $maxdistance . ',';
		echo '"start": '.$startpoint / $maxdistance . ',';
		echo '"duration": '.round($displayduration, 0). ',';
		echo '"color": "'.$s[8].'",';
		echo '"type": "'.$s[9].'"';		
		echo "}";
	if($s[1] <> $target) {
		echo ",";
		}
}
echo "]";
pg_close($link);
?>