<div data-pjax-fragment="StateList">
	<% if $Record.FlowStates %>
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
	<% end_if %>
</div>