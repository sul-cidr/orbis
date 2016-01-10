<?php
/* http://orbis-dev.stanford.edu/orbisapi/tripCost.php?s=50012&t=50112&m=6&v=foot&p=1&ml=ferry,road,fastup,fastdown,overseas,coastal*/
function checkIds ($source, $target, $month, $priority) {
	$opj = json_decode(file_get_contents("opm402.json"),true);
	global $og_route;
	$og_route = $source.' to '.$target;
	if (array_key_exists($source, $opj) && array_key_exists($target, $opj)) {
		// pass replacements
		$routeParams = $opj[$source].','.$opj[$target].','.$month.','.$priority;
		$newSource = $opj[$source]; $newTarget = $opj[$target]; 
		getRoutePl($newSource,$newTarget,$month,$priority);
	} else {
		if (
			!(array_key_exists($source, $opj)) &&
			!(array_key_exists($target, $opj))
		) {
			echo "<p>Sorry, neither ".$source." nor ".$target." are available, ";
		} else if (!(array_key_exists($source, $opj)))
		{
			echo "<p>Sorry, ".$source." is not available, ";
		} else {
			echo "<p>Sorry, ".$target." is not available, ";
		};
		echo "here's what we do have:</p><em>Pleiades ID</em> =>
			<em>ORBIS ID</em><br/>";
		foreach ($opj as $k => $v) {
			echo "$k => $v<br/>";
		}	
	}
}

//function getRoutePl($source, $target, $month, $priority) {
function getRoutePl($s,$tg,$m,$p) {
	global $og_route; // keep passing this along in order to use it in here
	header('Content-Type: text/html; charset=utf-8');
	include("../../conn/conn_webapp.php");
	include("util.php");
	if (!$vehicle) {
		$vehicle = "foot";
		//$priority = 1;
		$coastal = "false";
		$modelist = "ferry,road,fastup,fastdown,overseas,coastal";
	}
	// database connection
	$link = pg_connect($connectString_orbis) or die;

	$sql_base="
	SELECT 
	source,
	source_id,
	target,
	target_id,
	length as len,
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
	
	if ($p == 0){
	$sql = $sql_base.
	"
	FROM o_routing_cheapest(".$s.",".$tg.",".$m.",'".$vehicle."','".$modelist."')
	";
	}
	else if ($p == 1){
	$sql = $sql_base.
	"
	FROM o_routing_fastest(".$s.",".$tg.",".$m.",'".$vehicle."','".$modelist."')
	";
	}
	else {
	$sql = $sql_base.
	"
	FROM o_routing_shortest(".$s.",".$tg.",".$m.",'".$vehicle."','".$modelist."')
	";
	}
	//echo $sql;
	if (!$link) {
		 echo "error, didn't make the pg_connect()";
	} else {
		//echo "Here's the route, make of it what you will...\n\n";
		$result = pg_query($link, $sql);
		if (!$result) {
			echo "error, no result!<br/>";
			print pg_last_error($link);	  
			exit;
		} else {
			$trimmed = array();
			$r = 0;
			while ($row = pg_fetch_row($result)) {
				array_push($trimmed, $row);
			  $r++;
			}
			// echo $trimmed;
			$sorted = array();
			$currentpoint = $s;
			$finalpoint = $tg;
			$maxdistance = 0;
			$maxduration = 0;
			while (count($sorted) < count($trimmed)) {
				foreach ($trimmed as $t){
					if ($t[1] == $currentpoint){
					array_push($sorted, $t);
					$currentpoint = $t[3];
					$maxdistance = $maxdistance + $t[4];			    
					$maxduration = $maxduration + $t[5];			    
					}
				} 
			}
		}
		$delim = '';
		echo '{"route": "' . $og_route .'","km":"' . $maxdistance .
			'","days":"' . $maxduration . '","sites": [';
		foreach ($sorted as $s) {
			echo $delim.'{';
			echo '"start_name": "'.$s[0].'"';
			echo ',"start_oid": "'.$s[1].'"';
			echo ',"target_name": "'.$s[2].'"';
			echo ',"target_oid": "'.$s[3].'"';
			echo ',"km": "'.$s[4].'"';
			echo ',"days": "'.$s[5].'"';
			echo ',"type": "'.$s[6].'"';
			echo '}';
			$delim = ',';
		}
		echo ']}';

		/* echo indent(
			json_encode(
				array_map(
					function($key, $value) { return array($key, $value); },
					array_keys($sorted),
					array_values($sorted)
				)
			)
		); */
		//echo '<br/>dist: '.$maxdistance .', dur: '. $maxduration;
	//echo indent(json_encode($sorted));
	pg_close($link);
	}
}
?>