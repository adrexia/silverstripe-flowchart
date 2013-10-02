<div class="flow-chart-view">
	<div id="container" class="flowchart-container">
		<input type="hidden" name="flow-chart-store" id="flow-chart-store" value='$FlowchartData' />
		<% loop FlowStates.Reverse %>
		<div id="id_{$ID}" data-id="$ID" class="state col new-state <% if $Size %>$Size<% else %>two<% end_if %>">
			<div class="num">
				<span>$Number</span>
			</div>
			<div class="drag-content">
				$Title
			</div>
		</div>
		<% end_loop %>
	</div>
</div>