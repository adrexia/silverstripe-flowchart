<?php
/*
 * Task creates 100 flowchart pages. Useful when testing.
 */
class MakePagesTask extends BuildTask {
	
	public function run($request) {
		$masterPage = false;
		// Group all pages under a page
		$masterPages = Page::get()->filter('Title', 'flowchart-holder');
		if($masterPages->count()) {
			$masterPage = $masterPages->first();
		}
		if(!$masterPage) {
			$masterPage = new Page();
			$masterPage->Title = 'flowchart-holder';
			$masterPage->write();
		}
		
		
		$numPages = 100;
		for($a=0; $a<$numPages; $a++) {
			$randomName = uniqid();
			$page = new FlowchartPage();
			$page->Title = 'flowchart-'.$randomName;
			$page->URLSegment = 'flowchart-'.$randomName;
			$page->ParentID = $masterPage->ID;
			$page->write();
			echo '- '.$page->Title.PHP_EOL;
		}
	}
	
}