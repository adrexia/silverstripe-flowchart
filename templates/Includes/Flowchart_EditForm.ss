<div id="flowchart-cms-content" class="flowchart-admin-wrap cms-content center cms-tabset FlowChartAdmin LeftAndMain center ui-tabs ui-widget ui-widget-content ui-corner-all" data-layout-type="border" data-pjax-fragment="Content">
	<% with $ItemEditForm %>
	<div class="cms-content-header north">
		<div class="cms-content-header-info">
			<% include BackLink_Button %>
			<% with $Controller %>
				<% include CMSBreadcrumbs %>
			<% end_with %>
		</div>
	</div>
	<% end_with %>
	<form action="$Link(ItemEditForm)" $FormAttributes method="POST" class="cms-content-fields center ui-widget-content cms-panel-padded">
		<fieldset class="flowchart-toolbar">
			<label for="label-name">Connection Label</label>
			<input id="label-name" name="labelName" value="" class="text" aria-described-by="flowchart-admin-use" />
			<em id="flowchart-admin-use" class="flowchart-em extra-label">(e.g "Yes", "No", "Accepted")</em>
			<input type="hidden" name="flow-chart-store" id="flow-chart-store" value='$FlowchartData' />
			<input type='hidden' value='$SecurityID' name="SecurityID">	
	
			<button id="flow-chart-load">Load</button>
			<div class="new-states">
				<h2>New States</h2>
				<em class="flowchart-em">(Drag and drop into your workspace)</em>
				<div class="drag-area"></div>
			</div>
			<div id="container" class="flowchart-container">

				<h1>Workspace</h1>

				<% loop FlowStates.Reverse %>
				<div id="id_{$ID}" data-id="$ID" class="state columns new-state <% if $Size %>$Size<% else %>two<% end_if %>">
			
						<div class="num">
							<span>$Number</span>
						</div>
						<div class="drag-content">
							$Description
						</div>
			
				</div>
				<% end_loop %>
			</div>

			<div class="flowchart">
				<div class="new-states">
					<h2>New States</h2>
					<em class="flowchart-em">(Drag and drop into your workspace)</em>
					<div class="drag-area"></div>
				</div>
			</div>
		</fieldset>

		<div class="cms-content-actions cms-content-controls south">
			<div class="Actions">
				<button id="flow-chart-save" name='action_doSave'>Save</button>
			</div>
		</div>
	</form>
</div>