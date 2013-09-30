<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartGridFieldDetailForm extends GridFieldDetailForm {

	protected $template = 'FlowchartWorkspace';

	protected static $css_files = array(
		'flowchart/css/demo-all.css',
		'flowchart/css/demo.css',
		'flowchart/css/flowchart.css'
	);

	function __construct() {
		parent::__construct();
		Requirements::combine_files('flowchart.css', self::$css_files);
		Requirements::combine_files('flowchart.js', $this->getJSRequirements());
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
	/**
	 *
	 * @param GridFIeld $gridField
	 * @param GridField_URLHandler $component
	 * @param DataObject $record
	 * @param Controller $popupController
	 * @param string $popupFormName 
	 */
	public function __construct($gridField, $component, $record, $popupController, $popupFormName) {
		$this->gridField = $gridField;
		$this->component = $component;
		$this->record = $record;
		$this->popupController = $popupController;
		$this->popupFormName = $popupFormName;
		parent::__construct($gridField, $component, $record, $popupController, $popupFormName);
	}

	public function getFlowStates(){
		return FlowState::get()->filter(array('ParentID'=>$this->record->ID));
	}

	public function Link($action = null) {
		return Controller::join_links($this->gridField->Link('item'),
			$this->record->ID ? $this->record->ID : 'new', $action);
	}

	public function view($request) {
		if(!$this->record->canView()) {
			$this->httpError(403);
		}

		$controller = $this->getToplevelController();

		$form = $this->ItemEditForm($this->gridField, $request);
		$form->makeReadonly();

		$data = new ArrayData(array(
			'Backlink'     => $controller->Link(),
			'ItemEditForm' => $form
		));
		$return = $data->renderWith($this->template);

		if($request->isAjax()) {
			return $return;
		} else {
			return $controller->customise(array('Content' => $return));
		}
	}

	public function edit($request) {
		$controller = $this->getToplevelController();
		$form = $this->ItemEditForm($this->gridField, $request);

		$return = $this->customise(array(
			'Backlink' => $controller->hasMethod('Backlink') ? $controller->Backlink() : $controller->Link(),
			'ItemEditForm' => $form,
		))->renderWith($this->template);

		if($request->isAjax()) {
			return $return;	
		} else {
			// If not requested by ajax, we need to render it within the controller context+template
			return $controller->customise(array(
				// TODO CMS coupling
				'Content' => $return,
			));	
		}
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
	public function ItemEditForm() {
		$list = $this->gridField->getList();

		if (empty($this->record)) {
			$controller = Controller::curr();
			$noActionURL = $controller->removeAction($_REQUEST['url']);
			$controller->getResponse()->removeHeader('Location');   //clear the existing redirect
			return $controller->redirect($noActionURL, 302);
		}

		$canView = $this->record->canView();
		$canEdit = $this->record->canEdit();
		$canDelete = $this->record->canDelete();
		$canCreate = $this->record->canCreate();

		if(!$canView) {
			$controller = Controller::curr();
			// TODO More friendly error
			return $controller->httpError(403);
		}

		$actions = new FieldList();
		if($this->record->ID !== 0) {
			if($canEdit) {
				$actions->push(FormAction::create('doSave', _t('GridFieldDetailForm.Save', 'Save'))
					->setUseButtonTag(true)
					->addExtraClass('ss-ui-action-constructive')
					->setAttribute('data-icon', 'accept'));
			}

			if($canDelete) {
				$actions->push(FormAction::create('doDelete', _t('GridFieldDetailForm.Delete', 'Delete'))
					->setUseButtonTag(true)
					->addExtraClass('ss-ui-action-destructive action-delete'));
			}

		}else{ // adding new record
			//Change the Save label to 'Create'
			$actions->push(FormAction::create('doSave', _t('GridFieldDetailForm.Create', 'Create'))
				->setUseButtonTag(true)
				->addExtraClass('ss-ui-action-constructive')
				->setAttribute('data-icon', 'add'));
				
			// Add a Cancel link which is a button-like link and link back to one level up.
			$curmbs = $this->Breadcrumbs();
			if($curmbs && $curmbs->count()>=2){
				$one_level_up = $curmbs->offsetGet($curmbs->count()-2);
				$text = sprintf(
					"<a class=\"%s\" href=\"%s\">%s</a>",
					"crumb ss-ui-button ss-ui-action-destructive cms-panel-link ui-corner-all", // CSS classes
					$one_level_up->Link, // url
					_t('GridFieldDetailForm.CancelBtn', 'Cancel') // label
				);
				$actions->push(new LiteralField('cancelbutton', $text));
			}
		}
		$fields = $this->component->getFields();
		if(!$fields) $fields = $this->record->getCMSFields();
		$form = new Form(
			$this,
			'ItemEditForm',
			$fields,
			$actions,
			$this->component->getValidator()
		);
		
		$form->loadDataFrom($this->record, $this->record->ID == 0 ? Form::MERGE_IGNORE_FALSEISH : Form::MERGE_DEFAULT);

		if($this->record->ID && !$canEdit) {
			// Restrict editing of existing records
			$form->makeReadonly();
			// Hack to re-enable delete button if user can delete
			if ($canDelete) {
				$form->Actions()->fieldByName('action_doDelete')->setReadonly(false);
			}
		} elseif(!$this->record->ID && !$canCreate) {
			// Restrict creation of new records
			$form->makeReadonly();
		}

		// Load many_many extraData for record.
		// Fields with the correct 'ManyMany' namespace need to be added manually through getCMSFields().
		if($list instanceof ManyManyList) {
			$extraData = $list->getExtraData('', $this->record->ID);
			$form->loadDataFrom(array('ManyMany' => $extraData));
		}
		
		// TODO Coupling with CMS
		$toplevelController = $this->getToplevelController();
		if($toplevelController && $toplevelController instanceof LeftAndMain) {
			// Always show with base template (full width, no other panels), 
			// regardless of overloaded CMS controller templates.
			// TODO Allow customization, e.g. to display an edit form alongside a search form from the CMS controller
			$form->setTemplate('LeftAndMain_EditForm');
			$form->addExtraClass('cms-content cms-edit-form center');
			$form->setAttribute('data-pjax-fragment', 'CurrentForm Content');
			if($form->Fields()->hasTabset()) {
				$form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
				$form->addExtraClass('cms-tabset');
			}

			$form->Backlink = $this->getBackLink();
		}

		$cb = $this->component->getItemEditFormCallback();
		if($cb) $cb($form, $this);
		$this->extend("updateItemEditForm", $form);
		return $form;
	}

	/**
	 * Traverse up nested requests until we reach the first that's not a GridFieldDetailForm_ItemRequest.
	 * The opposite of {@link Controller::curr()}, required because
	 * Controller::$controller_stack is not directly accessible.
	 * 
	 * @return Controller
	 */
	protected function getToplevelController() {
		$c = $this->popupController;
		while($c && $c instanceof GridFieldDetailForm_ItemRequest) {
			$c = $c->getController();
		}
		return $c;
	}
	
	protected function getBackLink(){
		// TODO Coupling with CMS
		$backlink = '';
		$toplevelController = $this->getToplevelController();
		if($toplevelController && $toplevelController instanceof LeftAndMain) {
			if($toplevelController->hasMethod('Backlink')) {
				$backlink = $toplevelController->Backlink();
			} elseif($this->popupController->hasMethod('Breadcrumbs')) {
				$parents = $this->popupController->Breadcrumbs(false)->items;
				$backlink = array_pop($parents)->Link;
			} 
		}
		if(!$backlink) $backlink = $toplevelController->Link();
		
		return $backlink;
	}

	

	public function doSave($data, $form) {
		$new_record = $this->record->ID == 0;
		$controller = Controller::curr();
		$list = $this->gridField->getList();
		
		if($list instanceof ManyManyList) {
			// Data is escaped in ManyManyList->add()
			$extraData = (isset($data['ManyMany'])) ? $data['ManyMany'] : null;
		} else {
			$extraData = null;
		}

		if(!$this->record->canEdit()) {
			return $controller->httpError(403);
		}
		
		if (isset($data['ClassName']) && $data['ClassName'] != $this->record->ClassName) {
			$newClassName = $data['ClassName'];
			// The records originally saved attribute was overwritten by $form->saveInto($record) before.
			// This is necessary for newClassInstance() to work as expected, and trigger change detection
			// on the ClassName attribute
			$this->record->setClassName($this->record->ClassName);
			// Replace $record with a new instance
			$this->record = $this->record->newClassInstance($newClassName);
		}

		try {
			$form->saveInto($this->record);
			$this->record->write();
			$list->add($this->record, $extraData);
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

		// TODO Save this item into the given relationship

		$link = '<a href="' . $this->Link('edit') . '">"' 
			. htmlspecialchars($this->record->Title, ENT_QUOTES) 
			. '"</a>';
		$message = _t(
			'GridFieldDetailForm.Saved', 
			'Saved {name} {link}',
			array(
				'name' => $this->record->i18n_singular_name(),
				'link' => $link
			)
		);
		
		$form->sessionMessage($message, 'good');

		if($new_record) {
			return Controller::curr()->redirect($this->Link());
		} elseif($this->gridField->getList()->byId($this->record->ID)) {
			// Return new view, as we can't do a "virtual redirect" via the CMS Ajax
			// to the same URL (it assumes that its content is already current, and doesn't reload)
			return $this->edit(Controller::curr()->getRequest());
		} else {
			// Changes to the record properties might've excluded the record from
			// a filtered list, so return back to the main view if it can't be found
			$noActionURL = $controller->removeAction($data['url']);
			$controller->getRequest()->addHeader('X-Pjax', 'Content'); 
			return $controller->redirect($noActionURL, 302); 
		}
	}

	public function doDelete($data, $form) {
		$title = $this->record->Title;
		try {
			if (!$this->record->canDelete()) {
				throw new ValidationException(
					_t('GridFieldDetailForm.DeletePermissionsFailure',"No delete permissions"),0);
			}

			$this->record->delete();
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			return Controller::curr()->redirectBack();
		}

		$message = sprintf(
			_t('GridFieldDetailForm.Deleted', 'Deleted %s %s'),
			$this->record->i18n_singular_name(),
			htmlspecialchars($title, ENT_QUOTES)
		);
		
		$toplevelController = $this->getToplevelController();
		if($toplevelController && $toplevelController instanceof LeftAndMain) {
			$backForm = $toplevelController->getEditForm();
			$backForm->sessionMessage($message, 'good');
		} else {
			$form->sessionMessage($message, 'good');
		}

		//when an item is deleted, redirect to the parent controller
		$controller = Controller::curr();
		$controller->getRequest()->addHeader('X-Pjax', 'Content'); // Force a content refresh

		return $controller->redirect($this->getBacklink(), 302); //redirect back to admin section
	}

	/**
	 * @param String
	 */
	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}

	/**
	 * @return String
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @return Controller
	 */
	public function getController() {
		return $this->popupController;
	}

	/**
	 * @return GridField
	 */
	public function getGridField() {
		return $this->gridField;
	}

	/**
	 * CMS-specific functionality: Passes through navigation breadcrumbs
	 * to the template, and includes the currently edited record (if any).
	 * see {@link LeftAndMain->Breadcrumbs()} for details.
	 * 
	 * @param boolean $unlinked 
	 * @return ArrayData
	 */
	public function Breadcrumbs($unlinked = false) {
		if(!$this->popupController->hasMethod('Breadcrumbs')) return;

		$items = $this->popupController->Breadcrumbs($unlinked);
		if($this->record && $this->record->ID) {
			$items->push(new ArrayData(array(
				'Title' => $this->record->Title,
				'Link' => $this->Link()
			)));	
		} else {
			$items->push(new ArrayData(array(
				'Title' => sprintf(_t('GridField.NewRecord', 'New %s'), $this->record->i18n_singular_name()),
				'Link' => false
			)));	
		}
		
		return $items;
	}
}