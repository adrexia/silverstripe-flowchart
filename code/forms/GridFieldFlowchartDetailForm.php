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
			'flowchart/js/thirdparty/jsPlumb/src/util.js',
			'flowchart/js/thirdparty/jsPlumb/src/dom-adapter.js',
			'flowchart/js/thirdparty/jsPlumb/src/jsPlumb.js',
			'flowchart/js/thirdparty/jsPlumb/src/endpoint.js',
			'flowchart/js/thirdparty/jsPlumb/src/connection.js',
			'flowchart/js/thirdparty/jsPlumb/src/anchors.js',
			'flowchart/js/thirdparty/jsPlumb/src/defaults.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-bezier.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-statemachine.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-flowchart.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-svg.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-canvas.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-vml.js',
			'flowchart/js/thirdparty/jsPlumb/src/jquery.jsPlumb.js',
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