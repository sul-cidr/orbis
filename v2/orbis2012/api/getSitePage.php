<?php
function getSitePage($id) {
	header('Content-Type: text/html; charset=utf-8');
	include("../../conn/conn_webapp.php");
	include("util.php");
	// get a Json object with the site and its connected sites; reformat as geojson
	// database connection
	// echo "we are in getSites.php";
	$sql="
	SELECT 
	'{\"type\": \"FeatureCollection\",'||
	'\"features\": [' ||
	'{\"type\": \"Feature\",' ||
	'\"geometry\": {' ||
	'\"type\": \"Point\",' ||
	'\"coordinates\": [' ||
	st_x(o_sites.the_geom)::Numeric(8,3) || ',' || st_y(o_sites.the_geom)::Numeric(8,3) || ']},' ||
	'\"properties\": {' ||
	'\"prefName\": \"' || o_sites.label || '\",' ||
	'\"OrbisSiteId\": ' || o_sites.objectid || ',' ||
	'\"isPort\": \"' || o_sites.isport || '\",' ||
	'\"pleiades_uri\": \"' ||
	(  case
		when o_sites.\"PLPATH\" like '/places%' then 'http://pleiades.stoa.org' || o_sites.\"PLPATH\"
		else 'no Pleiades URI yet'
	end) || '\",' ||
	'\"relativeSize\": ' || o_sites.rank || '},' ||
	'\"routes\": {' ||
	'\"type\": \"FeatureCollection\",' ||
	'\"features\": [' || 
	string_agg(
	
	'{\"type\": \"Feature\",' ||
	'\"geometry\": {' ||
	'\"type\": \"Point\",' ||
	'\"coordinates\": [' ||
	st_x(t.the_geom)::Numeric(8,3) || ',' || st_y(t.the_geom)::Numeric(8,3) || ']},' ||
	'\"properties\": {' ||
	'\"prefName\": \"' || t.label || '\",' ||
	'\"ORBIS siteId\": ' || o_routing.target || ',' ||
	'\"routeType\": \"' || o_routing.type || '\",'||
	'\"distance\": ' || 
	(st_length(Geography(ST_Transform(o_routing.the_geom,4326))) / 1000)::Numeric(8,0) || '}' ||
	'}' ,','	
	
	) 
	||']}}]}'
	FROM o_sites
	LEFT JOIN o_routing ON o_routing.source = objectid AND o_routing.month IN (0,7) 
		AND o_routing.type NOT IN ('fastup','fastdown')
	LEFT JOIN o_sites as t ON t.objectid = o_routing.target
	WHERE ".$id." =
        CASE WHEN $id < 70000 THEN o_sites.objectid
        ELSE o_sites.pleiades_id
        END
	AND o_sites.displayed = 1
	GROUP BY
	o_sites.objectid,
    o_sites.pleiades_id,
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
				//$j = json_encode($row[0]);
				$j = $row[0];
	  			echo $result[0];
			}
		}
	}
	//echo indent(json_encode($sorted));
	pg_close($link);
$html = <<<EOT
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ORBIS site $id</title>
	<link rel="stylesheet" href="http://orbis.stanford.edu/api/css/leaflet.css" />
	<link rel="stylesheet" href="http://orbis.stanford.edu/api/css/leaflet.label.css" />
	<link rel="stylesheet" href="http://orbis.stanford.edu/api/css/sitepage.css" type="text/css" />
	<script src="http://orbis.stanford.edu/api/js/leaflet.js"  type="text/javascript"></script>  
	<script src="http://orbis.stanford.edu/api/js/leaflet.label.js"></script>
</head>
<body>
	<!-- build detail content -->
	<script>
        if ($id < 79000) {siteid = '{'+$id+'}'} else {siteid = '{'+$id+'}';};
        //siteid = $id;
		var qSite = $j;
		var connSites = qSite.features[0].routes;
		var siteName = qSite.features[0].properties.prefName;
		var port = qSite.features[0].properties.isPort;
		var rSize = qSite.features[0].properties.relativeSize;
        var oId = qSite.features[0].properties.OrbisSiteId;
		var plUri = qSite.features[0].properties.pleiades_uri;
		var siteDetails = " \
		<table width='100%' border='0' cellspacing='0' cellpadding='2'>	\
		  <tr><td width=20%>prefName</td><td>"+siteName+"</td></tr>	\
		  <tr><td width=20%>isPort</td><td>"+port+"</td></tr> \
		  <tr><td width=20%>relativeSize</td><td>"+rSize+"</td></tr> \
          <tr><td width=20%>ORBIS siteId</td><td>"+oId+"</td></tr> \
		  <tr><td width=20%>Pleiades URI</td><td>"+plUri+"</td></tr> \
		</table>"
		var routesDetails = "<table width='100%' border='0' cellspacing='0' cellpadding='2'> \
			<tr><td colspan='2' style='background-color:#E7D19A;color:#993333;'><strong>Connected Sites/Routes</strong></td></tr>";
		for(var i=0;i<connSites.features.length;i++){
			var obj = connSites.features[i].properties;
			for(var key in obj){
				var attrName = key;
				var attrValue = obj[key];
				routesDetails += "<tr><td>"+attrName+"</td><td>"+attrValue+"</td></tr>";
			}
			routesDetails += "<tr><td width=20%>&nbsp;</td><td>&nbsp;</td></tr>"
	};
	routesDetails += "</table>";
	</script>      

	<div id="content">
		<div id="banner"><span id="wordmark">ORBIS</span> The Stanford Geospatial Network Model of the Roman World</div>
		<div id="header"></div>
		<script>
		if (!(typeof siteid === "undefined")) {document.getElementById('header').innerHTML = "<h2>"+siteName+" "+siteid+" and connected sites</h2>";} else {
			document.getElementById('header').innerHTML = "<h2>Sorry, no site with that ID</h2>";}
		</script>
		<div id="main">
			<div id="map"></div>

			<script>
				var map = L.map('map').setView([41.890169, 12.492269], 3);
				// uh, dunno
				// jsonObject = eval($j);
				
				/*L.tileLayer('http://{s}.tile.cloudmade.com/{key}/22677/256/{z}/{x}/{y}.png', {
					attribution: 'basemap data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2012 CloudMade',
					key: '96fdfd79055c4839b4dea6ab152710d7'
				}).addTo(map); */
				
				L.tileLayer.wms("http://regis-dev.stanford.edu/geoserver/orbis/wms", {
				 layers: 'orbis:ne10m',
				 format: 'image/png',
				 transparent: true,
				 attribution: "Natural Earth tiles"
				}).addTo(map);
				
				L.tileLayer.wms("http://regis-dev.stanford.edu/geoserver/orbis/wms", {
				 layers: 'orbis:o_roads',
				 format: 'image/png',
				 transparent: true,
				 attribution: "Roman roads from ORBIS "
				}).addTo(map);
				
				// labels get obscured by sites
				/*L.tileLayer.wms("http://regis-dev.stanford.edu/geoserver/orbis/wms", {
				 layers: 'orbis:o_sites',
				 format: 'image/png',
				 transparent: true,
				 attribution: "Roman sites from ORBIS "
				}).addTo(map);	*/			
				
				function onEachFeature(feature, layer) {
					var popupContent = "<h3>the queried site:</h3>";
					if (feature.properties && feature.properties.prefName) {
							popupContent += feature.properties.prefName;
							popupContent += '<br/>http://pleiades.stoa.org' + feature.properties.pleiades_uri;
					}
						layer.bindPopup(popupContent);
				}
				function onEachConn(feature, layer) {
					var popupContent = "<h3>a connected site:</h3>";
					if (feature.properties && feature.properties.prefName) {
							popupContent += feature.properties.prefName;
							popupContent += '<br/>connected by: ' + feature.properties.routeType;
							popupContent += '<br/>at distance of: ' + feature.properties.distance + 'km';
					}
						layer.bindPopup(popupContent);
						mapbnds = [];
						mapbnds.push();
				}
				<!-- singleton feature in the foo geoJson object -->
				var siteLayer = L.geoJson(qSite, {
					pointToLayer: function (feature, latlng) {
						return L.circleMarker(latlng, <!--L.marker(latlng, {icon: baseballIcon}-->
							{radius: 8, fillColor: "#ff7800", color: "#000", weight: 1, opacity: 1, fillOpacity: 0.8 }
						);
					},
					onEachFeature: onEachFeature
				}).addTo(map);
				<!-- feature collection culled from qSite geoJson object -->	
				var connSiteLayer = L.geoJson(connSites, {
					pointToLayer: function (feature, latlng) {
						return L.circleMarker(latlng, <!--L.marker(latlng, {icon: baseballIcon}-->
							{radius: 6, fillColor: "#336699", color: "#000", weight: 1, opacity: 1, fillOpacity: 0.8 }
						);
					},
					onEachFeature: onEachConn
				}).addTo(map);
				map.fitBounds(connSiteLayer.getBounds()).zoomOut();
				// collection -->
				/* var connSiteLayer = L.geoJson(foo, {
					style: function (feature) {
						return feature.routes.properties && feature.routes.properties.style;
					},
					onEachFeature: onEachFeature,
					pointToLayer: function (feature.routes, latlng) {
						return L.circleMarker(latlng, {
							radius: 8, fillColor: "#ff7800", color: "#000", weight: 1, opacity: 1, fillOpacity: 0.8 });
					}
				} 
				).addTo(map);*/
				/* add icons at some point; seems to require absolute paths inn here
				var baseballIcon = L.icon({
					iconUrl: 'http://orbis-dev.stanford.edu/orbisapi/images/baseball-marker.png',
					iconSize: [32, 37],
					iconAnchor: [16, 37],
					popupAnchor: [0, -28]
				}); */
			</script>
		<div id="details">
		<div id="sitedetails"></div>
		<div id="routesdetails"></div>
		</div>
		</div><!-- end .main-->
		<div id="footer"><img src="http://orbis-dev.stanford.edu/orbisapi/images/logo_sul.png" align="absmiddle" />&nbsp;&nbsp;&copy; 2012</div>
		<script>
			document.getElementById('sitedetails').innerHTML = siteDetails
			document.getElementById('routesdetails').innerHTML = routesDetails
		</script>
		</div><!-- end .content-->
</body>
</html>
EOT;
echo $html;
};
?>