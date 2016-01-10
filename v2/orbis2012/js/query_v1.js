/*!
 * Main query form, including stores for months and sites 
 */
Ext.namespace('oq');
Ext.onReady(function(){
    Ext.QuickTips.init();  
    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
    var priorityGroup = {
		xtype: 'fieldset',
		title: 'Priority',
		autoHeight: true,
		layout: 'auto',
        items: [{
		  xtype: 'radiogroup',
		  id: 'rg_priority',
		  fieldLabel: '',
		  labelWidth: 0,
		  items: [
			  {boxLabel: 'fastest&nbsp;<img src="ico/rte-fast.png" align="texttop"/>', ref: 'fastest', name: 'rbpriority', inputValue: 1, 
			  	checked: true, 
			  	handler: function(field, value) {
					if (value) {
						Ext.getCmp('fs_expense').collapse();
						Ext.getCmp('rg_expense').disable();
						Ext.getCmp('rg_vehicle').enable();
						Ext.getCmp('fs_vehicle').expand();
					}
				} },
			  {boxLabel: 'cheapest&nbsp;<img src="ico/rte-cheap.png" align="texttop"/>', ref: 'cheapest', name: 'rbpriority', inputValue: 0, 
			  	handler: function(field, value) {
					if (value) {
						Ext.getCmp('fs_expense').expand();
						Ext.getCmp('rg_expense').enable();
						Ext.getCmp('rg_vehicle').disable();
						Ext.getCmp('fs_vehicle').collapse();
					} else {
						Ext.getCmp('fs_expense').collapse();
						Ext.getCmp('rg_expense').disable();
						Ext.getCmp('rg_vehicle').enable();
						Ext.getCmp('fs_vehicle').expand();
					}
				}
			  },
			  {boxLabel: 'shortest&nbsp;<img src="ico/rte-short.png" align="texttop"/>', ref: 'shortest', name: 'rbpriority', inputValue: 2, 
			  	handler: function(field, value) {
					if (value) {
						Ext.getCmp('fs_expense').expand();
						Ext.getCmp('rg_expense').enable();
						Ext.getCmp('rg_vehicle').enable();
						Ext.getCmp('fs_vehicle').expand();
					}
				}}  
			] //
		}]
    };
	 
	var modeGroup = {
		xtype: 'fieldset',
		title: 'Network mode',
		autoHeight: true,
		layout: 'auto',
        items: [{
		  xtype: 'checkboxgroup',
		  id: 'cg_mode',
		  fieldLabel: '',
		  labelWidth: 0,
		  // columns: 2,
		  //vertical: true,
		  items: [
		  	{columnWidth: '.40',
			layout: 'auto',
			items:[
				//{xtype: 'label', text: 'Heading 1', cls:'x-form-check-group-label', anchor:'-15'},
				{boxLabel: 'Road', value: 'road', id: 'landChecked', checked: true}, 
			  	{boxLabel: 'River', value: 'upstream\,downstream', id: 'riversChecked', checked: true,
			  		handler: function(field, value){
						if(!value) {Ext.getCmp('rg_riverboat').disable();} else {Ext.getCmp('rg_riverboat').enable();}}},
			  	{boxLabel: 'Open sea', value: 'overseas', id: 'overseasChecked', checked: true,
					handler: function(field, value){
						// if(value) {Ext.getCmp('daylightChecked').enable(); Ext.getCmp('daylightChecked').setValue(1);};
						if(!value && !Ext.getCmp('coastalChecked').checked && !Ext.getCmp('daylightChecked').checked) 
							{Ext.getCmp('rg_seaboat').disable(); }	else {Ext.getCmp('rg_seaboat').enable()}
						}},

			]}, {
			columnWidth: '.60', 
			items:[
				{boxLabel: 'Coastal sea', value: 'coastal', id: 'coastalChecked', checked: true,
					handler: function(field, value){
						if(value) {Ext.getCmp('daylightChecked').setValue(0);};
						if(!value && !Ext.getCmp('overseasChecked').checked && !Ext.getCmp('daylightChecked').checked) 
							{Ext.getCmp('rg_seaboat').disable(); }	else {Ext.getCmp('rg_seaboat').enable()}
						//if(!value && !Ext.getCmp('overseasChecked').checked) {Ext.getCmp('daylightChecked').disable(); }
					}},
				{boxLabel: 'Coastal sea (daylight)', value: 'daylight', id: 'daylightChecked', checked: false,
					handler: function(field, value){
						if(value) { Ext.getCmp('coastalChecked').setValue(0);};
						if(!value && !Ext.getCmp('coastalChecked').checked && !Ext.getCmp('overseasChecked').checked) 
							{Ext.getCmp('rg_seaboat').disable(); }	else {Ext.getCmp('rg_seaboat').enable()}
						// if(!value && !Ext.getCmp('overseasChecked').checked) {Ext.getCmp('daylightChecked').disable();}
					}}
			]}		  
		  ] 
		}]
    };			  	
	
	var boatGroup = {
		xtype: 'fieldset',
		title: 'Aquatic options',
		autoHeight: true,
		// columns: 2,
		labelWidth: 45,
		collapsible: true,
		collapsed: false,
        items: [{
		  xtype: 'radiogroup', id: 'rg_riverboat', fieldLabel: 'River', disabled: false,
		  items: [
			  {boxLabel: 'Military', name: 'rbriverboat', inputValue: 1, enabled: true}, // 
			  {boxLabel: 'Civilian', name: 'rbriverboat', inputValue: 0, checked: true},
			] //
		}, {
		  xtype: 'radiogroup', id: 'rg_seaboat', fieldLabel: 'Sea', disabled: false, // labelWidth: 60, 
		  items: [
			  {boxLabel: 'Faster', name: 'rbship', inputValue: 1, checked: true},
			  {boxLabel: 'Slower', name: 'rbship', inputValue: 0, enabled: true},
			] //
		}]
    };
    var vehicleGroup = {      
        xtype: 'fieldset',
		bodyStyle: 'border: none;',
        title: 'Road options',
        autoHeight: true,
        items: [{
			xtype: 'fieldset',
			id: 'fs_vehicle',
			layout: 'auto',
			title: 'Speed options',
			collapsible: true,
			collapsed: false,
			autoHeight: true,
			items: [{
				xtype: 'radiogroup',
				id: 'rg_vehicle',
				fieldLabel: '',
				columns: 1,
				items: [
				  {boxLabel: 'Foot/army/pack animal (mod. load)/<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;mule cart/camel caravan', 
					  id: 'foot', name: 'rbvehicle', inputValue: 'foot', checked: true},
				  {boxLabel: 'Rapid military march', id: 'rapid march', name: 'rbvehicle', inputValue: 'rapidmarch'},
				  {boxLabel: 'Ox cart', id: 'oxcart', name: 'rbvehicle', inputValue: 'oxcart'},
				  {boxLabel: 'Porter/fully loaded mule',id: 'porter & mule', name: 'rbvehicle', inputValue: 'porter'},
				  {boxLabel: 'Horseback rider (routine travel)',id: 'horseback-routine', name: 'rbvehicle', inputValue: 'horse'},
				  {boxLabel: 'Private travel (routine, vehicular)',id: 'vehicle-routine', name: 'rbvehicle', inputValue: 'privateroutine'},
				  {boxLabel: 'Private travel (accelerated,<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;vehicular/horseback)',id: 'horseback-accelerated', name: 'rbvehicle', 
				  	inputValue: 'privateaccelerated'},
				  {boxLabel: 'Fast carriage',id: 'fast carriage', name: 'rbvehicle', inputValue: 'fastcarriage'},
				  {boxLabel: 'Horse relay',id: 'horse relay', name: 'rbvehicle', inputValue: 'horserelay'},
				  ]
				}]
			}, {
			xtype: 'fieldset',
			id: 'fs_expense',
			layout: 'auto',
			collapsible: true,
			collapsed: true,
			title: 'Price options',
			autoHeight: true,
			items: [{
				xtype: 'radiogroup',
				id: 'rg_expense',
				fieldLabel: '',
				columns: 1,
				disabled: true,
				items: [
				  {boxLabel: 'Donkey/camel', name: 'rbvehicle', inputValue: 'donkey'},
				  {boxLabel: 'Wagon', name: 'rbvehicle', inputValue: 'wagon'},
				  {boxLabel: 'Passenger in carriage', name: 'rbvehicle', inputValue: 'carriage'} 
				  ]
				}]
			}] // end items
	};   
	// combine all that into one huge form
    // var fp = new Ext.FormPanel({
    fp = new Ext.FormPanel({
        border: false,
        labelWidth: 100,
        width: 290,	
        renderTo:'queryForm_full',
		monitorValid: true,
        // bodyStyle: 'padding:10px 5px;',
        items: [
            //routeGroup, // start, destination, month
			getSource = new Ext.form.ComboBox({
			store: o_sites_store, //direct array data
			typeAhead: false,
			editable: true,
			triggerAction: 'all',
			//hideTrigger: true,
			//emptyText:'From...',
			selectOnFocus:true,
			valueField: 'objectid',
			displayField:'prefname',
			fieldLabel: 'Start',
			mode: 'local',
			allowBlank: false,
			forceSelection: true,
			hiddenName: 's',
			blankText: 'We need a source location!'
		}),
		//var getTarget = new Ext.form.ComboBox({
		getTarget = new Ext.form.ComboBox({
			store: o_sites_store, //direct array data
			typeAhead: false,
			editable: true,
			triggerAction: 'all',
			//hideTrigger: true,
			//emptyText:'To...',
			selectOnFocus:true,
			valueField: 'objectid',
			displayField:'prefname',
			fieldLabel: 'Destination',
			mode: 'local',
			allowBlank: false,
			forceSelection: true,
			hiddenName: 't',
			blankText: 'We need a destination'
		}),
		//var getMon = new Ext.form.ComboBox({
		getMon = new Ext.form.ComboBox({
			store: months_store, //direct array data
			typeAhead: true,
			triggerAction: 'all',
			//emptyText:'Month...',
			selectOnFocus:true,
			valueField: 'id',
			displayField:'text',
			fieldLabel: 'Month of travel',
			allowBlank: false,
			mode: 'local',
			forceSelection: true,
			hiddenName: 'm',
			blankText: 'What month?'
		}),
			priorityGroup, // fastest, cheapest, shortest
			modeGroup, // land, river, sea, coastal (all checked by default)
			boatGroup, // fast or slow, sea and rivers
			vehicleGroup // 
			//costGroup
        ], // end fp items
    });
	getSource.setValue('50327');
	getTarget.setValue('50129');
	getMon.setValue('1');

/*if(fp.getForm().isValid()){
  Ext.Msg.alert('Submitted Values', 'The following will be sent to the server: <br />'+ 
	  fp.getForm().getValues(true).replace(/&/g,', '));
} */ 
});
// var months_store = new Ext.data.SimpleStore({
months_store = new Ext.data.SimpleStore({ //fiddle with scope
  //fields: [],
  data : [
	[1, 'January'],
	[2, 'February'],
	[3, 'March'],
	[4, 'April'],
	[5, 'May'],
	[6, 'June'],
	[7, 'July'],
	[8, 'August'],
	[9, 'September'],
	[10, 'October'],
	[11, 'November'],
	[12, 'December']
	],
	id: 0,
	fields: ['id', 'text']
});
// var o_sites_store = new Ext.data.ArrayStore({
o_sites_store = new Ext.data.ArrayStore({
  id: 0,
  idIndex: 0,
  data : Ext.orbisdata.o_sites,
  fields: [{name: 'objectid', type: 'integer'},'prefname','rank','isport']
});
