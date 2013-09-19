<?php
/*

**/

class FlowState extends DataObject implements PermissionProvider {

	public static $db = array(
		'Number'=>'Int',
		'Description'=>'Text',
		'Content'=>'HTMLText',
		'Size'=>'Varchar(6)'
	);

	public static $has_one = array(
		'Parent'=>'FlowchartPage'
	);

	public static $searchable_fields = array(
		'Number',
		'Description',
		'Content',
	);

	public static $summary_fields = array(
		'Number'=>'Number',
		'Description'=>'Title',
		'Content.NoHTML'=>'Content'
	);
	
	public static $default_sort = 'Number';

	public function getCMSFields(){
		$fields = parent::getCMSFields();

		$fields->removeByName('ParentID');

		$number = $fields->dataFieldByName('Number');
		$title = $fields->dataFieldByName('Description');
		$content = $fields->dataFieldByName('Content');

		$number->setRightTitle("Displayed in flow state box");
		$title->setRightTitle("Displayed in flow state box");
		$content->setDescription("Extra information related to state. May be displayed on hover, or click");

		$spanOpt = array("two","four","six","eight");
		$fields->insertAfter(new DropdownField('Size', "Relative display width", $spanOpt),'Description');

		return $fields;
	}

	public function providePermissions() {
		return array(
			'PROCESS_FLOW_VIEW' => array(
				'name' => 'View process map admin',
				'category' => 'Process Maps',
			),
			'PROCESS_FLOW_EDIT' => array(
				'name' => 'Edit process flows',
				'category' => 'Process Maps',
			),
			'PROCESS_FLOW_DELETE' => array(
				'name' => 'Delete from process flows',
				'category' => 'Process Maps',
			),
			'PROCESS_FLOW_CREATE' => array(
				'name' => 'Create process maps',
				'category' => 'Process Maps'
			)
		);
	}

}