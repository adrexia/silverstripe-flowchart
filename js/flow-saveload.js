jsPlumb.ready(function() {


	/*
	*   Save the current state 
	*/
	function saveFlowChart() {
		//initialize the string

		var saveArray = {states: [], connections: []};

			var states = $('.state'),
				connections = [];
				state = {},
				i = 0,
				id = 0,
				x = 0,
				y = 0,
				jsonify = {};

			//For each state convert to JSON and push into States
			for(i = 0; i < states.length; i++){
				state = $(states[i]);
				id = parseInt(state.attr('data-id'),10);
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
			connections = $.map(jsPlumb.getConnections(), function(k, v) {
				return [k];
			});

			saveArray.connections.push(
				connections
			);

			console.log(saveArray);





	}
	$('#save').on('click', function(){
		saveFlowChart();
	});

});

