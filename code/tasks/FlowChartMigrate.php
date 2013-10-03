<?php
/**
 * Converts old API where the flow chart data was saved in the database as 
 * "backslashed" json object. With the new API the ORM handles this automatically.
 */
class FlowChartMigrate extends BuildTask {
	
	public function run($request) {
		$nl = '<br />'.PHP_EOL;
		if(PHP_SAPI == 'cli') {
			$nl = PHP_EOL;
		}
		$flowchartPages = FlowchartPage::get();
		
		if($flowchartPages->Count() == 0) {
			echo "No pages to convert".$nl;
			return;
		}
		
		echo 'Will migrate '.$flowchartPages->count().' flowchart page(s) chart data:'.$nl.$nl;
		
		foreach($flowchartPages as $page) {
			$page->FlowchartData = stripslashes($page->FlowchartData);
			$page->write();
			echo 'Migrated "'.$page->Link().'"';
			if($page->isPublished()) {
				$page->doPublish();
				echo ' and republished it';
			}
			echo $nl;
		}
	}
}
