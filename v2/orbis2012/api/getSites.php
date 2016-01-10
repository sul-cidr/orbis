<?php
function getSites() {
	header('Content-Type: text/html; charset=utf-8');
	include("../../conn/conn_webapp.php");
	include("util.php");
	// database connection
	$link = pg_connect($connectString_orbis) or die;
	if (!$link) {
		 echo "error, didn't make the pg_connect()";
	} 
	$sql="
	SELECT 
	objectid AS osite_id,
	label AS prefname,
	\"XCOORD\" AS longitude,
	\"YCOORD\" AS latitude,
	(  case
		when \"PLPATH\" like '/places%' then substr(\"PLPATH\",9,6)
		else 'no Pleiades ID yet'
	end) AS pleiades_uri,
	\"Contributo\" AS contributor,
	isport,
	(rank / 10)
	FROM o_sites
	WHERE displayed = 1
	ORDER BY prefname
	";
	// echo $sql;
	if (!$link) {
		 echo "error, didn't make the pg_connect()";
	} else {
		//echo "Here's all our sites, Pleiades or otherwise...\n\n";
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
		}
/*		$sorted = array();
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
		}*/
	}
	echo indent(
		json_encode(
			array_map(
				function($key, $value) { return array($key, $value); },
				array_keys($trimmed),
				array_values($trimmed)
			)
		)
	);
	//echo indent(json_encode($sorted));
	pg_close($link);
}

?>