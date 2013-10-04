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
				<h3 class="cms-panel-header states-heading">New States</h3>
				<% loop $Record.FlowStates.Reverse %>
					<div id="id_{$ID}" data-id="$ID" class="order_$Modulus(6) state col new-state <% if $Size %>$Size<% else %>two<% end_if %>" tabindex="0">
						<% if $Number %>
						<div class="num">
							<span>$Number</span>
						</div>
						<% end_if %>
						<div class="drag-content">
							$TitleText
						</div>
					</div>
				<% end_loop %>
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
