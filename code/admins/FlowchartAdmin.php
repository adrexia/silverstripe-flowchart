<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartAdmin extends ModelAdmin {

 	private static $managed_models = array('FlowchartPage');

	private static $url_segment = 'flowcharts';

	private static $menu_title = 'Flowcharts';

	private static $menu_icon = "flowchart/images/flowchart.png";

	private static $tree_class = 'SS_Flowchart';

	public function getEditForm($id = null, $fields = null){
    	$form = parent::getEditForm($id, $fields);
	    $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
	    $config = $gridField->getConfig();
	    $config->removeComponentsByType('GridFieldDetailForm');
	    $config->addComponent(new FlowchartGridFieldDetailForm());
	    return $form;
	}
}

class FlowchartGridFieldDetailForm extends GridFieldDetailForm {

	protected $template = 'FlowchartWorkspace';

	protected static $css_files = array(
		'flowchart/css/demo-all.css',
		'flowchart/css/demo.css',
		'flowchart/css/flowchart.css'
	);

	protected static $js_files = array(
		// Dependencies
		'flowchart/js/lib/jquery-1.9.0.js',
		'flowchart/js/lib/jquery-ui-1.9.2-min.js',
		'flowchart/js/lib/jquery.ui.touch-punch.min.js',

		// support lib for bezier stuff
		'flowchart/js/lib/jsBezier-0.6.js',
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
		'flowchart/js/flow-saveload.js',
		'flowchart/js/flow-ui.js',
		'flowchart/js/flow-event.js',
	);

	function __construct() {
		parent::__construct();
		Requirements::combine_files('flowchart.css', self::$css_files);
		Requirements::combine_files('flowchart.js', self::$js_files);
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
