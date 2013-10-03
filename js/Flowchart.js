/*jslint browser: true*/
/*global $, jsPlumb, jQuery*/


jsPlumb.ready(function($) {
	'use strict';

	//Avoid conflicts with jsPlumb
	$ = jQuery.noConflict();

	$.entwine('ss', function($){
		
		/**
		 * Enable save buttons upon detecting changes to content.
		 * "changed" class is added by jQuery.changetracker.
		 */
		$('.cms-edit-form.changed').entwine({
			onmatch: function(e) {
				this.find('button[name=action_doSave]').button('option', 'showingAlternate', true);
				this.find('button[name=action_publish]').button('option', 'showingAlternate', true);
				this._super(e);
			},
			onunmatch: function(e) {
				var saveButton = this.find('button[name=action_doSave]');
				if(saveButton.data('button')) saveButton('option', 'showingAlternate', false);
				var publishButton = this.find('button[name=action_publish]');
				if(publishButton.data('button')) publishButton('option', 'showingAlternate', false);
				this._super(e);
			}
		});

		$('.flowchart-container').entwine({
			
			/**
			 * Get the chart data from an input field, return a deserialized
			 * json ready for usage.
			 * 
			 * @returns Object
			 */
			getChartData: function() {
				var val = $('input[data-chart-storage=true]')
					.filter(':first')
					.val();
				return $.parseJSON(val);
			},
			
			/**
			 * Save the object passed in as a JSON serialized value in an input
			 * field.
			 * 
			 * @param Object value
			 * @returns self
			 */
			setChartData: function(value) {
				$('input[data-chart-storage=true]')
					.filter(':first')
					.val(JSON.stringify(value));
				$('input[data-chart-storage=true]').trigger('change');
				return this;
			},

			setZoom: function(z) {
				var p = [ "-webkit-", "-moz-", "-ms-", "-o-", "" ],
					s = "scale(" + z + ")",
					i;

				for (i = 0; i < p.length; i = i+1){
					this.css(p[i] + "transform", s);
					this.css(p[i] + "transform-origin",  "0 0 0");
				}

				this.parent().find('.zoom').attr('data-zoom', z);

				jsPlumb.setZoom(z);
			},
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
					Anchor : 'Continuous',
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
				self.loadFlowChart();

				if(this.closest('.flowchart-admin-wrap').length > 0){
					self.flowInit();
				}
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
						parent: states[i],
						anchor: 'Continuous'
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
			/*
			 * Helper method to determine if state is within bounds
			 */
			boundingBox: function(state, workspace){
					var x1 = workspace.position().left,
						x2 = workspace.width() + x1,
						y1 = workspace.position().top,
						y2 = workspace.height() + y1,
						sx = state.position().left,
						sy = state.position().top;

					return sx > x1 && sx < x2 && sy > y1 && sy < y2;
			},
			storeFlowChart: function(){
				var saveArray = {states: [], connections: []},
					states = $('.state'),
					connections = jsPlumb.getConnections(),
					state = {},	connection = {},
					i = 0, j = 0, 
					workspace = this.find('.workspace');

				//For each state convert to array and push into States
				for(j = 0; j < states.length; j = j + 1){
					state = $(states[j]);

					if(this.boundingBox(state, workspace)){
						saveArray.states.push({
							id: state.attr('id'),
							x: state.position().left,
							y: state.position().top
						});
					}
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
				//Save the data
				this.setChartData(saveArray);
			},
			loadFlowChart: function(){
				if(this.getChartData() === null){
					return false;
				}
				
				var savedFlow = this.getChartData(),
					states = this.find('.state'),
					state, connection, newConnection,
					i = 0,
					id = 0, x = 0, y = 0,
					from = '', to = '', label = '',
					height = 0, h;

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
						h = 0;

						$('#'+id).css({left: x, top:y}).removeClass('new-state');

						if(this.closest('.flow-chart-view').length > 0){
							if($('#'+id).length > 0){
								h = $('#'+id).outerHeight();
								if(h + y > height){
									height = h + y;
								}
							}
						}

					}
				}
				
				if(this.closest('.flow-chart-view').length > 0){
					$('.flow-chart-view').height(height + 100); //height plus 100px padding
				}

				//Reconnect flowchart
				for(i in savedFlow.connections){
					if(savedFlow.connections.hasOwnProperty(i)){
						connection = savedFlow.connections[i];
						from = connection.from;
						to = connection.to;
						label = connection.label;

						if($('#'+from).length > 0 && $('#'+to).length > 0){
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
							if(this.closest('.flowchart-admin-wrap').length > 0){
								this.bindFlowEvents();
							}
						}
					}
				}
			},
		});

		$('.flowchart-admin-wrap .flowchart-container .state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on('drag', function(){
					self.closest('.flowchart-container').storeFlowChart();
				});
				this.dblclick(function(e) {
					jsPlumb.detachAllConnections($(this));
					$(this).attr('style','').addClass('new-state').css({'right':'45px', 'top':'146px'});
					self.closest('.flowchart-container').storeFlowChart();
					e.stopPropagation();
				});
			},
			onunmatch: function(){
				this._super();
			}
		});

		//Helper function to change display on states that 
		//have been moved from original location
		$('.flowchart-admin-wrap .state.new-state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on('mousedown', function(e){
					if(self.hasClass('new-state')){
						var scroll = $('.cms .cms-content-fields').scrollTop();
						$(self).removeClass('new-state').css({'top':scroll + e.clientY, 'right':0});
					}
				});
			},
			onunmatch: function(){
				this._super();
			}
		});

		$('.flow-chart-view .zoom a').entwine({
			onclick: function(){
				var currentZoom = parseFloat(this.closest('.zoom').attr('data-zoom')),
				container = $('.flowchart-container');
				if(this.hasClass('zoom-in')){
					container.setZoom(currentZoom + 0.1);
				}else if(this.hasClass('zoom-out')){
					container.setZoom(currentZoom - 0.1);
				}
				return false;
			}
		});
	});
});