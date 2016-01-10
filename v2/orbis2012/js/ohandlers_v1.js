Ext.namespace('o');
o.featIdx = 0;
function doAll() {
// read choices on form and run doBigQuery() 
	sourceValue = getSource.value;
	targetValue = getTarget.value,
	monthValue = getMon.value,
	modeList = "ferry,";
	p = fp.getForm().findField('rbpriority').getGroupValue();
	riverboatSpeed = fp.getForm().findField('rbriverboat').getGroupValue(); // 0 = civilian; 1 = military
	shipSpeed = fp.getForm().findField('rbship').getGroupValue();
	v = fp.getForm().findField('rbvehicle').getGroupValue();
	if (Ext.getCmp('landChecked').checked) {modeList += Ext.getCmp('landChecked').value + ',';};
	// if (Ext.getCmp('overseasChecked').checked) {modeList += Ext.getCmp('overseasChecked').value + ',';};
	if (Ext.getCmp('riversChecked').checked) 
		if (riverboatSpeed == 0) {modeList += Ext.getCmp('riversChecked').value + ','; boatType = 'civilian';}
		else {modeList += 'fastup,fastdown,';  boatType = 'military';};
	if (shipSpeed == 0) {shipType = 'slower';}
		else {shipType = 'faster';};

// NEW NEW LOGIC, 4/29
if (shipType == 'faster' && Ext.getCmp('overseasChecked').checked) {modeList += 'overseas,'}
if (shipType == 'slower' && Ext.getCmp('overseasChecked').checked) {modeList += 'slowover,'}

if (shipType == 'faster' && Ext.getCmp('coastalChecked').checked) {modeList += 'coastal,'}
if (shipType == 'slower' && Ext.getCmp('coastalChecked').checked) {modeList += 'slowcoast,'}

if (shipType == 'faster' && Ext.getCmp('daylightChecked').checked) {modeList += 'dayfast,'}
if (shipType == 'slower' && Ext.getCmp('daylightChecked').checked) {modeList += 'dayslow,'}
// end NEW NEW LOGIC

if(sourceValue !== '' && targetValue !== '' && monthValue !== '') {
	doBigQuery();} else {alert('We need Start, Destination and Month values!')};

_gaq.push(['_trackEvent', 'TabNavV1', 'calculate route']);
} // end doAll

// begin d3 functions, copied from ohandlers13.js 27 Apr 13:30pfunction clip(d) {	  return path(d);	}function cartclip(d) {	cartpath = d3.geo.path()	.projection(projection);		  return path(d);	}
function cartogram10() {		// ignore if there's already one	if (!o.cartodone) {	o.cartodone = true;	var svg = d3.select("div#cartotab10").append("svg")	    .attr("width", 960)	    .attr("height", 500);	projection = d3.geo.azimuthal()    .scale(1000)    .origin([12.485073740960559,41.891009607907208])    .mode("orthographic")    .translate([480, 250]);		path = d3.geo.path()	.projection(projection);	d3.json("data/cartdat.json", function(json) {		  var xramp=d3.scale.linear().domain([-8.5,43]).range([0,1]);		  var yramp=d3.scale.linear().domain([55.5,22.5]).range([0,1]);		  var colorramp=d3.scale.linear().domain([0,100,200]).range(["red","yellow","blue"]);		  var sites = json;		  cartoSetCenter = 2;		  restoreCarto = true;						d3.json("via/romeland.json", function(json) {				  var med = json;				  newMap = svg.selectAll("path.countries")			      .data(med.features)			    .enter().append("path")					.style("fill", "lightgray")					.style("stroke", "black")					.style("stroke-width", 1)					.style("opacity", 1)					.attr("d", clip)			  d3.json("via/basepaths.json", function(collection) {			  baseRoads = svg.selectAll("path.routes")			      .data(collection.features)			    .enter().append("svg:path")			      .style("fill", "none")			      .style("stroke", function(d) {return (d.c == "d" ? "#4e4e4e" : "#7ca5e4")})			      .attr("d", clip)			      .attr("class", "baseroads")			      .style("stroke-width", 2)			      .style("opacity", 0)				      .transition()				      .duration(1000)				      .style("opacity", 1)			      	      ;				  var colorramp=d3.scale.linear().domain([0,3,25]).range(["red","yellow","blue"]);						  var cartSites = svg.selectAll("g.sites")				  .data(sites)				  ;			  var cartSitesEnter = cartSites.enter().append("svg:g")			  .attr("class", "sites")		      			  .attr("transform", function(d) {return "translate(" + projection([d.xcoord,d.ycoord]) + ")";})		      .style("cursor", "pointer")			  .on("click", mouseclick)			  ;				  			  cartSites			  .append("rect")					  .attr("width", function(d) { return ((d.scale < 110) ? d.scale / 12 : 15)})			  .attr("height", function(d) { return ((d.scale < 110) ? d.scale / 12 : 15)})			  .attr("fill", function(d) { return (colorramp(d.we_rom))})			  .attr("rx", function(d) { return ((d.scale < 110) ? 10 : 10)})			  .attr("ry", function(d) { return ((d.scale < 110) ? 10 : 10)})			  .attr("stroke", function(d) { return ((d.scale < 110) ? "black" : "black")})			  .attr("stroke-width", function(d) { return ((d.scale < 110) ? 1 : 3)})			  .attr("class", "siterects")			  .style("opacity", 1)			  ;			  cartSites			  .append("text")			  .attr("x", -5)			  .attr("y", +5)			  .text(function(d) { return ((d.scale > 90) ? d.name : "")})			  .attr("class", "dclabel")			  .attr("id", function(d, i) { return "bgtext_" + d.name})			  .style("fill","white")			  .style("stroke","white")			  .style("stroke-width",4)			  .style("opacity",.6)			  ;			  			  cartSites			  .append("text")			  .attr("x", -5)			  .attr("y", +5)			  .text(function(d) { return ((d.scale > 90) ? d.name : "")})			  .attr("class", "dclabel")			  .attr("id", function(d, i) { return "text_" + d.name})			  ;			  			  var legendt = svg.selectAll("text.legendtext")			  .data([0, 1.5, 3, 12, 25])			  .enter().append("text")			  .attr("x", function(d, i) { return ((i * 50) + 25)})			  .attr("y", 475)			  .attr("class", "legendtext")			  .text(function(d, i) { return (i == 4) ? ("" + d + " denarii") : ("" + d)})			  ;			  var titletext = svg.selectAll("text.titletext")			  .data([0])			  .enter().append("text")			  .attr("x", 20)			  .attr("y", 450)			  .attr("class", "titletext")			  .text("Grain distance to Rome in Winter")			  ;			})			  })		  	  		  svg.selectAll("circle.dccircle")		  .data([1.0, .9, .8, .7, .6, .5, .4, .3, .2, .1])		  .enter().append("circle")				  .attr("cx", 480)		  .attr("cy", 250)		  .attr("r", function(d) { return (d * 960)})		  .attr("class", "dccircle")		  .style("stroke", "gray")		  .style("fill", "white")		  .style("opacity", 0);			  svg.selectAll("rect.legendrects")			  .data([0, 20, 40, 60, 80, 100, 120, 140, 160, 180, 200])			  .enter().append("rect")					  .attr("x", function(d) { return (d + 20)})			  .attr("y", 480)			  .attr("width", 20)			  .attr("height", 10)			  .attr("fill", function(d) { return (colorramp(d))})			  .attr("class", "legendrects")			  .style("opacity", 1)			  ;		  var clearlabelt = svg.selectAll("text.clearbtn")		  .data(["CLEAR LABELS","RESET LABELS"])		  .enter().append("text")		  .attr("x", 860)		  .attr("y", function(d, i) { return (i * 20) + 20})		  .attr("width", 100)		  .attr("height", 100)		  .style("stroke", "gray")		  .text(function(d) { return d })		  .attr("class", "clearbtn")		  		  .style("cursor", "pointer")		  .on("click", clearlabels)		  ;		  		  		  var selectiontext = svg.selectAll("text.ringlabel")		  .data([1.0, .9, .8, .7, .6, .5, .4, .3, .2, .1])		  .enter().append("text")		  .attr("x", function(d) { return ((d  * 960) + 480)})		  .attr("y", 250)		  .text(function(d) { return d})		  .attr("class", "ringlabel")		  .attr("opacity", 0);		  ;		  function mouseclick(d, i) {			  			  this.parentNode.appendChild(this);   			var bglabelTarget = d3.select(document.getElementById("bgtext_" + d.name));			var labelTarget = d3.select(document.getElementById("text_" + d.name));						if (d.scale < 110) {			    if (labelTarget.text() == d.name){			    	labelTarget.text("");			    	bglabelTarget.text("");			    }			    else {			    	labelTarget.text(d.name);			    	bglabelTarget.text(d.name);			    }			}			else {				var cheapBtn = document.getElementById('rb_cheapcarto');				var monthBtn = document.getElementById('rb_jancarto');								switch(d.name)				{				case "Roma":				  var centerPoint = 2;				  break;				case "Constantinopolis":					  var centerPoint = 3;					  break;				case "Londinium":					  var centerPoint = 4;					  break;				case "Antiochia":					  var centerPoint = 5;					  break;				}							shiftcarto(centerPoint, cheapBtn.checked, monthBtn.checked);			}		  }		  function clearlabels(d, i) {			  if (i == 0) {			  svg.selectAll("text.dclabel")			  			    .transition()			    .duration(800)	   		  	.text("")			  ;			  }			  else {				  svg.selectAll("text.dclabel")				    .transition()				    .duration(800)		   		  	.text(function(d) { return ((d.scale > 90) ? d.name : "")})			  }		  }		  	}	); // d3	}; // end if(!cartodone)	} //end cartogram10()	function shiftcarto(center, cheaptrue, jantrue) {		var newTitleText = "";		//now get segments and draw d3 graph		var svg = d3.select("div#cartotab10 svg");		if (cheaptrue == true) {			var pathtype = "e";			var ringtype = " denarii";					max = 25;			mid = 3;						newTitleText = "Grain distance to ";		}		else {			var pathtype = "s";			max = 60;			mid = 10;			var ringtype = " days";						newTitleText = "Travel time to ";		}		if (jantrue == true) {			var monthtype = "w";		}		else {			var monthtype = "s";		}		if (center == 1) {			center = cartoSetCenter;			restoreCarto = true;		}		else if (center == 6) {			center = cartoSetCenter;				}		else {			cartoSetCenter = center;			restoreCarto = false;			}				if (center == 2) {			var datapath = "" + monthtype + pathtype + "_rom";			centerx = 12.49;			centery = 41.89;			newTitleText = newTitleText + "Rome in ";				}		else if (center == 3) {			var datapath = "" + monthtype + pathtype + "_con";			centerx = 28.99;			centery = 41.02;						newTitleText = newTitleText + "Constantinopolis in ";				}		else if (center == 4) {			var datapath = "" + monthtype + pathtype + "_lon";			centerx = -0.08;			centery = 51.52;			newTitleText = newTitleText + "Londinium in ";							}		else if (center == 5) {			var datapath = "" + monthtype + pathtype + "_ant";			centerx = 36.17;			centery = 36.21;			newTitleText = newTitleText + "Antiochia in ";				}				if (monthtype == "s") {			newTitleText = newTitleText + "Summer";					}		else {			newTitleText = newTitleText + "Winter";								}			var xramp=d3.scale.linear().domain([-8.5,43]).range([0,960]);			var yramp=d3.scale.linear().domain([55.5,22.5]).range([0,500]);			var colorramp=d3.scale.linear().domain([0,mid,max]).range(["red","yellow","blue"]);			var costramp=d3.scale.linear().domain([0,max]).range([0,1]);	// get an event recorded			_gaq.push(['_trackEvent', 'TabNavCarto', newTitleText]); 			  			  svg.selectAll("text.titletext")			  .transition()			  .duration(3000)			  .text(newTitleText)			  ;				svg.select(document.getElementById("nodistort")).remove();	if (restoreCarto == true) {					  svg.selectAll("g.sites")			    .transition()			    .duration(3000)			     .attr("transform", function(d) {return "translate(" + projection([d.xcoord,d.ycoord]) + ")";})//		      .attr("transform", function(d) {return "translate("+ (xramp(d.xcoord)) + "," + (yramp(d.ycoord)) + ")";})		      ;			  			  svg.selectAll("rect.siterects")			    .transition()			    .duration(3000)			  .attr("fill", function(d) { return (colorramp(d[datapath]))})			  .style("opacity", .8)			  ;			  svg.selectAll("circle.dccircle")			    .transition()			    .duration(3000)			  .style("opacity", 0)			  ;			  svg.selectAll("text.ringlabel")			    .transition()			    .duration(3000)				  .style("opacity", 0)			    ;			  svg.selectAll("path")			    .transition()			    .duration(3000)			    .style("opacity", 1)			    ;	}	else {			  svg.selectAll("g.sites")			    .transition()			    .duration(3000)		      .attr("transform", function(d) {return "translate("+ (findx(d[datapath],d.xcoord,d.ycoord,centerx,centery)) + "," + (findy(d[datapath],d.xcoord,d.ycoord,centerx,centery)) + ")";})			  ;			  svg.selectAll("rect.siterects")			    .transition()			    .duration(3000)			  .attr("fill", function(d) { return (colorramp(d[datapath]))})			  .style("opacity", .8)			  ;			  svg.selectAll("circle.dccircle")			    .transition()			    .duration(3000)			  .style("opacity", 100)			  ;			  svg.selectAll("text.ringlabel")			  .data([1.0, .9, .8, .7, .6, .5, .4, .3, .2, .1])			  .text(function(d) { return (d == .2) ? "" + (Math.round((d * max) * 10)/10 ) + ringtype : (Math.round((d * max) * 10)/10 )})				    .transition()				    .duration(3000)				    .style("opacity", 1);			  ;			  svg.selectAll("path")				    .transition()				    .duration(3000)				    .style("opacity", 0);			  ;			  function cartoProject(costin, thisx, thisy, cenx, ceny) {					var costramp=d3.scale.linear().domain([0,max]).range([0,1000]);									  var projectedCoordsThis = projection([thisx,thisy]);					  var projectedCoordsCen = projection([cenx,ceny]);						  						var xdiff = xramp(projectedCoordsThis[0]) - xramp(projectedCoordsCen[0]) + .001;						var ydiff = yramp(projectedCoordsThis[1]) - yramp(projectedCoordsCen[1]) + .001;								var hypotenuse = Math.sqrt((Math.pow(xdiff,2)) + (Math.pow(ydiff,2)));						var ratio = costramp(costin) / hypotenuse;										  return [((ratio * xdiff) + 480),((ratio * ydiff) + 250)];				  }			  function findx(costin, thisx, thisy, cenx, ceny) {					var costramp=d3.scale.linear().domain([0,max]).range([0,1000]);										  var projectedCoordsThis = projection([thisx,thisy]);						  var projectedCoordsCen = projection([cenx,ceny]);						  							var xdiff = xramp(projectedCoordsThis[0]) - xramp(projectedCoordsCen[0]) + .001;							var ydiff = yramp(projectedCoordsThis[1]) - yramp(projectedCoordsCen[1]) + .001;														var hypotenuse = Math.sqrt((Math.pow(xdiff,2)) + (Math.pow(ydiff,2)));						var ratio = costramp(costin) / hypotenuse;				  return (ratio * xdiff) + 480;			  }			  function findy(costin, thisx, thisy, cenx, ceny) {					var xramp=d3.scale.linear().domain([-8.5,43]).range([0,960]);					var yramp=d3.scale.linear().domain([55.5,22.5]).range([0,500]);										var costramp=d3.scale.linear().domain([0,max]).range([0,1000]);						var xdiff = xramp(thisx) - xramp(cenx) + .001;						var ydiff = yramp(thisy) - yramp(ceny) + .001;												var hypotenuse = Math.sqrt(Math.pow(xdiff,2) + Math.pow(ydiff,2));						var ratio = costramp(costin) / hypotenuse;					  				  return (ratio * ydiff) + 250;			  }		}	var legendt = svg.selectAll("text.legendtext")	.data([0, mid/2, mid, (max/2), max])	.text(function(d, i) { return (i == 4) ? ("" + d + ringtype) : ("" + d)})	  .transition()	  .duration(3000)	.style("opacity", .8)	;	}
//makeGraph09()
tripArray = [];
function makeGraph09(stored, index) {

	tripIndex = index;
	//now get segments and draw d3 graph
	var d3s = getSource.value;
	var d3t = getTarget.value;
	var d3m = getMon.value;
	var d3v = fp.getForm().findField('rbvehicle').getGroupValue();
	var d3p = fp.getForm().findField('rbpriority').getGroupValue();
	var d3ml = modeList;
	handler = "handlers/tripCost_v1.php?m="+d3m+"&s="+d3s+"&t="+d3t+"&v="+d3v+"&p="+d3p+"&ml="+d3ml;
	// alert(handler);
	
	if (stored == false) {
	d3.json(handler, function(json) {
	  trip = json;
	  tripArray.push(trip);
	  processGraph14(tripArray[tripArray.length - 1]);
	})
	}
	else {
		  processGraph14(tripArray[index]);
	}
}

function processGraph14(thisTrip) {
	  d3.selectAll("circle.gracircle")
	  	.attr("opacity", 1)
		  .remove(); 

	  d3.selectAll("text.gratext")
	  	.attr("opacity", 1)
		  .remove();

	  d3.selectAll("rect.grarect")
	  	.attr("opacity", .5)
		  .remove();
	  
	  d3.select("#ograph")
	  		.attr("opacity", 1)
		  .remove(); 
	  // start new
	  var canv = d3.select("div#south svg").attr("width");
	  var svg = d3.select("div#south svg").append("svg:svg").attr("id", "ograph");
	  
	  trimmed = unique(thisTrip); 
	  
	  svg.selectAll("line.legline")
	  	.data(trimmed)
	  	.enter().append("svg:line")
	  	.attr("x1", 900)
	  	.attr("x2", 980)
	  	.attr("y1", function (d, i) { return ((i < 2) ? (i * 15) + 12 : (i * 15) + 26)})
	  	.attr("y2", function (d, i) { return ((i < 2) ? (i * 15) + 12 : (i * 15) + 26)})
	  	.attr("fill", "none")
		.attr("class", "legline")
	  	.attr("stroke", function(d, i) { return d.color})
	  	.style("stroke-width", 4);
	  var selectiontext = svg.selectAll("text.legtext")
		.data(trimmed)
		.enter().append("text")
	  	.attr("x", 910)
	  	.attr("y", function (d, i) { return ((i < 2) ? (i * 15) + 11 : (i * 15) + 24)})
		.attr("class", "legtext")
		.text(function(d) { return d.type })
	  ;

	  svg.selectAll("path.grapath")
	  	.data(thisTrip)
	  	.enter().append("svg:path") 	
	  	.attr("d", function(d, i) {
	    var dx = ((d.start * 800) + 15) - ((d.distance * 800) + 15),
	        dy = ((i == 1 || i%5 == 0) ? 35 : ((i%2 == 1) ? (((i%5 + 2) * 4)) : (-(i%5 + 2) * 4)) + 38) - ((d.distance == 1 || i%5 == 4) ? 35 : ((i%2 == 0) ? (((i%5 + 3) * 4)) : (-(i%5 + 3) * 4)) + 38),
	        dr = (Math.sqrt(dx * dx + dy * dy)) + 90;
	    return "M" + ((d.start * 800) + 15) + "," + ((i == 1 || i%5 == 0) ? 35 : ((i%2 == 1) ? (((i%5 + 2) * 4)) : (-(i%5 + 2) * 4)) + 38) + "A" + dr + "," + dr + " 0 0,1 " + ((d.distance * 800) + 15) + "," + ((d.distance == 1 || i%5 == 4) ? 35 : ((i%2 == 0) ? (((i%5 + 3) * 4)) : (-(i%5 + 3) * 4)) + 38);
	  })
		.attr("class", "grapath")
	  	.attr("fill", "none")
	  	.attr("stroke", function(d, i) { return d.color})
	  	.style("stroke-width", 3); 

		var ramp=d3.scale.linear().domain([0,trip.length]).range(["yellow","orange"]);
		
	  svg.selectAll("circle.gracircle")
		.data(thisTrip)
		.enter().append("circle")		
		.attr("cx", function(d) { return ((d.distance * 800) + 15)})
		.attr("cy", function (d, i) { return ((d.distance == 0 || d.distance == 1 || i%5 == 4) ? 35 : ((i%2 == 0) ? (((i%5 + 3) * 4)) : (-(i%5 + 3) * 4)) + 38)})
		.attr("r", function(d) { return ((d.scale < 10) ? d.scale / 2 : 8)})
		.attr("fill", function(d,i) { return (ramp(i))})
		.attr("class", "gracircle")
		.style("opacity", 50)
		.label;
  
	  var selectiontext = svg.selectAll("text.gratext")
		.data(thisTrip)
		.enter().append("text")
		.attr("x", function(d) { return ((d.distance * 800))})
		.attr("y", function (d, i) { return ((d.distance == 0 || d.distance == 1 || i%5 == 4) ? 43 : ((i%2 == 0) ? (((i%5 + 3) * 4)) : (-(i%5 + 3) * 4)) + 44)})
		.attr("class", "gratext")
		.text(function(d) { return d.name })
		.attr("opacity", function(d) { return (((d.scale > 9 && trip.length < 15) || d.distance == 0 || d.distance == 1) ? 100 : 0)})
		    .on("mouseover", mouseover)
		    .on("mouseout", mouseout);
	  ;

	  var selectiontext = svg.selectAll("rect.grarect")
		.data(thisTrip)
		.enter().append("rect")
	  	.attr("opacity", function(d) { return ((d.duration == 0) ? 0 : .3) })
		.attr("x", function(d) { return ((d.distance * 800) + 14)})
		.attr("y", 0)
		.attr("width", 2)
		.attr("height", 100)
		.attr("class", "grarect")
;
	  var selectiontext = svg.selectAll("text.tlabels")
		.data(thisTrip)
		.enter().append("text")
	  	.attr("opacity", .7)
	  	.attr("x", function(d,i) { return ((i == 0) ? 15 : (d.duration > 10) ? ((d.distance * 800) - 2) : ((d.distance * 800) + 4))})
		.attr("y", 10)
		.text(function(d,i) { return (i == 0) ? "Day" : (((d.duration == 0) ? "" : "" + d.duration)) })
;	  
	  function mouseover(d, i) {
		  d3.select(this)
		      .attr("opacity", 100);
		}

		function mouseout(d, i) {
		  d3.select(this)
			.attr("opacity", function(d) { return (((d.scale > 9 && trip.length < 15) || d.distance == 0 || d.distance == 1) ? 100 : 0)})
		}
		
		function unique(origArr) {  
		    var newArr = [],  
		        origLen = origArr.length,  
		        found,  
		        x, y;  
		  
		    for ( x = 0; x < origLen; x++ ) {  
		        found = undefined;  
		        for ( y = 0; y < newArr.length; y++ ) {  
		            if ( origArr[x].type === newArr[y].type ) {  
		              found = true;  
		              break;  
		            }  
		        }  
		        if ( !found) newArr.push( origArr[x] );  
		    }  
		   return newArr;  
		};  
} // end makeGraph09()
// end d3 functions

// doBigQuery()
function doBigQuery() {
   //alert('shipType: '+ shipType + ', modelist: ' + modeList);
	var req = OpenLayers.Request.GET( {
		 // relative path
		url: 'handlers/q_big_v1.php',
		//complete: Ext.getBody().mask('Loading... Please wait', 'loading'),
		//success: Ext.getBody().unmask,//{Ext.getBody().mask('Loading... Please wait', 'loading')},
		// pass parameters
		params: {
			s: getSource.value,
			t: getTarget.value,
			m: getMon.value,
			v: fp.getForm().findField('rbvehicle').getGroupValue(),
			p: fp.getForm().findField('rbpriority').getGroupValue(),
			ml: modeList,
			riv: boatType,
			sea: shipType
		},
		callback: function(req) {
			// when server answers, do this
			response = req.responseText;
			if ((/^ERROR|INFO/).test(response) || response === '') {
			  alert("ERROR/INFO" + response);
			} else {
			// alert (response);
			jsonObject = eval('(' + response + ')');
			// this is a single GeoJSON Feature object
			omap.j_format = new OpenLayers.Format.GeoJSON();
			// parse it
			omap.vectorFeature = omap.j_format.read(jsonObject, 'Feature'); // 
			var distance = Math.round(omap.vectorFeature.data.distance);
			var duration = Math.round(omap.vectorFeature.data.duration*10)/10;
			var exp_w = Math.round(omap.vectorFeature.data.exp_w*100)/100;
			var exp_d = Math.round(omap.vectorFeature.data.exp_d*100)/100;
			var exp_p = Math.round(omap.vectorFeature.data.exp_p*100)/100;  
			
			if (exp_w == exp_d) {
				exp_blurb = '<p style="font-size:1.2em;">Prices in <span class="italic">denarii</span>, based on the use of a <b>'+shipType+'</b> '+
				'sail ship and a <b>'+boatType+'</b> river boat (where applicable):<br/>&nbsp;* Per kilogram of wheat: <b>'+exp_d+
				'</b><br/>&nbsp;* Per passenger: <b>'+exp_p+'</b> </p>';
				} else {
				exp_blurb = '<p style="font-size:1.2em;">Prices in <span class="italic">denarii</span>, based on the use of a <b>'+shipType+'</b> '+
				'sail ship and a <b>'+boatType+'</b> river boat (where applicable), and on these road options:<br/>&nbsp;* Per kilogram of '+
				'wheat (by donkey): <b>'+exp_d+'</b><br/>'+'&nbsp;* Per kilogram of wheat (by wagon): <b>'+exp_w+'</b><br/>&nbsp;* Per passenger '+
				'in a carriage: <b>'+exp_p+'</b></p>';}
					
			// kml into string
			omap.kmlRoute = omap.vectorFeature.attributes.kml;
			// populate response box
			var srcidx = o_sites_store.find('prefname', getSource.getValue());
			var taridx = o_sites_store.find('prefname', getTarget.getValue());
			var monidx = months_store.find('text', getMon.getValue());
			var means = fp.getForm().findField('rbvehicle').getGroupValue();
			o.priority = fp.getForm().findField('rg_priority').getValue().boxLabel;
			o.pstring = fp.getForm().findField('rg_priority').getValue().ref;
			o.pnum = fp.getForm().findField('rbpriority').getGroupValue();
			routeOptions = o.priority + '; '  + v + '; ' + 'modeList: ' + modeList;
			kmlOptions = o.pstring + '; '  + v + '; ' + 'modeList: ' + modeList;
			
			if (omap.vectorFeature) {
			// we have feature geometry, add to map, build kml and csv representations
				o.featIdx +=1; // add one
				// insert a value to the feature object; now available in the store and as attribute of selected feature
				omap.vectorFeature.attributes.featid = o.featIdx;	// back off one to match d3 index
				
				vectorLayer.addFeatures(omap.vectorFeature, options); 
				addKML(omap.kmlRoute,0); addCSV();
				// osites.setZIndex(999);
				allbounds = new OpenLayers.Bounds();
				allbounds.extend(vectorLayer.getDataExtent());
				map.zoomToExtent(allbounds)
		
				o.resultBlurb = '<p style="font-size:1.2em;">The '+ o.priority+' journey from <b style="color: #993333;">' + 
					getSource.getRawValue() + '</b> to <b style="color: #993333;">'+ getTarget.getRawValue() + '</b> in <b>' + 
					getMon.getRawValue() + '</b> takes <b>' + duration + 
					' days</b>, covering <b>' + distance + ' kilometers</b>.</p><hr>' + exp_blurb;
				document.getElementById("result_blurb").innerHTML = o.resultBlurb;
				makeGraph09(false,-1);  //
				showResult();  // opens and positions little window with tab getting contents of the div result_blurb
				} else {
				 alert("There is no omap.vectorFeature, (poss. no route found with those options)...");
				}
			} // end if (omap.vectorFeature)
		} // end callback: function(req)
  } ); // end  OpenLayers.Request.GET
  return false; 
} // end doBigQuery()
