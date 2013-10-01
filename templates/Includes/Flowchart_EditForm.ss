<form id="Form_ItemEditForm" action="$Link(ItemEditForm)" method="POST" class="cms-content cms-edit-form center cms-tabset flowchart-admin-wrap" data-pjax-fragment="CurrentForm Content" data-layout-type="border">
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
	<div class="cms-content-fields center">
		<fieldset>
			<input type="hidden" name="flow-chart-store" id="flow-chart-store" value='$FlowchartData' />
			<input type='hidden' value='$SecurityID' name="SecurityID">	

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
		</fieldset>
	</div>
	<div class="cms-content-actions cms-content-controls south flowchart-toolbar">
		<div class="Actions">
			<button id="flow-chart-save" name='action_doSave' value="save" class="action ss-ui-action-constructive ss-ui-button" data-icon="accept" role="button">Save</button>
			<button id="flow-chart-load">Load</button>
			<label for="label-name">Connection Label</label>
			<input id="label-name" name="labelName" value="" class="text" aria-described-by="flowchart-admin-use" />
			<em id="flowchart-admin-use" class="flowchart-em extra-label">(e.g "Yes", "No", "Accepted")</em>
		</div>
	</div>
</form>