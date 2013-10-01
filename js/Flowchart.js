/*jslint browser: true*/
/*global $, jsPlumb, jQuery*/

jsPlumb.ready(function($) {
	'use strict';

	//Avoid conflicts with jsPlumb
	$ = jQuery.noConflict();

	$.entwine('ss', function($){

		$('.flowchart-container').entwine({

			onmatch: function(){
				var self = this;
				this._super();
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
						[ "Label", { label:$('#label-name').val(), id:"label", cssClass:"empty", location:0.5 }]
					]
				});
				self.flowInit();
			},
			onunmatch: function(){
				this._super();
			},
			flowInit: function(){
				var states = this.find('.state'),
					connect,
					i;

				for(i = 0; i < states.length; i = i + 1){
					connect = $('<div>').addClass('connect');
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

					this.bindFlowEvents();
				}

			},
			makeConnection: function(newConnection){
				var label = $('#label-name').val(),
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
				this.storeFlowChart();
			},
			bindFlowEvents: function(){
				var self = this;

				jsPlumb.bind("click", jsPlumb.detach);

				jsPlumb.bind("connection", function(newConnection) {
					self.makeConnection(newConnection);
				});

				jsPlumb.bind("connectionDetached", function(newConnection) {
					self.storeFlowChart(newConnection);
				});

			},
			storeFlowChart: function(){
				var saveArray = {states: [], connections: []},
					states = $('.state'),
					connections = jsPlumb.getConnections(),
					state = {},	connection = {},
					i = 0, j = 0, 
					output;

				//For each state convert to array and push into States
				for(j = 0; j < states.length; j = j + 1){
					state = $(states[j]);
					saveArray.states.push({
						id: state.attr('id'),
						x: state.position().left,
						y: state.position().top
					});
				}

				//For each connection convert to array and push into Connections
				for(i in connections){
					if(connections.hasOwnProperty(i)){
						connection = connections[i];
						saveArray.connections.push({
							from: connection.targetId,
							to: connection.sourceId,
							label: connection.getOverlay("label").getLabel()
						});
					}
				}

				//Turn array into JSON
				output = JSON.stringify(saveArray);

				// Needs to save this in silverstripe if save is pressed, 
				// but for now, just store the value for reload
				$('#flow-chart-store').val(output);
			},
			loadFlowChart: function(){
				var savedFlow = $.parseJSON($('#flow-chart-store').val()),
					states = this.find('.state'),
					state, connection, newConnection,
					i = 0,
					id = 0, x = 0, y = 0,
					from = '', to = '', label = '';

				//Cleans up the endpoints
				states.each(function () {
					jsPlumb.removeAllEndpoints($(this));
				});

				//Turns off jsPlumb listeners
				jsPlumb.unbind();

				//Set state positions
				for(i in savedFlow.states){
					if(savedFlow.states.hasOwnProperty(i)){
						state = savedFlow.states[i];
						id = state.id;
						x = state.x;
						y = state.y;

						$('#'+id).css({left: x, top:y});
					}
				}

				//Reconnect flowchart
				for(i in savedFlow.connections){
					if(savedFlow.connections.hasOwnProperty(i)){
						connection = savedFlow.connections[i];
						from = connection.from;
						to = connection.to;
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
						this.bindFlowEvents();
					}
				}
			}
		});

		$('#flow-chart-save').entwine({
			onclick: function(){
				$('.flowchart-container').storeFlowChart();
			}
		});

		$('#flow-chart-load').entwine({
			onclick: function(){
				$('.flowchart-container').loadFlowChart();
				$('.flowchart-container').storeFlowChart(); //so the store is kept up to date
			}
		});

		$('.flowchart-container .state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on('drag', function(){
					self.closest('.flowchart-container').storeFlowChart();
				});
			},
			onunmatch: function(){
				this._super();
			},
		});

		//Helper function to change display on states that 
		//have been moved from original location
		$('.state.new-state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on('drag', function(){
					$(self).removeClass('new-state');
				});
			},
			onunmatch: function(){
				this._super();
			},
			onclick: function(){
				$(this).removeClass('new-state');
			}
		});
	});
});