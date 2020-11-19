/***  orbis map 29 Apr 2013 geodata.stanford.edu ***/
Ext.namespace('omap');
omap.windowwidth = window.innerWidth;

var mapBounds = new OpenLayers.Bounds( -14.67,21.65,45.3,57.5 );
var options = {
	controls: [
		new OpenLayers.Control.ScaleLine(),
		new OpenLayers.Control.LayerSwitcher({'ascending':false, 'roundedCornerColor':'#993333'}),
		//new OpenLayers.Control.MousePosition({element: $('location')}),
		new OpenLayers.Control.Scale($('scale')),
		new OpenLayers.Control.ZoomToMaxExtent(),
		new OpenLayers.Control.PanZoom(),
		new OpenLayers.Control.Navigation(
			{mouseWheelOptions: {interval: 100}})
	],
	resolutions: [.07,.06,.055,.05,.045 ,.04, .035,.03,.025,.02,.0175,.0087,.0065, .004],
	//maxResolution: 0.17578125,
	maxExtent: new OpenLayers.Bounds(-14.67,21.65,45.3,57.5),
	restrictedExtent: mapBounds,
	theme: null
	};
var map = new OpenLayers.Map('map', options);
var mapMinZoom = 0;
var mapMaxZoom = 5;
// map layers
var baseNE = new OpenLayers.Layer.WMS(
	//" Natural Earth terrain", "http://orbis-prod.stanford.edu/geoserver/orbisv1/wms",
	"Natural Earth terrain", "http://regis-prod.stanford.edu/geoserver/orbisv1/wms",
	//" Natural Earth terrain", "http://regis-dev.stanford.edu/geoserver/orbis/wms",
	{LAYERS: 'orbis:ne10m'  },
	{isBaseLayer: true, bgcolor: '#434A44' } //, buffer: 0 }
)
var baseVMap = new OpenLayers.Layer.WMS(
	"Modern boundaries", "http://vmap0.tiles.osgeo.org/wms/vmap0",
	{layers: "basic"},
	{isBaseLayer: true }
	)
var osites = new OpenLayers.Layer.WMS(
	//"Cities and towns", "http://orbis-prod.stanford.edu/geoserver/orbisv1/wms",
	"Cities and towns", "http://regis-prod.stanford.edu/geoserver/orbisv1/wms",
	//"Cities and towns", "http://regis-dev.stanford.edu/geoserver/orbis/wms",
	//{layers: 'barrington:o_sites', transparent: true },
	{layers: 'orbis:o_sites', transparent: true },
	{isBaseLayer: false }
  )
var oroads = new OpenLayers.Layer.WMS(
	//" Roman roads", "http://orbis-prod.stanford.edu/geoserver/orbisv1/wms",
	"Roman roads", "http://regis-prod.stanford.edu/geoserver/orbisv1/wms",
	//" Roman roads", "http://regis-dev.stanford.edu/geoserver/orbis/wms",
	{layers: 'orbis:o_roads', transparent: true }
	//{isBaseLayer: false },
	//{buffer: 0 }
  )

var styleMap = new OpenLayers.StyleMap({
	'default': new OpenLayers.Style({
		strokeColor: '#ffffff',
		strokeWidth: 4,
		strokeOpacity: .7 ,
		fontColor: 'black',
		fontSize: '14px',
		fontFamily: 'Courier New, monospace',
		fontWeight: 'bold',
		labelAlign: 'cm',
		labelOutlineColor: 'white',
		labelOutlineWidth: 2,
		//graphicZIndex: 3,
	}, {
	'select': new OpenLayers.Style({
		strokeColor: '#ffffff',
		strokeWidth: 8
	})
	})
})

var lookup = {
  "cheapest <img src='ico/rte-cheap.png' align='texttop'>": {strokeColor: "#33ff33", strokeOpacity: .7},
  "fastest <img src='ico/rte-fast.png' align='texttop'>": {strokeColor: "#ff33ff", strokeOpacity: .7},
  "shortest <img src='ico/rte-short.png' align='texttop'>": {strokeColor: "#ffff00", strokeOpacity: .9}

}
var lookupSel = {
  "cheapest <img src='ico/rte-cheap.png' align='texttop'>": {strokeColor: "#ffffff", strokeWidth: 7, strokeOpacity: .8},
  "fastest <img src='ico/rte-fast.png' align='texttop'>": {strokeColor: "#ffffff", strokeWidth: 7, strokeOpacity: .8},
  "shortest <img src='ico/rte-short.png' align='texttop'>": {strokeColor: "#ffffff", strokeWidth: 7, strokeOpacity: .8}

}
styleMap.addUniqueValueRules("default", "priority", lookup);
styleMap.addUniqueValueRules("select", "priority", lookupSel);

vectorLayer = new OpenLayers.Layer.Vector(' Calculated paths', {styleMap: styleMap})

map.addLayers([baseNE, baseVMap, oroads, vectorLayer, osites]);

if (OpenLayers.Util.alphaHack() == false) { baseNE.setOpacity(0.7); }

map.zoomTo(omap.properzoom);
omap.properzoom = function() {
	if (omap.windowwidth < 1340 ) {return 0}
		else if (omap.windowwidth < 1490) {return 1}
		else if (omap.windowwidth < 1570) {return 3}
		else if (omap.windowwidth < 1610) {return 4}
		else if (omap.windowwidth < 1700) {return 5}
		else {return 6};
};
mapPanelB = new GeoExt.MapPanel({
	title: "Mapping ORBIS",
	map: map,
	zoom: omap.properzoom(),
	center: ([15, 41.5]),
	region: 'center',
	listeners: {
		scope: this,
		activate: function (tab) {
			omap.windowwidth = window.innerWidth;
			map.zoomTo(omap.properzoom());
			Ext.getCmp('eastPanel').show();
			Ext.getCmp('eastPanel').expand();
			Ext.getCmp('southPanel').show();
			// Ext.getCmp('southPanel').expand(true);
			// Ext.getCmp('westPanel').show();
			if(resultWin)
				{resultWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [10, -110]);};
			if(resultGridWin) //(omap.gridup == true)
				{resultGridWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [410, -110]);};
			Ext.getCmp('fubar').doLayout();
			// google analytics
			_gaq.push(['_trackEvent', 'TabNavV1', 'Mapping-Map']);
		}
	}
});
