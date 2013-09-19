<?php

LeftAndMain::require_css('processmap/css/process.css');

LeftAndMain::require_javascript('processmap/javascript/process-admin.js');

if (class_exists('DefinitionAdmin')) {
	Object::add_extension('ProcessInfo', 'ProcessInfoExtension');
}


