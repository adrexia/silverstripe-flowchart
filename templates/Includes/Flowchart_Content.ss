<% loop FlowStates %>
<div class="modal" id="modal_{$ID}">
	<div class="content">
		<a class="close switch" data-trigger="|#modal_{$ID}"><i class="icon-cancel"></i></a>
		<article class="content-text">
			$Content
		</article>
	</div>
</div>
<% end_loop %>
