<?php
/*

**/

class FlowState extends DataObject implements PermissionProvider {

	private static $db = array(
		'Number'=>'Float',
		'TitleText'=>'Text',
		'Content'=>'HTMLText',
		'Size'=>'Varchar(6)'
	);

	private static $has_one = array(
		'Parent'=>'FlowchartPage',
		'LinkedState'=>'FlowState'
	);

	private static $searchable_fields = array(
		'Number',
		'TitleText',
		'Content',
	);

	private static $summary_fields = array(
		'Number'=>'Number',
		'TitleText'=>'Title'
	);
	
	private static $default_sort = 'Number';

	public function getCurrentDisplayFields(){
		return array(
			'Number'=>'Number',
			'Title'=>'Title',
			'ParentName'=>'Flowchart Page'
		);
	}

	public function getParentName(){
		$parent = FlowchartPage::get()->byID($this->ParentID);
		if($parent){
			return $parent->Title;
		}
	}

	public function getCMSFields(){
		$fields = parent::getCMSFields();

		$number = $fields->dataFieldByName('Number');
		$title = $fields->dataFieldByName('TitleText');
		$content = $fields->dataFieldByName('Content');

		$number->setRightTitle("Displayed in flow state box");
		$title->setRightTitle("Displayed in flow state box");
		$content->setDescription("Extra information related to state. May be displayed on hover, or click");

		$spanOpt = array("two"=>"two","four"=>"four","six"=>"six","eight"=>"eight");
		$fields->insertAfter(new DropdownField('Size', "Relative display width", $spanOpt),'TitleText');

		return $fields;
	}

	public function getTitle(){
		$title = $this->TitleText;
		if($title == ''){
			$title = "New state";
		}

		$textObj = new Text('TitleText');
		$textObj->setValue($title);
		return $textObj->LimitWordCount(10);
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