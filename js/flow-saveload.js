function bindFlowEvents(){
	//Bind events
	jsPlumb.bind("click", jsPlumb.detach);

	jsPlumb.bind("connection", function(newConnection) {
		var label =$('#label-name').val(),
		overlayLabel = newConnection.connection.getOverlay("label");

		if(label === ""){
			overlayLabel.setLabel(label);
			overlayLabel.addClass("empty");
			overlayLabel.removeClass("aLabel");
		}else{
			overlayLabel.setLabel(label);
			overlayLabel.removeClass("empty");
			overlayLabel.addClass("aLabel");
		}
		storeFlowChart();
	});

	jsPlumb.bind("connectionDetached", function(newConnection) {
		storeFlowChart();
	});
}


/*
*   Save the current state 
*/
function storeFlowChart() {
	//initialize the string

	var saveArray = {states: [], connections: []},
		states = $('.state'),
		connections = [],
		state = {},
		connection = {},
		i = 0,
		j = 0,
		id = 0,
		x = 0,
		y = 0,
		from = '',
		to = '',
		label = '',
		output;

	//For each state convert to JSON and push into States
	for(j = 0; j < states.length; j = j + 1){
		state = $(states[j]);
		id = state.attr('id');
		x = state.offset().left;
		y = state.offset().top;
		saveArray.states.push(
			{
				id: id,
				x: x,
				y: y
			}
		);
	}

	connections = jsPlumb.getConnections();

	for(i in connections){
		connection = connections[i];
		from = connection.targetId;
		to = connection.sourceId;
		label = connection.getOverlay("label").getLabel();
		saveArray.connections.push(
			{
				from: from,
				to: to,
				label: label
			}
		);
	}
	output = JSON.stringify(saveArray);

	// Needs to save this in silverstripe if save is pressed, 
	// but for now, just store the value for reload

	$('#flow-chart-store').val(output);
}

/*
*   Save the current state 
*/
function loadFlowChart() {

	var savedFlow = $.parseJSON($('#flow-chart-store').val()),
		states = $('.flowchart-container .state'),
		state,
		connection,
		i = 0,
		j = 0,
		id = 0,
		x = 0,
		y = 0,
		from = '',
		to = '',
		label = '',
		newConnection;

	//Cleans up the endpoints
	states.each(function () {
		jsPlumb.removeAllEndpoints($(this));
	});

	//Turns off jsPlumb listeners
	jsPlumb.unbind();

	//Set state positions
	for(i in savedFlow.states){
		state = savedFlow.states[i];
		id = state.id;
		x = state.x;
		y = state.y;

		$('#'+id).css({left: x, top:y});
	}

	//Reconnect flowchart
	for(i in savedFlow.connections){
		connection = savedFlow.connections[i];
		from = connection.from,
		to = connection.to,
		label = connection.label;

		newConnection = jsPlumb.connect({ source:to, target:from });
		if(label === ""){
			newConnection.getOverlay("label").setLabel(label);
			newConnection.getOverlay("label").addClass("empty");
			newConnection.getOverlay("label").removeClass("aLabel");
		}else{
			newConnection.getOverlay("label").setLabel(label);
			newConnection.getOverlay("label").removeClass("empty");
			newConnection.getOverlay("label").addClass("aLabel");
		}
		bindFlowEvents();
	}
}