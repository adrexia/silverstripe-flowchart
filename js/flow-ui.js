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
			[ "Label", { label:$('#label-name').val(), id:"label", cssClass:"empty" }]
		]
	});

	//Handle update of label
	$('#label-name').on('change paste keyup',function(){
		var labelVal =$('#label-name').val();
		if(labelVal.length  >0 ){
			jsPlumb.Defaults.ConnectionOverlays = [
				[ "Arrow", {
						location:1,
						id:"arrow",
						length:8,
						width:8,
						foldback:0.8
					} ],
					[ "Label", { label:labelVal, id:"label", cssClass:"aLabel" }]
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
					[ "Label", { label:'', id:"label", cssClass:"empty" }]
				]
		}
	});

	//Helper function to change display on states that 
	//have been moved from original location
	$('.state.new-state').on('click', function(){
		$(this).removeClass('new-state');
	});


	var states = $('#container .state'),
		connect = $('<div>').addClass('connect');

	for(var i = 0; i < states.length; i = i + 1){
		var connect = $('<div>').addClass('connect');
		jsPlumb.makeTarget(states[i], {
			anchor: 'Continuous'
		});

		jsPlumb.makeSource(connect, {
			parent: states[i]
		});

		$(states[i]).append(connect);

		//Make new state draggable
		jsPlumb.draggable(states[i], {
			containment: 'parent' //Stay inside container
		});

		jsPlumb.bind("click", jsPlumb.detach);
	}
});