<?php

class FlowchartPage extends Page {

	private static $icon = "flowchart/images/flowchart.png";

	private static $description = 'A page for storing data and displaying flowcharts';

	private static $db = array(
		'FlowchartData' => 'Text'
	);

	private static $has_many = array(
		"FlowStates" => "FlowState"
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$states = new GridField(
			'FlowStates',
			'FlowState',
			$this->FlowStates(),
			$config = GridFieldConfig_RelationEditor::create());

		$fields->addFieldToTab('Root.FlowStates', $states);

		return $fields;
	}
}

class FlowchartPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::combine_files('flowchart.css', $this->getCSSRequirements());
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
	}

	public function getCSSRequirements(){
		return array(
			'flowchart/css/jsPlumb.css',
			'flowchart/css/flowchart.css'
		);
	}

	public function getJSRequirements(){

		return array(
			FRAMEWORK_DIR . '/thirdparty/jquery-entwine/dist/jquery.entwine-dist.js',
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

	public function getFlowchartData() {
		// Need to strip the slashes that raw2SQL applies during the doSave function below
		return stripslashes($this->failover->FlowchartData);
	}

}
