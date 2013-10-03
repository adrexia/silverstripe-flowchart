<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class GridFieldFlowchartDetailForm extends GridFieldDetailForm {

	/**
	 *
	 * @var array
	 */
	protected static $css_files = array(
		'flowchart/css/jsPlumb.css',
		'flowchart/css/flowchart.css'
	);

	public function __construct($name = 'FlowchartDetailForm') {
		parent::__construct($name);
		Requirements::combine_files('flowchart.css', self::$css_files);
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
	}

	/**
	 * 
	 * @return array
	 */
	public function getJSRequirements(){

		return array(
			'flowchart/js/thirdparty/jsPlumb/src/util.js',
			'flowchart/js/thirdparty/jsPlumb/src/dom-adapter.js',
			'flowchart/js/thirdparty/jsPlumb/src/jsPlumb.js',
			'flowchart/js/thirdparty/jsPlumb/src/endpoint.js',
			'flowchart/js/thirdparty/jsPlumb/src/connection.js',
			'flowchart/js/thirdparty/jsPlumb/src/anchors.js',
			'flowchart/js/thirdparty/jsPlumb/src/defaults.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-bezier.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-statemachine.js',
			'flowchart/js/thirdparty/jsPlumb/src/connectors-flowchart.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-svg.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-canvas.js',
			'flowchart/js/thirdparty/jsPlumb/src/renderers-vml.js',
			'flowchart/js/thirdparty/jsPlumb/src/jquery.jsPlumb.js',
			'flowchart/js/Flowchart.js'
		);
	}

}

class GridFieldFlowchartDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

	/**
	 *
	 * @var array
	 */
	private static $allowed_actions = array(
		'edit',
		'view',
		'ItemEditForm',
		'publish'
	);
	
	/**
	 * Builds an item edit form. 
	 * 
	 * @return Form 
	 */
	public function ItemEditForm() {
		
		// If there are no record set, redirect back to the "main" model admin
		if (empty($this->record) || $this->record->ID == 0) {
			$controller = Controller::curr();
			$noActionURL = $controller->removeAction($_REQUEST['url']);
			$controller->getResponse()->removeHeader('Location');   //clear the existing redirect
			return $controller->redirect($noActionURL, 302);
		}
		
		// Create form field
		$fields = new FieldList();
		$chartData = new HiddenField('FlowchartData');
		$chartData->setAttribute('data-chart-storage', 'true');
		$fields->push($chartData);
		
		
		$existsOnLive = $this->record->getExistsOnLive();
		
		// Create the action buttons
		$actions = new FieldList();
		
		if($this->record->canEdit()) {
			$actions->push(FormAction::create('doSave', _t('SiteTree.BUTTONSAVED', 'Saved'))
				->setAttribute('data-icon', 'accept')
				->setAttribute('data-icon-alternate', 'addpage')
				->setAttribute('data-text-alternate', _t('CMSMain.SAVEDRAFT','Save draft'))
				->setUseButtonTag(true)
			);
		}
		
		if($this->record->canPublish() && !$this->record->IsDeletedFromStage) {
			// "publish", as with "save", it supports an alternate state to show when action is needed.
			$actions->push(
				$publish = FormAction::create('publish', _t('SiteTree.BUTTONPUBLISHED', 'Published'))
					->setAttribute('data-icon', 'accept')
					->setAttribute('data-icon-alternate', 'disk')
					->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISH', 'Save & publish'))
					->setUseButtonTag(true)
			);

			// Set up the initial state of the button to reflect the state of the underlying SiteTree object.
			if($this->record->stagesDiffer('Stage', 'Live')) {
				$publish->addExtraClass('ss-ui-alternate');
			}
		}
		
		$form = new Form($this, 'ItemEditForm', $fields, $actions);
		$form->loadDataFrom($this->record);
		$form->Backlink = $this->getBackLink();
		$form->setTemplate('Flowchart_EditForm');
		return $form;
	}
	
	/**
	 * This method tries to blend DetailForm::doSave behaviour with CMSMain 
	 * publish behaviour. It might not be rock solid..
	 * 
	 * @param array $data
	 * @param Form $form
	 * @return SS_HTTPResponse
	 */
	public function publish($data, $form) {
		if(!$this->record->canPublish()) {
			return $controller->httpError(403);
		}
		
		$controller = Controller::curr();
		$list = $this->gridField->getList();
		if($list instanceof ManyManyList) {
			// Data is escaped in ManyManyList->add()
			$extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
		} else {
			$extraData = null;
		}
		
		try {
			$this->record->writeWithoutVersion();
			$form->saveInto($this->record);
			$this->record->write();
			$list->add($this->record, $extraData);
			$this->record->doPublish();
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			$responseNegotiator = new PjaxResponseNegotiator(array(
				'CurrentForm' => function() use(&$form) {
					return $form->forTemplate();
				},
				'default' => function() use(&$controller) {
					return $controller->redirectBack();
				}
			));
			if($controller->getRequest()->isAjax()){
				$controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
			}
			return $responseNegotiator->respond($controller->getRequest());
		}
		
		$link = '"' . $this->record->Title . '"';
		$message = _t(
			'GridFieldDetailForm.Saved', 
			'Saved {name} {link}',
			array(
				'name' => $this->record->i18n_singular_name(),
				'link' => $link
			)
		);
		$form->sessionMessage($message, 'good');
		return $this->edit(Controller::curr()->getRequest());
	}
}