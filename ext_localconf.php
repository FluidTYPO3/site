<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('site', 'VolatileInstaller.php');

if ('6.2' === TYPO3_branch) {
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
		'TYPO3\\CMS\\Extensionmanager\\Controller\\ConfigurationController',
		'afterExtensionConfigurationWrite',
		'VolatileInstaller',
		'processForSixTwo'
	);
} else {
	\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher')->connect(
		'TYPO3\\CMS\\Extensionmanager\\Controller\\ConfigurationController',
		'afterExtensionConfigurationWrite',
		'VolatileInstaller',
		'process'
	);
}
