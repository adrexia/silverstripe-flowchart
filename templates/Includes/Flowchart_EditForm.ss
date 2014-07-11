<form id="Form_ItemEditForm" action="$FormAction" method="POST" class="cms-content cms-edit-form center cms-tabset flowchart-admin-wrap" data-pjax-fragment="CurrentForm Content" data-layout-type="border">
	<div class="cms-content-header north">
		<div class="cms-content-header-info">
			<% include BackLink_Button %>
			<% with $Controller %>
				<% include CMSBreadcrumbs %>
			<% end_with %>
		</div>
	</div>
	
	<div class="cms-content-fields center">
		<% if $Message %>
		<p id="{$FormName}_error" class="message $MessageType">$Message</p>
		<% else %>
		<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
		<% end_if %>
		<fieldset>
			<% loop $Fields %>
				$FieldHolder
			<% end_loop %>
			<div id="container" class="flowchart-container">
			<div class="new-states cms-content-tools">
				<h3 class="cms-panel-header">Tools</h3>
				<fieldset class="field label-name-field">
					<label for="label-name" class="left">Connection Label</label>
					
					<input id="label-name" name="labelName" value="" class="text" aria-described-by="flowchart-admin-use" />
					<em id="flowchart-admin-use" class="flowchart-em extra-label">(e.g "Yes", "No", "Accepted")</em>
				</fieldset>
				<h3 class="cms-panel-header states-heading">
					New States 
					<a href="admin/flowcharts/FlowState/EditForm/field/FlowState/item/new" class="action flowchart-add-state ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all new new-link" data-parent-id="$Record.ID" role="button" data-popupclass="flowchart-state-popup" aria-disabled="false" data-controller-url="$Controller.GetAllStatesLink()">
					+
					</a>
				</h3>
				<% if $Record.FlowStates %>
					<% with $Record %>
						<% loop $FlowStates.Reverse %>
							<% include State %>
						<% end_loop %>
					<% end_with %>
				<% end_if %>
				
			</div>
			<div class="flowchart-wrap">
				<div class="workspace">
					<h1>Workspace</h1>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="cms-content-actions cms-content-controls south flowchart-toolbar">
		<% if $Actions %>
		<div class="Actions">
			<% loop $Actions %>
				$Field
			<% end_loop %>
		</div>
		<% end_if %>
	</div>
</form>
