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
	'version' => '1.7.0',
	'state' => 'stable',
	'uploadfolder' => TRUE,
	'createDirs' => '',
	'clearCacheOnLoad' => TRUE,
	'author' => 'FluidTYPO3 Team',
	'author_email' => 'claus@namelesscoder.net',
	'author_company' => '',
	'constraints' => array (
		'depends' => array (
			'typo3' => '7.6.0-8.3.99',
			'flux' => '',
			'vhs' => '',
			'fluidcontent' => '',
			'fluidpages' => '',
			'builder' => '',
		),
		'conflicts' => array (
		),
		'suggests' => array (
			'fluid_styled_content' => '',
		),
	),
);

