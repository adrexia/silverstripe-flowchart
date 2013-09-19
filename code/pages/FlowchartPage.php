<?php

class FlowchartPage extends Page {
	public static $icon = "flowchart/images/flowchart.png";
	public static $description = 'A page for storing data and displaying flowcharts';

	public static $db = array(

	);
	public static $has_many = array(
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