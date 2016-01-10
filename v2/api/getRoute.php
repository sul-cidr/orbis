<?php
function getRoute($source, $target, $month, $priority) {
    global $og_route;
	$og_route = $source.' to '.$target;
	header('Content-Type: text/html; charset=utf-8');
	include("../../../conn/conn_webapp.php");
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
	
	if ($priority == 0){
	$sql = $sql_base.
	"
	FROM o_routing_cheapest(".$source.",".$target.",".$month.",'".$vehicle."','".$modelist."')
	";
	}
	else if ($priority == 1){
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
        
		/*echo indent(
			json_encode(
				array_map(
					function($key, $value) { return array($key, $value); },
					array_keys($sorted),
					array_values($sorted)
				)
			)
		); */
	//echo indent(json_encode($sorted));
	pg_close($link);
	}
}
?>