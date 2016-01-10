<?php
function getSite($id) {
//	echo($id);
//	echo "you are in getSite(id) now";
	header('Content-Type: text/html; charset=utf-8');
	include("../../conn/conn_webapp.php");
	include("util.php");
	// database connection
	// echo "we are in getSites.php";
	$sql="
	SELECT 
	'{\"site_id\": '||
	o_sites.objectid||
	',\"prefname\": \"'||
	o_sites.label||
	'\",\"longitude\": '||
	st_x(o_sites.the_geom)::Numeric(8,3)||
	',\"latitude\": '||
	st_y(o_sites.the_geom)::Numeric(8,3)||
	',\"pleiades_uri\": \"'||
	(  case
		when o_sites.\"PLPATH\" like '/places%' then o_sites.\"PLPATH\"
		else 'no Pleiades ID yet'
	end)||
	'\",\"isport\": \"'||
	o_sites.isport||
	'\",\"relative_size\": '||
	o_sites.rank/10||
	',\"routes\": ['||
	string_agg(

	'[\"'||t.label||
	'\",'||
	o_routing.target||',\"'||
	o_routing.type||'\",'||
	(st_length(Geography(ST_Transform(o_routing.the_geom,4326))) / 1000)::Numeric(8,0)
	||']'
	,','
	)
	||']}'
	FROM o_sites
	LEFT JOIN o_routing ON o_routing.source = objectid AND o_routing.month IN (0,7) AND o_routing.type NOT IN ('fastup','fastdown')
	LEFT JOIN o_sites as t ON t.objectid = o_routing.target
	WHERE ".$id." =
        CASE WHEN $id < 70000 THEN o_sites.objectid
        ELSE o_sites.pleiades_id
        END
	AND o_sites.displayed = 1
	GROUP BY
	o_sites.objectid,
	o_sites.label,
	o_sites.the_geom,
	o_sites.\"PLPATH\",
	o_sites.isport,
	o_sites.rank
	
		";
	
	$link = pg_connect($connectString_orbis) or die;
	if (!$link) {
		 echo "error, didn't make the pg_connect()";
	} else {
		// echo "Here's a site...\n\n";
		$result = pg_query($link, $sql);
		if (!$result) {
			echo "no site with that id, sorry!<br/>";
			print pg_last_error($link);	  
			exit;
		} else {
			while ($row = pg_fetch_row($result)) {
	  			echo $row[0];
			}
		}
	}
	//echo indent(json_encode($sorted));
	pg_close($link);
}

?>