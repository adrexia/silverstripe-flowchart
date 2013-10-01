<?php
/**
 * Graphical interface for creating basic flowcharts
 */
class FlowchartAdmin extends ModelAdmin {

 	private static $managed_models = array('FlowchartPage','FlowState');

	private static $url_segment = 'flowcharts';

	private static $menu_title = 'Flowcharts';

	private static $menu_icon = "flowchart/images/flowchart.png";

	private static $tree_class = 'SS_Flowchart';

	public function getEditForm($id = null, $fields = null){
		$form = parent::getEditForm($id, $fields);
		$gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
		$config = $gridField->getConfig();

		if($this->sanitiseClassName($this->modelClass) == "FlowchartPage"){
			$config->removeComponentsByType('GridFieldDetailForm');
			$config->addComponent(new GridFieldFlowchartDetailForm());
		} else if($this->sanitiseClassName($this->modelClass) == "FlowState"){
			$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
				singleton($this->sanitiseClassName($this->modelClass))->getCurrentDisplayFields()
			);
		}
		return $form;
	}
}
