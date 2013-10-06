<?php
/**
 * Graphical interface for creating basic flowcharts
 *
 * @package silverstripe-flowchart
 * @category page
 */
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

/**
 * Graphical interface for creating basic flowcharts
 *
 * @package silverstripe-flowchart
 * @category controller
 */
class FlowchartPage_Controller extends Page_Controller {

	/**
	 * Initialises the controller and combines the script and css requirements
	 *
	 * @return void
	 */
	function init() {
		parent::init();
		Requirements::combine_files('flowchart.css', $this->getCSSRequirements());
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
	}

	/**
	 * Returns the json_encoded flowchart data
	 *
	 * @return string
	 */
	public function getFlowchartData() {
		// Strip the slashes that raw2SQL applies during saving in {@link GridFieldFlowchartDetailForm}
		return stripslashes($this->failover->FlowchartData);
	}

	/**
	 * Returns an array of the CSS requirements for the form
	 *
	 * @return array
	 */
	public function getCSSRequirements() {
		return array(
			'flowchart/css/jsPlumb.css',
			'flowchart/css/flowchart.css'
		);
	}

	/**
	 * Returns an array of the JavaScript {@link Requirements} for displaying the {@link FlowchartPage}
	 *
	 * @return array
	 */
	public function getJSRequirements() {
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
}
