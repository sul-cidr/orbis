Ext.onReady(function() {
	
	getHashValue = function() {
var arr = window.location.hash.split("#");
// var hasValue = arr[1];
  //sets default
  if (typeof arr == "undefined") {
		return false;
  }
  var hashLen = arr.indexOf("?");
  if(hashLen>0){
		//hasValue = hasValue.substring(0,hashLen);
		arg1 = arr.substring(0,hashLen);
		if(hashLen = 2){
			arg2 = arr.substring(0,hashLen);}
  }
  return arr;
}

// popup grid that CAN be copy/pasted
gridPrintWin = function (){
// routeStore.data.items[0].data.destination
	// alert('well you got as far as gridPrintWin()')
	gridhtml = new Ext.Window({
		border: false,
		title: 'Select, copy and paste',
		closeAction: 'hide',
		width: 500,
		autoHeight: true,
		//height: 100,
		bodyBorder: false,
		html: printStore(routeStore, '')
	});
	gridhtml.show(this).alignTo(document.getElementById('south'), "bl-c")
};

// trying history
/**
* Creates the necessary DOM elements required for Ext.History to manage state
* Sets up a listener on Ext.History's change event to fire this.handleHistoryChange
*/
initialiseHistory = function() {
  this.historyForm = Ext.getBody().createChild({
    tag:    'form',
    action: '#',
    cls:    'x-hidden',
    id:     'history-form',
    children: [
      {
        tag: 'div',
        children: [
          {
            tag:  'input',
            id:   Ext.History.fieldId,
            type: 'hidden'
          },
          {
            tag:  'iframe',
            id:   Ext.History.iframeId
          }
        ]
      }
    ]
  });

  //initialize History management
  Ext.History.init();
  Ext.History.on('change', this.handleHistoryChange, this);
}
loadContent = function(div,content){
	Ext.get(div).load(content);
	};
 // generic tab getter
goTab = function(tabnum){
	// alert('you want tab #'+ tabnum +', eh?');
	Ext.getCmp('centerPanel').setActiveTab(tabnum);
}

goSubtab = function(parent,tabnum){
	// alert('you want tab #'+ tabnum +', eh?');
	Ext.getCmp(parent).setActiveTab(tabnum);
}
goRemoteTab = function(parentnum,parentname,tabnum){
	// alert('you want tab #'+ tabnum +', eh?');
	Ext.getCmp('centerPanel').setActiveTab(parentnum);
	Ext.getCmp(parentname).setActiveTab(tabnum);
}

helpWin = function(cat, element) { // , where
	help = new Ext.Window({
		border: false,
		title: 'Help',
		closeAction: 'hide',
		width: 300,
		autoHeight: true,
		//height: 100,
		bodyBorder: false,
		contentEl: 'helptext_'+cat,
	});
	help.show(this).alignTo(document.getElementById(element), "bl-c")
	//help.show(this)..alignTo(Ext.getBody(), "b-b", [0, 0]); //[0,0]b?", [600, 300]); [100, -175]
	//cite.show(this).setPosition(10,10); //[0,0]
};
citeWin = function() {
	o.dt = new Date();
	o.dttext = o.dt.format('d M, Y');  
	cite = new Ext.Window({
		border: false,
		title: 'To cite ORBIS:',
		closeAction: 'hide',
		width: 300,
		height: 100,
		bodyBorder: false,
		html: '<p>Scheidel, W. and Meeks, E. (May 2, 2012). <span class="italic">' +
			'ORBIS: The Stanford Geospatial Network Model of the Roman World</span>. Retrieved ' + 
			o.dttext +', from http://orbis.stanford.edu.</p>'
	});
	cite.show(this).alignTo('homebot', "bl-bl", [10, -80])
	//cite.show(this).setPosition(10,10); //[0,0]
};
	 
picWin = function (pic, cap){
	// create the window each time
	modalWin = new Ext.Window({
		// applyTo:'result-win',
		modal: true,
		border: false,
		autoHeight: true,
		title: document.getElementById(cap).innerHTML,
		closeAction: 'close',
		// width: w,
		// height: h,
		//width: window.innerWidth - 60,
		//height: window.innerHeight - 60,
		bodyBorder: false,
		html: '<img src="figures/'+pic+'" />'
	});
	// modalWin.show(this).alignTo(Ext.getBody(), "c-c?"); //[0,0]
	modalWin.show(this).setPosition(10,10); //[0,0]
}

// var bottomPosition = document.height();
// wires Introduction panel links to setActiveTab
  Ext.get('link_mapping').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(5);
	Ext.getCmp('eastPanel').expand();
  });
  Ext.get('homemap').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(5);
	Ext.getCmp('eastPanel').expand();
  });
  Ext.get('link_building').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(3);
  });
  Ext.get('link_understanding').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(2)
  });
  Ext.get('link_using').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(4);
  });
  Ext.get('link_introducing').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(1);
  });
  Ext.get('link_applying').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(6);
  });
  Ext.get('north').on('click', function(){
	Ext.getCmp('centerPanel').setActiveTab(0);
  });

});

