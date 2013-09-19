//
// this script is used to dynamically insert links from each demo to its previous and next,
// as well as write the drop down.  
//
;(function() {

	var libraries = [
		{id:"jquery", name:"jQuery"},
		{id:"mootools", name:"MooTools"},
		{id:"yui", name:"YUI3"}
	],
	prepareOtherLibraryString = function(demoId, library) {
		var s = "", demoInfo = jsPlumb.DemoList.find(demoId);
		for (var i = 0; i < libraries.length; i++) {
			var c = libraries[i].id == library ? "selected" : "";
			s += '<a class="' + c + '" href="' + libraries[i].id + '.html" title="Use ' + libraries[i].name + ' as the support library">' + libraries[i].name + '</a>&nbsp;';
		}
		return s;
	};
	
	
	jsPlumb.DemoList = {
		find:function(id) {
			for (var i = 0; i < entries.length; i++) {
				if (entries[i].id === id) {
					var next = i < entries.length - 1 ? i + 1 : 0,
						prev = i > 0 ? i - 1 : entries.length - 1;
						
					return {
						current:entries[i],
						prev:entries[prev],
						next:entries[next],
						idx:i
					};
				}
			}
		},
		init : function() {
			var bod = document.body,
				typeId = bod.getAttribute("data-id"),
				library = bod.getAttribute("data-library"),
				libraryString = prepareOtherLibraryString(typeId, library),
				info = jsPlumb.DemoList.find(typeId);
		}
	};

	window.jsPlumbDemo.loadTest = function(count) {
		count = count || 10;
		for (var i = 0; i < count; i++) {
			jsPlumb.deleteEveryEndpoint();
			jsPlumbDemo.init();
		}
	};
})();