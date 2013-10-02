<% loop FlowStates %>
<div class="modal" id="modal_{$ID}">
	<div class="content">
		<a class="close switch" data-trigger="|#modal_{$ID}"><i class="icon-cancel" /></i></a>
		<div class="row">
			<div class="ten columns centered text-center">
				$Content
			</div>
		</div>
	</div>
</div>
<% end_loop %>
