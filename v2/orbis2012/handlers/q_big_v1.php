<?php
// https://orbis.stanford.edu/orbis_dev/handlers/q_big14.php?s=50191&t=50367&m=1&v=donkey&p=1&ml=ferry,road,fastup,fastdown,overseas,coastal&riv=civilian&sea=slower
$source = $_GET['s'];
$target = $_GET['t'];
$month = $_GET['m'] ;
$vehicle = $_GET['v'] ;
$priority = $_GET['p'];
$modes = rtrim($_GET['ml'],',');
$aqriver = $_GET['riv'];
$aqsea = $_GET['sea'];

$terse = array("upstream,downstream", "fastup,fastdown");
$better   = array("civboat", "milboat");
$fancymodes = str_replace($terse,$better,rtrim($_GET['ml'],','));

// display errors or not...
ini_set('display_errors', 'on');
error_reporting(1);
// database connection
include("conn_webapp.php");
$dbconn = pg_connect($connectString_orbis);
//
if (!$dbconn) {
  handleError('Could not connect to the database');
}

$months = array(1 => 'January',2 => 'February',3 => 'March',4 => 'April',5 => 'May',6 => 'June',
	7 => 'July',8 => 'August',9 => 'September',10 => 'October',11 => 'November',12 => 'December');
// ------------------------------------------------------------------
// Run SQL
// ------------------------------------------------------------------
if ($priority == 0) {$func = 'o_routing_cheapest'; $pri = 'cheapest'; $ico='rte-cheap';} 
	elseif ($priority == 1) {$func = 'o_routing_fastest'; $pri = 'fastest'; $ico='rte-fast';}
	else {$func = 'o_routing_shortest'; $pri = 'shortest'; $ico='rte=short';}
/*
$sql = "SELECT sum(length) AS length, sum(duration) AS duration, sum(expense_donkey) AS exp_d, sum(expense_wagon) AS exp_w, 
	sum(expense_passenger) AS exp_p, ST_AsGeoJson(ST_Union(the_geom)) AS geometry, ST_AsKML(ST_Union(the_geom)) AS kml 
	FROM ".$func."(".stripslashes($source).",".stripslashes($target).",".stripslashes($month).",'".stripslashes($vehicle).
	"','".stripslashes($modes."transferc,transferf,transfero,transferr")."')";
*/
$sql2 = "with z as (
	SELECT ".$source." AS source, ".$target." AS target, ".$month."AS month, sum(length)::integer AS length, ".
	"sum(duration)::numeric(10,1) AS duration, sum(expense_donkey)::numeric(10,2) AS exp_d, sum(expense_wagon::numeric(10,2)) AS exp_w, ".
	"sum(expense_passenger)::numeric(10,2) AS exp_p, ST_AsGeoJson(ST_Union(the_geom)) AS geometry, ST_AsKML(ST_Union(the_geom)) AS kml 
	FROM  ".$func."(".stripslashes($source).",".stripslashes($target).",".stripslashes($month).",'".stripslashes($vehicle).
	"','".stripslashes($modes)."transferc,transferf,transfero,transferr"."')
	)
	select z.*, os1.label AS start, os2.label as destination, '".stripslashes($pri)."' AS priority from z 
	join o_sites os1 on z.source = os1.objectid join o_sites os2 on z.target = os2.objectid";
	
if (isset ($_GET['s']) && isset ($_GET['t']) && isset ($_GET['m'])) {
// echo $cmd2;
// else...
  // run query
  $res = pg_query($sql2);
  if ($res) {
    // query executed, so
    if (pg_num_rows($res) === 1) {
      // only if one line:
		  $arr = pg_fetch_object($res); // there's only one; call it $arr for convenience
        // $arr = pg_fetch_array($res);
        // succeeded...if there's a geometry proceed
        if ($arr) {// && $arr[5] !== false) {
            handleRes($arr);
        } else {
          //handleError('Array could not be filled.');
			 handleError('no result');
        }
       /*} else {
        handleError('More than one field returned.');
      } */
    } else {
      handleError('More than one line returned.');
    }
  } else {
    handleError('Query could not be executed [1]. PostgreSQL says "' . pg_last_error() . '"');
  }
} else {
  handleError('Didn\'t get a source and target. PostgreSQL says "' . pg_last_error() . '"');
}

// Report error and terminate
function handleError($string) {
  echo strip_tags(utf8_decode("ERROR:\n\n" . $string));
  die();
}
// Return info message and terminate
function handleInfo($string) {
  echo strip_tags(utf8_decode("INFO:\n\n" . $string));
  die();
}
// encode result and return it **ALL** as a geojson feature
function handleRes($string) {
$months = array(1 => 'January',2 => 'February',3 => 'March',4 => 'April',5 => 'May',6 => 'June',
	7 => 'July',8 => 'August',9 => 'September',10 => 'October',11 => 'November',12 => 'December');
$terse = array("upstream,downstream", "fastup,fastdown");
$better   = array("civboat", "milboat");
$fancymodes = str_replace($terse,$better,rtrim($_GET['ml'],','));
if ($string->priority == 'cheapest') {$ico='rte-cheap';} 
	elseif ($string->priority == 'fastest') {$ico='rte-fast';}
	else {$ico='rte-short';}
	$geoj = '{ "type": "Feature",'. 
  	'"properties": { "start": "'.$string->start.'", "destination": "'.$string->destination.'", "priority": "'.$string->priority.
	' <img src=\'ico/'.$ico.'.png\' align=\'texttop\'>", "month": "'.$months[$string->month].'", "distance": '.$string->length.
	', "duration": '.$string->duration.', "exp_d": '.$string->exp_d.', "exp_w": '.$string->exp_w.', "exp_p": '.$string->exp_p.
	', "options": "'.substr($fancymodes,6).'", "boat": "'.$_GET['riv'].'", "ship": "'.$_GET['sea'].'", "vehicle": "'.$_GET['v'].
	'", "featid": "","kml": "'.$string->kml.'"},'.'"geometry": '.$string->geometry.'}';
//rtrim(substr($_GET['ml'],6),',')

// must add start, dest and month here or add it to the geojson object later...
  echo json_encode($geoj);
  // echo json_encode($string->length);
  die(); 
}
// Minimal check for WKT syntax consistency ** not called for geojson
function isGeometry($string) {
  $types = array (
    'POINT',
    'LINESTRING',
    'POLYGON',
    'MULTIPOINT',
    'MultiLineString',
    'MULTIPOLYGON',
    'GEOMETRYCOLLECTION'
  );
  $re = '/^(' . implode($types, '|') . ')/i';
  return preg_match($re, $string);
}
?>