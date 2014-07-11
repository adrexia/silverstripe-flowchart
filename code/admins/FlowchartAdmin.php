<?php
/**
 * Graphical interface for creating basic flowcharts
 *
 * @package flowchart
 * @category admin
 */
class FlowchartAdmin extends ModelAdmin {

	/**
	 * @var array
	 * @static
	 */
	private static $managed_models = array('FlowchartPage','FlowState');

	/**
	 * @var string
	 * @static
	 */
	private static $url_segment = 'flowcharts';

	/**
	 * @var string
	 * @static
	 */
	private static $menu_title = 'Flowcharts';

	/**
	 * @var string
	 * @static
	 */
	private static $menu_icon = "flowchart/images/flowchart.png";

	/**
	 * @var string
	 * @static
	 */
	private static $tree_class = 'SS_Flowchart';

	/**
	 * Returns the cms edit form for the managed model.
	 * - FlowchartPage forms are replaced with the custom flowchart editing workspace form, see {@link: GridFieldFlowchartDetailForm}
	 * - FlowState forms are default cms generated.
	 *
	 * @return GridFieldDetailForm
	 */
	public function getEditForm($id = null, $fields = null) {
		
		// Fix CMS regression that filters flowcharts by live status
		Versioned::reading_stage('Stage');

		$form = parent::getEditForm($id, $fields);
		$sanitisedClassName = $this->sanitiseClassName($this->modelClass);
		$gridField = $form->Fields()->fieldByName($sanitisedClassName);
		$config = $gridField->getConfig();

		// If editing a FlowchartPage, replace the default gridfield detail form
		// with the custom flowchart workspace editing form
		if($sanitisedClassName == "FlowchartPage") {

			$config->removeComponentsByType('GridFieldDetailForm');
			$config->removeComponentsByType('GridFieldAddNewButton');
			$config->addComponent(new GridFieldFlowchartDetailForm());
		}
		// If editing a FlowState, show its default junk
		else if($sanitisedClassName == "FlowState") {
			$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
				singleton($sanitisedClassName)->getCurrentDisplayFields()
			);
		}
		return $form;
	}

	/**
	 * Override redirection logic to avoid redirects when adding from modal
	 */
	public function redirect($url, $code=302) {
		if($this->request->isAjax()) {

			if($this->request->getHeader('X-Pjax') === "StateList"){
				// Return url null if this is related to add a new state from flowchartadmin
				$this->response->addHeader('X-ControllerURL', null);

				// Pass the ID back to the JS. @todo, find a way to do this that isn't a hack
				$ID =(int)substr($url, strrpos($url, '/')+1);
				if(is_int($ID) && $ID > 0){
					$this->response->addHeader('X-ID', $ID);
				}
			} else{
				$this->response->addHeader('X-ControllerURL', $url);
			}
			if($this->request->getHeader('X-Pjax') && !$this->response->getHeader('X-Pjax')) {
				$this->response->addHeader('X-Pjax', $this->request->getHeader('X-Pjax'));
			}

			$oldResponse = $this->response;

			$newResponse = new LeftAndMain_HTTPResponse(
				$oldResponse->getBody(), 
				$oldResponse->getStatusCode(),
				$oldResponse->getStatusDescription()
			);
			foreach($oldResponse->getHeaders() as $k => $v) {
				$newResponse->addHeader($k, $v);
			}

			$newResponse->setIsFinished(true);
			$this->response = $newResponse;
			return ''; // Actual response will be re-requested by client
		} else {
			parent::redirect($url, $code);
		}
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
}
