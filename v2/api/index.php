<?php
// Require the Slim Framework
require 'Slim/Slim.php';
include 'getRoute.php';
include 'getRoutePl.php';
require 'getSites.php';
require 'getSite.php';
require 'getSitePage.php';
//autoloader
\Slim\Slim::registerAutoloader();
// Instantiate a Slim application
$app = new \Slim\Slim();

// display errors or not...
ini_set('display_errors', 'on');
error_reporting(1);
// some variables?
$template_paths = "";

// Define the Slim application routes
$app->get('/sites', 'getSites');
$app->get('/sites/:id', 'getSite');
$app->get('/paths/:s/:t', 'getPaths');
$app->get('/route/:source/:target/:month/:priority', 'getRoute');
$app->get('/route_pl/:source/:target/:month/:priority', 'checkIds');
// this one to load a site detail page wiith map, etc.
$app->get('/site/:id', 'getSitePage');
/* http://orbis-dev.stanford.edu/orbisapi/tripCost.php?s=50012&t=50112&m=6&p=1&v=foot&ml=ferry,road,fastup,fastdown,overseas,coastal*/
$app->get('/', function () {
    $template = <<<EOD
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Orbis API</title>
			<link href="css/slim.css" rel="stylesheet" type="text/css" />
			<script>
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-30365192-1']);
				_gaq.push(['_trackPageview']);
				(function() {
					var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					po.src = 'https://apis.google.com/js/plusone.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				})();
				(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();    
				///
			</script>
			</head>
        <body>
			<div id="image"  style="float:left;">
					<img src="images/atlas200h.jpg" />
			</div>
			<div id="intro" style="clear:right; height:180px; padding-top:30px; ">
				<h1 style="margin-bottom:5px;">Welcome to the <span style="color:#993333;">ORBIS</span> API</h1>
				<p style="font-size:95%;">Data from <br/><a href="http://orbis.stanford.edu" target = "_blank">ORBIS: The Stanford Geospatial Network Model of the Roman World</a> 
				<p>Everything begins with <strong>http://orbis.stanford.edu/api</strong></p>
			</div>
            <section style = "border-bottom: double #993333;">
                <h2>What you can GET so far</h2>
					<p>permanent URI to a site page describing nearest neighbor road and river network connections</p>
					<pre><strong>/site</strong>/{site_id)</pre>
					<p>JSON responses for all sites, a single site and a route</p>
					<pre><strong>/sites</strong></pre>
                    <p style="font-size:.8em;padding-left:20px;">returns {orbis_id, name, lat, lon, Pleiades ID if known, contributor, isPort (t/f), size rank(6-10)}</p>
					<pre><strong>/sites/</strong>{site_id}</pre>
					<pre><strong>/route/</strong>{fromsite}/{tosite}/{month}/{priority}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">use ORBIS IDs</span></pre>
					<pre><strong>/route_pl/</strong>{fromsite}/{tosite}/{month}/{priority}&nbsp;&nbsp;&nbsp;<span style="font-weight:bold;">use Pleiades IDs</span></pre>
			</section>
			<section>
				{site_id} is an integer. Enter either an ORBIS or Pleiades ID.<br/>
				{fromsite}, and {tosite} are integers. Use pairs of ORBIS site IDs or <br/>&nbsp;&nbsp;Pleiades place IDs as indicated above.<br/>
				{month} is an integer from 1 to 12, like 6 for June<br/>
				{priority} is either 1 or 2 (fastest or shortest, respectively)</p>
            </section>
        </body>
    </html>
EOD;
    echo $template;
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});
// Run the Slim applications
$app->run();
/* replacing these with functions in their own 'required' files
function getSites() {
	echo "running the getSites() function<br/>";
	$sql = "SELECT this, that, the_other FROM o_sites";
	echo "we'll do something like '". $sql."'";
}
function getSite($id) {
	echo "running the getSite() function for " .$id. "<br/>";
	$sql = "SELECT this, that FROM o_sites WHERE objectid = ".$id;
	echo "we'll do something like '". $sql."'";
}*/
function getPaths($from, $to) {
    $template = <<<EOD
<!DOCTYPE html>
		<html><head>
		    <meta charset="utf-8"/>
            <title>ORBIS paths query</title>
			<link href="css/slim.css" rel="stylesheet" type="text/css" />
		</head>
		<body>
		<p>So you wanted all orbis paths (road segments, river reaches) between $from and $to? Coming up...</p>
		<img src="http://orbis-dev.stanford.edu/orbisapi/images/atlas200h.jpg" width="125" height="200" />
		</body></html>

EOD;
    echo $template;
}
function router($source, $target, $month, $priority) {
    echo "The <strong>".$priority."</strong> route between <strong>".$source."</strong> and <strong>".$target. "</strong> in the month of <strong>".$month."</strong> <br/>given all mode and vehicle options is as follows: <br/>";
}
