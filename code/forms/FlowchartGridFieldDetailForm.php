<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartGridFieldDetailForm extends GridFieldDetailForm {

	protected $template = 'Flowchart_EditForm';

	protected static $css_files = array(
		'flowchart/css/demo-all.css',
		'flowchart/css/demo.css',
		'flowchart/css/flowchart.css'
	);

	public function __construct($name = 'FlowchartDetailForm') {
		parent::__construct($name);
		Requirements::combine_files('flowchart.css', self::$css_files);
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
	}

	/**
	 * Builds an item edit form.  The arguments to getCMSFields() are the popupController and
	 * popupFormName, however this is an experimental API and may change.
	 * 
	 * @todo In the future, we will probably need to come up with a tigher object representing a partially
	 * complete controller with gaps for extra functionality.  This, for example, would be a better way
	 * of letting Security/login put its log-in form inside a UI specified elsewhere.
	 * 
	 * @return Form 
	 */
	public function FlowchartItemEditForm() {
		$form = parent::ItemEditForm();

		$form->setTemplate('FlowchartWorkspace');
		return $form;
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

	public function getFlowstates() {
		return FlowState::get()->filter('ParentID', $this->record->ID);
	}

	public function getFlowchartData() {
		return $this->record->FlowchartData;
	}

	public function getFlowchartID() {
		return $this->record->ID;
	}

//	public function doSave($data, $form) {
	//	die('saving');
	//}
}

