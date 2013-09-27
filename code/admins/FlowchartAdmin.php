<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartAdmin extends ModelAdmin {

 	private static $managed_models = array('FlowchartPage');

	private static $url_segment = 'flowcharts';

	private static $menu_title = 'Flowcharts';

	private static $menu_icon = "flowchart/images/flowchart.png";

	private static $tree_class = 'SS_Flowchart';

	public function getEditForm($id = null, $fields = null){
    	$form = parent::getEditForm($id, $fields);
	    $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
	    $config = $gridField->getConfig();
	    $config->removeComponentsByType('GridFieldDetailForm');
	    $config->addComponent(new FlowchartGridFieldDetailForm());
	    return $form;
	}
}

class FlowchartGridFieldDetailForm extends GridFieldDetailForm {

	protected $template = 'FlowchartWorkspace';
}

class FlowchartGridFieldDetailForm_ItemRequest extends GridFieldDetailForm_ItemRequest {

	/**
	 * Replaces the default ItemEditForm with the Flowchart Workspace
	 *
	 * @return Form
	 */
	public function ItemEditForm() {
		return parent::ItemEditForm();
		// TODO: implement breadcrumbs, save, cancel/back buttons here
	}

	/**
	 * Saves the Flowchart Workspace
	 *
	 * @return Form
	 */
	public function doSave($data, $form) {
		//TODO: implement this
	}
}
