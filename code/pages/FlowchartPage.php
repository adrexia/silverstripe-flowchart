<?php

class FlowchartPage extends Page {
	private static $icon = "flowchart/images/flowchart.png";
	private static $description = 'A page for storing data and displaying flowcharts';

	private static $db = array(

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
			GridFieldConfig_RelationEditor::create());

		$fields->addFieldToTab('Root.FlowStates', $states);

		return $fields;

	}




}

class FlowchartPage_Controller extends Page_Controller {
	
	function init() {
		parent::init();
	}


	




}