jsPlumb.ready(function() {
	// jsPlumb.makeSource($('.item'), {
	// 	connector: 'StateMachine'
	// });
	// jsPlumb.makeTarget($('.item'), {
	// 	anchor: 'Continuous'
	// });

	var i = 0; //Track number of states

	$('#container').dblclick(function(e){
		var newState = $('<div>').attr('id', 'state' + i).addClass('item')
			title = $('<div>').addClass('title').text('State' + i),
			connect = $('<div>').addClass('connect');

			newState.css({
				'top': e.pageY,
				'left': e.pageX
			});

			jsPlumb.importDefaults({
				Endpoint : ["Dot", {radius:1}],
				EndpointStyle : {strokeStyle:'#666' },
				PaintStyle: {strokeStyle:"#9bc10d", lineWidth:1 },
				HoverPaintStyle : {strokeStyle:"#fff600", lineWidth:1 },
				Connector : "StateMachine",
				RenderMode : "html",
				Anchors : ["TopCenter", "TopCenter"],
				ConnectionOverlays : [
					[ "Arrow", { 
						location:1,
						id:"arrow",
						length:14,
						foldback:0.8
					} ],
					[ "Label", { label:"Yes", id:"label", cssClass:"aLabel" }]
				]
			});

			jsPlumb.makeTarget(newState, {
				anchor: 'Continuous'
			});

			jsPlumb.makeSource(connect, {
				parent: newState,
				anchor: 'Continuous'
			});

			newState.append(title);
			newState.append(connect);

			$('#container').append(newState);

			//Make new state draggable
			jsPlumb.draggable(newState, {
				containment: 'parent' //Stay inside container
			});

			//Removal function
			newState.dblclick(function(e){
				jsPlumb.detachAllConnections($(this)); //detach all connections 
				$(this).remove(); //Remove state
				e.stopPropagation(); //Stop other event handlers from firing (eg this parent)
			});

			i = i + 1;
	});
});