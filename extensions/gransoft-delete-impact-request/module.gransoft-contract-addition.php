<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'gransoft-delete-impact-request/1.0.0',
	array(
		// Identification
		//
		'label' => 'Test extension',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-request-mgmt/3.0.2',
			'itop-bridge-cmdb-ticket/3.0.2',
			'itop-tickets/2.4.0',
			'itop-profiles-itil/2.3.0',
		),
		'mandatory' => false,
		'visible' => true,

		// Components
		//
		'datamodel' => array(
			'model.gransoft-contract-addition.php',
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);


?>
