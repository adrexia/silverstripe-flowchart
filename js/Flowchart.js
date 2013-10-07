/*jslint browser: true, nomen: true */
/*global $, jsPlumb, jQuery*/


jsPlumb.ready(function($) {
	'use strict';

	//Avoid conflicts with jsPlumb
	$ = jQuery.noConflict();

	$.entwine('ss', function($){
		
		$('.flowchart-container').entwine({
			/*
			 * Set JSPlumb defaults settings
			 */
			jsPlumbDefaults: function(){
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
			},
			onmatch: function(){
				var self = this;
				this._super();
				self.loadFlowChart();

				if(this.closest('.flowchart-admin-wrap').length > 0){
					self.workspaceInit();
				}
			},
			
			onunmatch: function(){
				this._super();
			},

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
			/*
			 * Adjust overlays on a connection instance
			 * Currently only sets the label overlay
			 *
			 * @parem JSPlumbConnection connectionInstance, String label
			 */
			setOverlays: function(connectionInstance, label){
				var overlayLabel = connectionInstance.getOverlay("label");

				if(label === ""){
					overlayLabel.setLabel(label);
					overlayLabel.addClass("empty");
					overlayLabel.removeClass("aLabel");
				} else {
					overlayLabel.setLabel(label);
					overlayLabel.removeClass("empty");
					overlayLabel.addClass("aLabel");
				}
			},

			/**
			 * Set jsPlumb zoom property. Takes an integer value and zoom to that value
			 *
			 * NOTE: and unzoomed state is 1, 0.9 is zoomed out by 10%
			 * 
			 * @param Int
			 */
			setZoom: function(z) {
				var p = [ "-webkit-", "-moz-", "-ms-", "-o-", "" ],
					s = "scale(" + z + ")",
					i;

				for (i = 0; i < p.length; i = i+1){
					this.css(p[i] + "transform", s);
					this.css(p[i] + "transform-origin",  "0 0 0");
				}

				$('.flowchart-zoom').attr('data-zoom', z);

				jsPlumb.setZoom(z);
			},
			/*
			 * Set up special layout for flowchart construction interface
			 * TO DO: Put user config values here for workspace size (currently in css)
			 */
			layoutAdmin: function(){
				var contentFields = this.closest('.cms-content-fields'),
					height = contentFields.removeClass('auto-height').height();

				// Layout set-up
				this.find('.flowchart-wrap').height(height);
				this.find('.new-states').height(height);
				contentFields.addClass('auto-height');
			},
			/* 
			 * Initialise admin interface specifics:
			 *  * Call to setup layout
			 *  * jsPlumb events
			 *  * State events
			 */
			workspaceInit: function(){
				var states = this.find('.state'),
					connect,
					i,
					self = this;

				this.layoutAdmin();

				// State set-up
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
					jsPlumb.draggable(states[i], {});

					// If within workspace, contain
					if($(states[i]).closest('.workspace').length > 0){
						$(states[i]).draggable( "option", "containment", "parent" );
					}
				}

				// JS plumb event hooks  

				// Detach connections on click
				jsPlumb.bind("click", jsPlumb.detach);

				// Set overlays on new connections and save values to json
				jsPlumb.bind("connection", function(newConnection) {
					var label = $('#label-name').val();
					self.setOverlays(newConnection.connection, label);
					self.storeFlowChart();
				});

				// When Connection detached, update json
				jsPlumb.bind("connectionDetached", function(newConnection) {
					self.storeFlowChart(newConnection);
				});
			},

		
			/*
			 * Responsible for updating the JSON object that will be sent to the 
			 * db on save. Constructs arrays from state positions and label info.
			 *
			 * Filters states by those within the workspace. 
			 *
			 */
			storeFlowChart: function(){
				var saveArray = {states: [], connections: []},
					states = $('.state'),
					connections = jsPlumb.getConnections(),
					state = {},	connection = {},
					i = 0, j = 0;

				//convert each state position into array, with id.
				//push into saveArray.states
				for(j = 0; j < states.length; j = j + 1){
					state = $(states[j]);

					if($(states[j]).closest('.workspace').length > 0){
						saveArray.states.push({
							id: state.attr('id'),
							x: state.position().left,
							y: state.position().top
						});
					}
				}

				//convert important connection values into array
				//push into saveArray.connections
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
			/*
			 * Responsible for updating the flowchart state objects with previously saved JSON data
			 *
			 * Moves and positions states found in JSON
			 * Calls methods to apply connections and initializes events if needed
			 */
			loadFlowChart: function(){
				var self = this,
					savedFlow = this.getChartData(),
					states = this.find('.state'),
					state, connection, newConnection,
					i = 0,
					id = 0, x = 0, y = 0,
					from = '', to = '', label = '',
					height = 0, h;

				//If this is a new chart, quit out of this function
				if(savedFlow === null){
					return false;
				}

				//Apply jsplumb defaults
				this.jsPlumbDefaults();

				//Turns off jsPlumb listeners
				jsPlumb.unbind();

				//Cleans up the endpoints so jsPlumb won't know about them
				states.each(function () {
					jsPlumb.removeAllEndpoints($(this));
				});

				//Set state positions
				for(i in savedFlow.states){
					if(savedFlow.states.hasOwnProperty(i)){
						state = savedFlow.states[i];
						id = state.id;
						x = state.x;
						y = state.y;
						h = 0;

						if(self.closest('.flowchart-admin-wrap').length > 0){
							//in create mode, move to workspace if exists in json
							$('#'+id).appendTo($('.flowchart-admin-wrap .workspace'));
						}

						// Position states
						$('#'+id).css({left: x, top:y}).removeClass('new-state');

						// Calculate height needed in view mode
						if(this.closest('.flowchart-view').length > 0){
							if($('#'+id).length > 0){
								h = $('#'+id).outerHeight();
								if(h + y > height){
									height = h + y;
								}
							}
						}
					}
				}
				
				// set height needed in view mode
				if(this.closest('.flowchart-view').length > 0){
					$('.flowchart-view ').height(height + 100); //lowest flowstate plus 100px padding
				}

				//Reconnect flowchart. Miust check that both end points still exist
				for(i in savedFlow.connections){
					if(savedFlow.connections.hasOwnProperty(i)){
						connection = savedFlow.connections[i];
						from = connection.from;
						to = connection.to;
						label = connection.label;

						if($('#'+from).length > 0 && $('#'+to).length > 0){
							newConnection = jsPlumb.connect({ source:to, target:from });
							this.setOverlays(newConnection, label);
						}
					}
				}
			}
		});

		/*
		 * Make workspace droppable
		 */
		$('.flowchart-admin-wrap .workspace').entwine({
			onmatch: function(){
				this.droppable({
					accept: ".state"
				});	

				this.on("drop", function(e, ui) {
					if($(ui.draggable).hasClass('new-state')){
						var scroll = $('.cms .flowchart-wrap').scrollTop();
						//Move object to the workspace
						ui.draggable.appendTo($('.flowchart-admin-wrap .workspace')).removeClass('new-state');
						
						//Reset draggable parent to new container
						$(ui.draggable).draggable( "option", "containment", "parent" );

						//Account for scrolled workspace
						$(ui.draggable).css({'top':scroll + e.clientY, 'right':0});
					}
				});
			}
		});

		/*
		 * Attach special event handlers to states within the construction view
		 */
		$('.flowchart-admin-wrap .state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on('dragstop', function(){
					self.closest('.flowchart-container').storeFlowChart();
				});
				this.dblclick(function(e) {
					e.stopPropagation();

					//Tell jsPlumb that the connections are gone
					jsPlumb.detachAllConnections($(this));

					//Move object back into new states panel, reset styles, and apply new-state class
					$(this).appendTo(self.closest('.flowchart-admin-wrap').find('.new-states'));
					$(this).attr('style','').addClass('new-state');

					//Shift containment to the wider container, so item can be dragged into workspace
					$(this).draggable( "option", "containment", ".flowchart-container");
					
					//Update model
					self.closest('.flowchart-container').storeFlowChart();
				});
			},
			onunmatch: function(){
				this._super();
			}
		});

		//Helper function to set specific options on new state draggables
		$('.flowchart-admin-wrap .state.new-state').entwine({
			onmatch: function(){
				var self = this;
				this._super();
				this.on( "dragcreate", function(){
					self.draggable( "option", "revert", "invalid" );
					self.draggable( "option", "containment", "body" );
				});
			},
			onunmatch: function(){
				this._super();
			}
		});


		// Zoom event handler
		$('.flowchart-zoom a').entwine({
			onclick: function(){
				var currentZoom = parseFloat(this.closest('.flowchart-zoom').attr('data-zoom')),
				container = $('.flowchart-container');
				if(this.hasClass('zoom-in')){
					container.setZoom(currentZoom + 0.1);
				}else if(this.hasClass('zoom-out')){
					container.setZoom(currentZoom - 0.1);
				}
				return false;
			}
		});

		// This is a hack to apply layout on redraw. 
		// TODO: Make this more elegant
		$('.cms .cms-container').entwine({
			redraw: function(){
				this._super();
				var scope = $('.flowchart-admin-wrap .flowchart-container');
				if(scope.length > 0){
					scope.layoutAdmin();
				}
			}
		});

		/**
		 * Enable save buttons upon detecting changes to content.
		 * "changed" class is added by jQuery.changetracker.
		 */
		$('.cms-edit-form.changed').entwine({
			onmatch: function(e) {
				this.find('button[name=action_doSave]').button('option', 'showingAlternate', true).addClass('ss-ui-action-constructive');
				this.find('button[name=action_publish]').button('option', 'showingAlternate', true).addClass('ss-ui-action-constructive');
				this._super(e);
			},
			onunmatch: function(e) {
				var saveButton = this.find('button[name=action_doSave]'),
					publishButton = this.find('button[name=action_publish]');
				if(saveButton.data('button')) {
					saveButton('option', 'showingAlternate', false).removeClass('ss-ui-action-constructive');
				}
				if(publishButton.data('button')) {
					publishButton('option', 'showingAlternate', false).removeClass('ss-ui-action-constructive');
				}
				this._super(e);
			}
		});

	});
});