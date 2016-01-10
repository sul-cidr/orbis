<?php
// Require the Slim Framework
require 'Slim/Slim.php';
include 'getRoute.php';
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
// this one to load a site detail page wiith map, etc.
$app->get('/site/:id', 'getSitePage');

$app->get('/', function () {
    $template = <<<EOD
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Orbis API</title>
			<link href="css/slim.css" rel="stylesheet" type="text/css" />
			<script type="text/javascript">
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', 'UA-30365192-1']);
				_gaq.push(['_trackPageview']);
				(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
			</script>
        </head>
        <body>
            <header>
                <img src="images/atlas200h.jpg" width="125" height="200" />
            </header>
            <h1 style="margin-bottom:5px;">Welcome to the <span style="color:#993333;">ORBIS</span> API</h1>
				<p>Data from <a href="http://orbis.stanford.edu" target = "_blank">ORBIS: The Stanford Geospatial Network Model of the Roman World</a> 
				<p>Everything begins with <strong>http://orbis.stanford.edu/api</strong></p>
            <section style = "border-bottom: double #993333;">
                <h2>What you can GET so far</h2>
					<p>permanent URI to a site page describing nearest neighbor road and river network connections</p>
					<pre><strong>/site</strong>/{site_id)</pre>
					<p>&nbsp;</p>
					<p>JSON responses for all sites, a single site and a route</p>
					<pre><strong>/sites</strong></pre>
					<pre><strong>/sites/</strong>{site_id}</pre>
					<pre><strong>/route/</strong>{fromsite}/{tosite}/{month}/{priority}</pre>
			</section>
			<section>
				{site_id}, {fromsite}, and {tosite} are numbers, learn them with <strong>/sites</strong><br/>
				{month} is a number from 1 to 12, like 6 for June<br/>
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
// these two aren't called
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
		<img src="http://orbis.stanford.edu/api/images/atlas200h.jpg" width="125" height="200" />
		</body></html>

EOD;
    echo $template;
}
function router($source, $target, $month, $priority) {
    echo "The <strong>".$priority."</strong> route between <strong>".$source."</strong> and <strong>".$target. "</strong> in the month of <strong>".$month."</strong> <br/>given all mode and vehicle options is as follows: <br/>";
}
