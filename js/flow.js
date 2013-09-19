jsPlumb.ready(function() {

	jsPlumb.importDefaults({
		Endpoint : ["Dot", {radius:1}],
		EndpointStyle : {strokeStyle:'transparent' },
		PaintStyle: {strokeStyle:"#0C768E", lineWidth:1 },
		HoverPaintStyle : {strokeStyle:"#E26F1E", lineWidth:1 },
		Connector :[ "Flowchart", { cornerRadius:0 } ],
		RenderMode : "svg",
		Anchors : ["BottomCenter", "TopCenter"],
		ConnectionOverlays : [
			[ "Arrow", {
				location:1,
				id:"arrow",
				length:8,
				width:8,
				foldback:0.8
			} ],
			[ "Label", { label:makeLabel(), id:"label", cssClass:"empty" }]
		]
	});

	$('#label-name').on('change paste keyup',function(){
		if($('#label-name').val().length  >0 ){
			jsPlumb.Defaults.ConnectionOverlays = [
				[ "Arrow", {
						location:1,
						id:"arrow",
						length:8,
						width:8,
						foldback:0.8
					} ],
					[ "Label", { label:makeLabel(), id:"label", cssClass:"aLabel" }]
				]
		} else {
			jsPlumb.Defaults.ConnectionOverlays = [
				[ "Arrow", {
						location:1,
						id:"arrow",
						length:8,
						width:8,
						foldback:0.8
					} ],
					[ "Label", { label:makeLabel(), id:"label", cssClass:"empty" }]
				]
		}
	});



	//run through for loop

	function makeLabel(){
		//return input
		return $('#label-name').val();
	}

	var items = $('#container .item');
	var connect = $('<div>').addClass('connect');

	for(var i = 0; i < items.length; i = i + 1){
		var connect = $('<div>').addClass('connect');
		jsPlumb.makeTarget(items[i], {
			anchor: 'Continuous'
		});

		jsPlumb.makeSource(connect, {
			parent: items[i]
		});

		$(items[i]).append(connect);

		//Make new state draggable
		jsPlumb.draggable(items[i], {
			containment: 'parent' //Stay inside container
		});

		jsPlumb.bind("click", jsPlumb.detach);
	}
});