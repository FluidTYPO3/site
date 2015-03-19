<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "site".
 *
 * Auto generated 18-10-2014 01:10
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'FluidTYPO3 site kickstarter',
	'description' => 'Single-use site kickstarting extension - destroys itself after one use. To use: install, click configure, adjust and save. Note: may take a few minutes to finish installing after clicking install, please have patience.',
	'category' => 'distribution',
	'version' => '1.3.3',
	'state' => 'stable',
	'uploadfolder' => true,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'FluidTYPO3 Team',
	'author_email' => 'claus@namelesscoder.net',
	'author_company' => '',
	'constraints' =>
	array (
		'depends' =>
		array (
			'typo3' => '6.2.0-7.99.99',
			'flux' => '7.1.0-7.2.99',
			'vhs' => '2.1.0-2.3.99',
			'fluidcontent' => '4.1.0-4.2.99',
			'fluidpages' => '3.1.0-3.2.99',
			'builder' => '0.12.0-1.99.99',
			'fluidcontent_core' => '1.0.0-1.99.99',
		),
		'conflicts' =>
		array (
			'css_styled_content' => '',
		),
		'suggests' =>
		array (
		),
	),
);

