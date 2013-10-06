<?php
/**
 * Represents one item on a {@link FlowchartPage} flowchart diagram
 *
 * @package cms
 * @category admin
 * @author scienceninjas@silverstripe.com
 */
class FlowState extends DataObject implements PermissionProvider {

	/**
	 * @var array
	 * @static
	 */
	private static $db = array(
		'Number'=>'Float',
		'TitleText'=>'Text',
		'Content'=>'HTMLText',
		'Size'=>'Varchar(6)'
	);

	/**
	 * @var array
	 * @static
	 */
	private static $has_one = array(
		'LinkedState'=>'FlowState',
		'Parent'=>'FlowchartPage'
	);

	/**
	 * @var array
	 * @static
	 */
	private static $searchable_fields = array(
		'Number',
		'TitleText',
		'Content',
	);

	/**
	 * @var array
	 * @static
	 */
	private static $summary_fields = array(
		'Number'=>'Number',
		'TitleText'=>'Title'
	);

	/**
	 * @var string
	 * @static
	 */
	private static $default_sort = 'Number';

	/**
	 * Returns an array of the current display fields
	 * @TODO explain this better, or rename the funciton to be more meaningful
	 *
	 * @return array
	 */
	public function getCurrentDisplayFields(){
		return array(
			'Number'=>'Number',
			'Title'=>'Title',
			'ParentName'=>'Flowchart Page'
		);
	}

	/**
	 * Get the name of the {@link FlowchartPage} parent object
	 * @return string
	 */
	public function getParentName(){
		$parent = FlowchartPage::get()->byID($this->ParentID);
		if($parent){
			return $parent->Title;
		}
	}

	/**
	 * Returns the {@Link FieldList} of cms form fields for editing this object
	 *
	 * @return FieldList
	 */
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

		// If within a flowchart page remove parentID
		if(!(Controller::curr() instanceof FlowchartAdmin)) {
			$fields->removeByName('ParentID');
		}

		return $fields;
	}

	/**
	 * Get the Title of the FlowState
	 *
	 * @return string
	 */
	public function getTitle(){
		$title = $this->TitleText;
		if($title == ''){
			$title = "New state";
		}

		$textObj = new Text('TitleText');
		$textObj->setValue($title);
		return $textObj->LimitWordCount(10);
	}

	public function canCreate($member = null) {
		return Permission::check('FLOWCHART_VIEW');
	}

	public function canEdit($member = null) {
		return Permission::check('FLOWCHART_EDIT');
	}

	public function canDelete($member = null) {
		return Permission::check('FLOWCHART_DELETE');
	}

	public function canView($member = null) {
		return Permission::check('FLOWCHART_CREATE');
	}

	/**
	 * Get an array of {@link Permission} definitions that this object supports
	 *
	 * @return array
	 */
	public function providePermissions() {
		return array(
			'FLOWCHART_VIEW' => array(
				'name' => 'View flowchart admin',
				'category' => 'Flowcharts',
			),
			'FLOWCHART_EDIT' => array(
				'name' => 'Edit flowcharts',
				'category' => 'Flowcharts',
			),
			'FLOWCHART_DELETE' => array(
				'name' => 'Delete flowcharts',
				'category' => 'Flowcharts',
			),
			'FLOWCHART_CREATE' => array(
				'name' => 'Create flowcharts',
				'category' => 'Flowcharts'
			)
		);
	}
}
