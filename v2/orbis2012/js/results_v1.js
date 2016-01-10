/*!
 * Main query form, including stores for months and sites 
 * ext06a succeeds in getting a json response, but doesn't register success
 * so it doesn't process the response
 */
Ext.namespace('oresults');
// CSV
oresults.csv = 'routenum,start,destination,month,distance,#days,Exp_Donkey,Exp_Wagon,Exp_Pass,Priority,Mode_Options,Road_VehicleOption\n'
oresults.csvrownum = 0
function addCSV(){ 
	oresults.csvrownum += 1
	oresults.row = oresults.csvrownum + ',"' + getSource.getRawValue() + '","' + getTarget.getRawValue() + '","' + getMon.getRawValue() + '",' +  
	omap.vectorFeature.data.distance+','+omap.vectorFeature.data.duration+','+omap.vectorFeature.data.exp_d+','+omap.vectorFeature.data.exp_w + ',' +
	omap.vectorFeature.data.exp_p + ',"' + o.pstring + '","' + modeList + '","' + v + '"\n';
	oresults.csv += oresults.row;
}
function downloadCSV(){
	if (oresults.row !== null){ //there's been at least one route calculated
		// if (!oresults.footer) {oresults.kml += '</Document></kml>'; oresults.footer = true;} // add footer
		//uriContent = "data:application/octet-stream," + encodeURIComponent(omap.kmlRoute);
		uriContent = "data:application/octet-stream;filename=ORBIS_routes.csv," + encodeURIComponent(oresults.csv);
		newWindow=window.open(uriContent, 'ORBIS_routes.csv');	
		_gaq.push(['_trackEvent', 'TabNavV1', 'download_csv']);
	} else {alert('no routes calculated yet')};
}

//KML
oresults.kml = '';
oresults.kmlout = '';
oresults.placemark = '';
oresults.footer = false;
oresults.head = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom"><Document><name>kml-sample.kml</name><StyleMap id="msn_ylw-cheapest"><Pair><key>normal</key><styleUrl>#sn_ylw-cheapest</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#sh_ylw-cheapest</styleUrl></Pair></StyleMap><Style id="sh_ylw-cheapest"><IconStyle><scale>1.2</scale></IconStyle><LineStyle><color>bf00ff00</color><width>4</width></LineStyle></Style><Style id="sn_ylw-cheapest"><LineStyle><color>bf00ff00</color><width>4</width></LineStyle></Style><StyleMap id="msn_ylw-fastest"><Pair><key>normal</key><styleUrl>#sn_ylw-fastest</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#sh_ylw-fastest</styleUrl></Pair></StyleMap><Style id="sh_ylw-fastest"><IconStyle><scale>1.2</scale></IconStyle><LineStyle><color>bf7f00ff</color><width>4</width></LineStyle></Style><Style id="sn_ylw-fastest"><LineStyle><color>bf7f00ff</color><width>4</width></LineStyle></Style><StyleMap id="msn_ylw-shortest"><Pair><key>normal</key><styleUrl>#sn_ylw-shortest</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#sh_ylw-shortest</styleUrl></Pair></StyleMap><Style id="sh_ylw-shortest"><IconStyle><scale>1.2</scale></IconStyle><LineStyle><color>bf00ffff</color><width>4</width></LineStyle></Style><Style id="sn_ylw-shortest"><LineStyle><color>bf00ffff</color><width>4</width></LineStyle></Style>';
function addKML(newroute, pnum){ 
	if (o.pnum == 0) {style='<styleUrl>#msn_ylw-cheapest</styleUrl>'} else if 
		(o.pnum == 1) {style='<styleUrl>#msn_ylw-fastest</styleUrl>'} else 
		{style='<styleUrl>#msn_ylw-shortest</styleUrl>'}; 
	rOptions = 	'Priority: <b>'+ o.pstring + '</b> route<br/>'+'Vehicle class:&nbsp;&nbsp;<b>'+ v +'</b><br/>'+
		//'Allowed network modes:<b><br/>' + modeList.substring(0, modeList.length -1) +'</b>';
		'Allowed network modes:<b><br/>' + modeList +'</b>';
	oresults.placemark = '<Placemark><name>'+getSource.getRawValue()+'-'+getTarget.getRawValue()+', '+getMon.getRawValue()+
	'</name><description><![CDATA[ <b>OPTIONS CHOSEN</b> <br/>'+ rOptions +'<br/><img src="http://orbis.stanford.edu/images/ShipBlackWhite_400w.png" alt="'+
	'Ulixes mosaic at the Bardo Museum in Tunis, Tunisia. 2nd century AD (WikiMedia)" />]]></description>' + style + newroute + '</Placemark>';
	oresults.kml += oresults.placemark;
}

function downloadKML(){
	if (oresults.kml !== ''){ //there's been at least on route calculated
		// always build a new outfile: header + current results + footer
		oresults.kmlout = oresults.head + oresults.kml + '</Document></kml>';
		uriContent = "data:application/octet-stream;filename=ORBIS_routes.kml," + encodeURIComponent(oresults.kmlout);
		newWindow=window.open(uriContent, 'ORBIS_routes.kml');	
		_gaq.push(['_trackEvent', 'TabNavV1', 'download_kml']);
	} else {alert('there are no routes in the result grid!')};
}
// routeStore.data.items[0].data.destination
Ext.onReady(function(){
	o.resultgridShown = false
	Ext.QuickTips.init();  
    // turn on validation errors beside the field globally
	Ext.form.Field.prototype.msgTarget = 'side';
    // store bound to layer, populated by geoJson from route calculations
	routeStore =  new GeoExt.data.FeatureStore({
		layer: vectorLayer,
		initDir: 1,
		fields: [
				{name: 'featid',	type: 'integer'},
				{name: 'start'},
				{name: 'destination'},
				{name: 'month'},			
				{name: 'distance',	type: 'integer'},
				{name: 'duration',   type: 'float'},
				{name: 'exp_d',      type: 'float'},
				{name: 'exp_w',      type: 'float'},
				{name: 'exp_p',      type: 'float'},
				{name: 'priority'},
				{name: 'vehicle'},
				{name: 'options'},
				{name: 'boat'},
				{name: 'ship'}
		]
	});

	// a grid to fill with routing results disabled to test above replacement 
	gp = new Ext.grid.GridPanel({
		store: routeStore,
		renderTo:'resultsDiv',
		columns: [
				new Ext.grid.RowNumberer(),
				{header: 'Start', dataIndex: 'start', sortable: true},
				{header: 'Destination', dataIndex: 'destination', sortable: true},
				{header: 'Month', dataIndex: 'month', width: 50, sortable: true},			
				{header: 'Distance', dataIndex: 'distance', width: 60, sortable: true},
				{header: '# Days', dataIndex: 'duration', width: 52, sortable: true},
				{header: 'Exp_Donk', dataIndex: 'exp_d', width: 65, sortable: true},
				{header: 'Exp_Wag', dataIndex: 'exp_w', width: 65, sortable: true},
				{header: 'Exp_Pass', dataIndex: 'exp_p', width: 65, sortable: true},
				{header: 'Priority', dataIndex: 'priority', width: 77, sortable: true},
				{header: 'Road', dataIndex: 'vehicle', width: 105, sortable: true},
				{header: 'Mode Options', dataIndex: 'options', width: 195, sortable: false, 
					tooltip: 'summary of mode options chosen', renderer: renderIcon('question')}
			],
		sm: new GeoExt.grid.FeatureSelectionModel( {singleSelect: true }),
		stripeRows: true,
		autowidth: true, // container is 355
		autoHeight: true,
	}); 
	
	function renderIcon(val) {
    return '<img src=ico/"' + val + '.png" />';
	}

// update resultsWin on grid row aelect
vectorLayer.events.on({
	"featureselected": function(e) {
		selStart = gp.selModel.getSelected().data.start
		selDest = gp.selModel.getSelected().data.destination
		selMonth = gp.selModel.getSelected().data.month
		selExpD = gp.selModel.getSelected().data.exp_d
		selExpW = gp.selModel.getSelected().data.exp_w
		selExpP = gp.selModel.getSelected().data.exp_p
		selPriority = gp.selModel.getSelected().data.priority
		selDistance = gp.selModel.getSelected().data.distance
		selDuration = gp.selModel.getSelected().data.duration
		selBoatType = gp.selModel.getSelected().data.boat
		selShipType = gp.selModel.getSelected().data.ship
		selVeh = gp.selModel.getSelected().data.vehicle
		selRowIndex = gp.selModel.last
		selIndex = gp.selModel.getSelected().data.featid
		
		if (selExpW == selExpD) {
			exp_blurb = '<p style="font-size:1.2em;">Prices in <span class="italic">denarii</span>, based on the use of a <b>'+selShipType+'</b> '+
			'sail ship and a <b>'+selBoatType+'</b> river boat (where applicable):<br/>&nbsp;* Per kilogram of wheat: <b>'+selExpD+
			'</b><br/>&nbsp;* Per passenger: <b>'+selExpW+'</b> </p>';
			} else {
			exp_blurb = '<p style="font-size:1.2em;">Prices in <span class="italic">denarii</span>, based on the use of a <b>'+selShipType+'</b> '+
			'sail ship and a <b>'+selBoatType+'</b> river boat (where applicable), and on these road options:<br/>&nbsp;* Per kilogram of '+
			'wheat (by donkey): <b>'+selExpD+'</b><br/>'+'&nbsp;* Per kilogram of wheat (by wagon): <b>'+selExpW+'</b><br/>&nbsp;* Per passenger '+
			'in a carriage: <b>'+selExpP+'</b></p>';}
			
			o.resultBlurb = '<p style="font-size:1.2em;">The '+ selPriority+' journey from <b style="color: #993333;">' + 
			selStart + '</b> to <b style="color: #993333;">'+ selDest + '</b> in <b>' + selMonth + '</b> takes <b>' + selDuration + ' days</b>, covering <b>' + selDistance + ' kilometers</b>.</p><hr>' + exp_blurb;
		
			document.getElementById("result_blurb").innerHTML = o.resultBlurb;
		
		makeGraph09(true, selIndex-1);
	},
     "scope": vectorLayer
});
	

});

	// end print stuff
	
// showResults()
var resultWin /* try to make it global */
var resultGridWin

function showResult(){
	// create the window on the first click and reuse on subsequent clicks
	if(!resultWin){
		resultWin = new Ext.Window({
			applyTo:'result-win',
			layout:'fit',
			width:400,
			autoHeight: true,
			//height:200,
			bodyStyle: 'background-color: #fff',
			closeAction:'hide',
			//plain: true,
			collapsible: true,
			expandOnShow: true,
			contentEl: 'result_blurb'
		});
	}
	resultWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [10, -110]);
	if(resultGridWin) {if(resultGridWin.isVisible()){resultGridWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [10, -5]);}}
};	

function showResultGrid(){
	// create the window on the first click and reuse on subsequent clicks
	o.resultgridShown = true;
	if(routeStore.getCount() == 0) {alert('No results to show yet!');} else 
	{if(!resultGridWin){
		resultGridWin = new Ext.Window({
			// html: 'a grid of route results will appear here',
			// applyTo:'resultsDiv',
			title: 'All route results (select path on map or row in grid to highlight, click elsewhere on map to de-select)',
			contentEl: 'resultsDiv',
			layout:'fit',
			width:980,
			autoHeight: true,
			// height:400,
			closeAction:'hide',
			plain: true,
			collapsible: true,
			expandOnShow: true,
			tbar: ['-',
				{text:'download routes in <b>KML</b> format (rename file with \'.kml\' extension', scale:'small', handler: downloadKML, 
				icon: 'ico/save.gif', tooltip: 'save file, with  \'.kml\' extension, then open in Google Earth'},
				{icon: 'ico/question14.png', handler: helpWin.createDelegate(this,['kml', 'south', '[0, -175]'])},
				//, handler: helpWin('kml','resultGridWin')},
				'-','-',
				{text:'download routes in <b>CSV</b> format', scale:'small', handler: downloadCSV, 
				icon: 'ico/save.gif', tooltip: 'save file, with  \'.csv\' extension, open in spreadsheet'},
				{icon: 'ico/question14.png', handler: helpWin.createDelegate(this,['csv', 'south', '[0, -175]'])}/*,
				{icon: 'ico/grid.png', scale: 'small', handler: gridPrintWin	},{
            text: "Print map...",
            handler: function(){
                // convenient way to fit the print page to the visible map area
                printPage.fit(mapPanel, true);
                omap.printProvider.print(mapPanel, printPage);
            }
        	} */
			],
			autoscroll: true,
		})};
	}

	omap.gridup = true;
	resultGridWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [10, -5]);
	// resultGridWin.show(this).alignTo(Ext.getBody(), "bl-bl?", [410, -90]);
};	// end resultGrid()


