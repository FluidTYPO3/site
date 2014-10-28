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
	'description' => 'Single-use site kickstarting extension. Destroys itself after one use.',
	'category' => 'distribution',
	'version' => '1.1.3',
	'state' => 'beta',
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
			'typo3' => '6.2.0-6.2.99',
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
			'flux' => '7.1.0',
			'vhs' => '2.1.0',
			'fluidcontent' => '4.1.0',
			'fluidpages' => '3.1.0',
			'builder' => '0.12.0',
			'fluidcontent_core' => '1.0.0',
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

