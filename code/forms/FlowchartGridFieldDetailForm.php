<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartGridFieldDetailForm extends GridFieldDetailForm {

	protected $template = 'FlowchartWorkspace';

	protected static $css_files = array(
		'flowchart/css/demo-all.css',
		'flowchart/css/demo.css',
		'flowchart/css/flowchart.css'
	);

	function __construct() {
		parent::__construct();
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

class FlowchartGridFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

	/**
	 * Replaces the default ItemEditForm with the Flowchart Workspace
	 *
	 * @return Form
	 */
	public function ItemEditForm() {
		// TODO: implement breadcrumbs, save, cancel/back buttons
	}

	/**
	 * Saves the Flowchart Workspace
	 *
	 * @return Form
	 */
	public function doSave($data, $form) {
		//TODO: implement saving to FlowchartPage
	}
}