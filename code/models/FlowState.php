<?php
/**
 * Represents one item on a {@link FlowchartPage} flowchart diagram
 *
 * @package flowchart
 * @category model
 */
class FlowState extends DataObject implements PermissionProvider {

	/**
	 * @var array
	 * @static
	 */
	private static $db = array(
		'Number'=>'Varchar(255)',
		'TitleText'=>'Text',
		'Content'=>'HTMLText',
		'Size'=>'Varchar(255)'
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
	 * @var string
	 * @static
	 */
	private static $indexes = array(
		"fulltext (TitleText, Content)"
	);

	private static $create_table_options = array(
		'MySQLDatabase' => 'ENGINE=MyISAM'
	);

	/**
	 * Returns an array of the field names for displaying FlowStates in an admin gridflield summary view
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
	 * Get the name of the {@link FlowchartPage} parent page
	 * @return string
	 */
	public function getParentName() {
		if ($this->ParentID) {
			return $this->Parent()->Title;
		}
		return null;
	}

	/**
	 * Returns the {@Link FieldList} of cms form fields for editing this object
	 *
	 * @return FieldList
	 */
	public function getCMSFields(){
		$fields = parent::getCMSFields();

		// Remove LinkedStateID in order to filter out current record ID 
		$fields->removeByName('LinkedStateID');

		// Remove parentID. Re-add as TreeDropdownField where needed
		$fields->removeByName('ParentID');

		$number = $fields->dataFieldByName('Number');
		$title = $fields->dataFieldByName('TitleText');
		$content = $fields->dataFieldByName('Content');

		$number->setRightTitle("Displayed in flow state box");
		$title->setRightTitle("Displayed in flow state box");
		$content->setDescription("Extra information related to state. May be displayed on hover, or click");

		$spanOpt = array("two"=>"two","four"=>"four","six"=>"six","eight"=>"eight");
		$fields->insertAfter(new DropdownField('Size', "Relative display width", $spanOpt),'TitleText');

		$fields->insertAfter(
			$linkedState = new DropdownField('LinkedStateID', "Linked State", FlowState::get()->exclude('ID', $this->ID)->map('ID', 'Title')),
			'Content'
		);

		$linkedState->setEmptyString(' ')
					->setRightTitle("HTML link to another State");

		// If not within a flowchart page re-add parentID as DropdownField
		if((Controller::curr() instanceof FlowchartAdmin)) {
			$fields->insertAfter(new DropdownField('ParentID', "Belongs to", FlowchartPage::get()->map('ID','Title')),'Content');
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

	/**
	 * Get a link to the parent flowchart page.
	 * This is required by SOLR so that FlowStates can be clicked on in search results.
	 *
	 * @return string
	 */
	public function Link($action = 'show') {
		if ($this->ParentID) {
			return $this->Parent()->Link();
		}
		return null;
	}

	/**
	 * Returns a comma separated string of field names that are searchable {@link getSearchResults()}
	 *
	 * @return string
	 */
	protected function getSearchableFields() {
		return implode(self::$searchable_fields, ',');
	}

	/**
	 * Returns the FlowState objects that match the search query, using a boolean mode fulltext search
	 *
	 * @param string $searchQuery
	 */
	public function getSearchResults($searchQuery) {
		return DataObject::get("FlowState", "MATCH (". $this->getSearchableFields() .") AGAINST ('". $searchQuery ."' IN BOOLEAN MODE)");
	}

	/**
	 * Returns a custom SearchContext that matches search queries with filters on the searchable fields in this object
	 *
	 * @TODO make this actually search
	 * @return SearchContext
	 */
	public function getCustomSearchContext() {
		$fields = new FieldList(self::$searchable_fields);
		$filters = array(
			'Number' => new PartialMatchFilter('Number'),
			'TitleText' => new PartialMatchFilter('TitleText'),
			'Content' => new PartialMatchFilter('Content')
		);
		return new SearchContext($this->class, $fields, $filters);
	}
}
