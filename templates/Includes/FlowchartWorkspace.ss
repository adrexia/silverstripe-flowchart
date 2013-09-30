<div class="flowchart-admin-wrap cms-content center cms-tabset FlowChartAdmin LeftAndMain ui-tabs ui-widget ui-widget-content ui-corner-all">
	<fieldset class="flowchart-toolbar">
		<label for="label-name">Connection Label</label>
		<input id="label-name" name="labelName" value="" class="text" aria-described-by="flowchart-admin-use" />
		<em id="flowchart-admin-use" class="flowchart-em extra-label">
			(e.g "Yes", "No", "Accepted")</em>

		<input type="text" name="flow-chart-store" id="flow-chart-store" val="$FlowchartData" />

		<div class="actions">

			<button id="flow-chart-save">Save</button>
			<button id="flow-chart-load">Load</button>
		</div>
	</fieldset>


	<div class="new-states">
		<h2>New States</h2>
		<em class="flowchart-em">(Drag and drop into your workspace)</em>
		<div class="drag-area"></div>
	</div>
	<div id="container" class="flowchart-container">
		<h1>Workspace</h1>

		<% loop FlowStates.Reverse %>
		<div id="id_{$ID}" data-id="$ID" class="state columns new-state <% if $Size %>$Size<% else %>two<% end_if %>">
			<div class="num">$Number</div>
			<div class="drag-content">
				$Description
			</div>
		</div>
		<% end_loop %>
	</div>
</div>