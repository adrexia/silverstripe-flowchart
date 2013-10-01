<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class GridFieldFlowchartDetailForm extends GridFieldDetailForm {

	protected $template = 'Flowchart_EditForm';

	protected static $css_files = array(
		'flowchart/css/jsPlumb.css',
		'flowchart/css/flowchart.css'
	);

	public function __construct($name = 'FlowchartDetailForm') {
		parent::__construct($name);
		Requirements::combine_files('flowchart.css', self::$css_files);
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
	}

	public function getJSRequirements(){

		return array(
			// jsplumb util
			'flowchart/js/thirdparty/jsPlumb/src/util.js',
			// base DOM adapter
			'flowchart/js/thirdparty/jsPlumb/src/dom-adapter.js',
			// main jsplumb engine
			'flowchart/js/thirdparty/jsPlumb/src/jsPlumb.js',
			//endpoint
			'flowchart/js/thirdparty/jsPlumb/src/endpoint.js',
			// connection
			'flowchart/js/thirdparty/jsPlumb/src/connection.js',
			// anchors
			'flowchart/js/thirdparty/jsPlumb/src/anchors.js',
			// connectors, endpoint and overlays
			'flowchart/js/thirdparty/jsPlumb/src/defaults.js',
			// bezier connectors
			'flowchart/js/thirdparty/jsPlumb/src/connectors-bezier.js',
			// state machine connectors
			'flowchart/js/thirdparty/jsPlumb/src/connectors-statemachine.js',
			// flowchart connectors
			'flowchart/js/thirdparty/jsPlumb/src/connectors-flowchart.js',
			// SVG renderer
			'flowchart/js/thirdparty/jsPlumb/src/renderers-svg.js',
			//canvas renderer
			'flowchart/js/thirdparty/jsPlumb/src/renderers-canvas.js',
			// vml renderer
			'flowchart/js/thirdparty/jsPlumb/src/renderers-vml.js',

			// jquery jsPlumb adapter
			'flowchart/js/thirdparty/jsPlumb/src/jquery.jsPlumb.js',

			// custom
			'flowchart/js/Flowchart.js'
		);
	}

}

class GridFieldFlowchartDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

	private static $allowed_actions = array(
		'doSave'
	);


	public function getFlowstates() {
		return FlowState::get()->filter('ParentID', $this->record->ID);
	}

	public function getFlowchartData() {
		// Need to strip the slashes that raw2SQL applies during the doSave function below
		return stripslashes($this->record->FlowchartData);
	}

	public function doSave($data, $form) {
		$this->record->FlowchartData = Convert::raw2SQL($data['flow-chart-store']);
		$this->record->write();
		Controller::curr()->redirect($this->Link());
	}
}