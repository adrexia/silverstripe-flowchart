<!doctype html>
<!--[if IE 6 ]><html class="no-js ie6 oldie" lang="$ContentLocale" id="ie6"><![endif]-->
<!--[if IE 7 ]><html class="no-js ie7 oldie" lang="$ContentLocale" id="ie7"><![endif]-->
<!--[if IE 8 ]><html class="no-js ie8 oldie" lang="$ContentLocale" id="ie8"><![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" id="ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="$ContentLocale"> <!--<![endif]-->

<head>
	<% base_tag %>
	<head>
		<title></title>
		<link rel="stylesheet" href="flowchart/css/demo-all.css">
		<link rel="stylesheet" href="flowchart/css/demo.css">
		<link rel="stylesheet" href="flowchart/css/flowchart.css">
	</head>
	 <body>

	 	<div class="flowchart-admin-wrap">

			<fieldset class="label-connect">
				<label for="label-name">Connection Label</label>
				<input id="label-name" name="labelName" value="" class="text" aria-described-by="flowchart-admin-use" />
				<em id="flowchart-admin-use" class="flowchart-em">
					(e.g "Yes", "No", "Accepted")</em>
			</fieldset>

			<div class="new-states">
				<h2>New States</h2>
				<em class="flowchart-em">(Drag and drop into your workspace)</em>
				<div class="drag-area"></div>
			</div>
			<div id="container" class="flowchart-container">
				<h1>Workspace</h1>

				<% loop FlowStates %>

				<div id="info_{$Pos}" class="item columns <% if $Size > 0 %>$Size<% else %>two<% end_if %>">
					<div class="num">$Number</div>
					<div class="drag-content">
						$Description
					</div>
				</div>

				<% end_loop %>

			</div>
		</div>


		<div id="list"></div>
	
		<!-- DEP -->
		<script src="flowchart/js/lib/jquery-1.9.0.js"></script>
		<script src="flowchart/js/lib/jquery-ui-1.9.2-min.js"></script>
		<script src="flowchart/js/lib/jquery.ui.touch-punch.min.js"></script>
		<!-- /DEP -->
				
		<!-- JS -->
		<!-- support lib for bezier stuff -->
		<script src="flowchart/js/lib/jsBezier-0.6.js"></script>
		<!-- jsplumb util -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/util.js"></script>
		<!-- base DOM adapter -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/dom-adapter.js"></script>
		<!-- main jsplumb engine -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/jsPlumb.js"></script>
		<!-- endpoint -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/endpoint.js"></script>
		<!-- connection -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/connection.js"></script>
		<!-- anchors -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/anchors.js"></script>
		<!-- connectors, endpoint and overlays  -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/defaults.js"></script>
		<!-- bezier connectors -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/connectors-bezier.js"></script>
		<!-- state machine connectors -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/connectors-statemachine.js"></script>
		<!-- flowchart connectors -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/connectors-flowchart.js"></script>
		<!-- SVG renderer -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/renderers-svg.js"></script>
		<!-- canvas renderer -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/renderers-canvas.js"></script>
		<!-- vml renderer -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/renderers-vml.js"></script>
		
		<!-- jquery jsPlumb adapter -->
		<script src="flowchart/js/thirdparty/jsPlumb/src/jquery.jsPlumb.js"></script>
		<!-- /JS -->

		<!--  demo code -->
		<!--<script type="text/javascript" src="../js/demo-jquery.js"></script>-->
		
		<!--  demo list and helper code. you don't need this stuff if you're just copying the single demo. -->
		<!--<script src="../js/demo-list.js"></script>
		<script src="../js/demo-helper-jquery.js"></script> -->

		<script src="flowchart/js/flow.js"></script>
	</body>
</html>
