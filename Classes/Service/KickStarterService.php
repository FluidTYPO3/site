<?php
namespace FluidTYPO3\Site\Service;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

class KickStarterService implements SingletonInterface {

	/**
	 * Default extension key
	 */
	const DEFAULT_EXTENSION_KEY = 'Vendor.Myprovider';
	/**
	 * Default extension author
	 */
	const DEFAULT_AUTHOR = 'FluidTYPO3 user <info@fluidtypo3.org>';
	/**
	 * Default extension title
	 */
	const DEFAULT_EXTENSION_TITLE = 'Site templates';
	/**
	 * Default extension description
	 */
	const DEFAULT_EXTENSION_DESCRIPTION = 'Template files and assets for this site';

	/**
	 * Site "enterprise level". Preset collections of extensions
	 * which should NOT be installed in each "mass".
	 *
	 * @var array
	 */
	protected $extensionRemovals = array(

		EnterpriseLevelEnumeration::BY_DEFAULT => array(),
		EnterpriseLevelEnumeration::MINIMALIST => array(
			'about', 'aboutmodules', 'belog', 'beuser', 'context_help', 'felogin', 'form', 'impexp',
			'info_pagetsconfig', 'info', 'reports', 'setup', 'sys_note', 'viewpage', 'wizard_crpages', 'wizard_sortpages',
			 'func', 'documentation', 'lowlevel'
		),
		EnterpriseLevelEnumeration::SMALL => array(
			'about', 'aboutmodules', 'belog', 'beuser', 'context_help', 'impexp', 'info_pagetsconfig',
			'wizard_crpages', 'wizard_sortpages',  'func', 'documentation', 'lowlevel',
		),
		EnterpriseLevelEnumeration::MEDIUM => array(
			'about', 'aboutmodules', 'context_help', 'impexp', 'info_pagetsconfig', 'wizard_crpages', 'wizard_sortpages',
			 'func', 'documentation'
		),
		EnterpriseLevelEnumeration::LARGE => array(
			'about', 'aboutmodules', 'context_help', 'documentation'
		),
	);

	/**
	 * Site "enterprise level". Preset collections of extensions
	 * which SHOULD be installed in each "mass".
	 *
	 * @var array
	 */
	protected $extensionAdditions = array(
		EnterpriseLevelEnumeration::BY_DEFAULT => array(),
		EnterpriseLevelEnumeration::MINIMALIST => array(),
		EnterpriseLevelEnumeration::SMALL => array(),
		EnterpriseLevelEnumeration::MEDIUM => array('scheduler', 'recycler', 'filemetadata'),
		EnterpriseLevelEnumeration::LARGE => array('scheduler', 'taskcenter', 'sys_action', 'recycler', 'filemetadata', 'linkvalidator', 'opendocs'),
	);

	/**
	 * @var array
	 */
	protected $settings = array();


	/**
	 * Returns author value for extension
	 * @param $author
	 *
	 * @return string
	 */
	protected function getAuthor($author) {
		if (FALSE === empty($author)) {
			return $author;
		}
		if (TRUE === isset($GLOBALS['BE_USER']) && TRUE === isset($GLOBALS['BE_USER']->user['name'])) {
			return $GLOBALS['BE_USER']->user['name'] . TRUE === isset($GLOBALS['BE_USER']->user['email']) ? '<' . $GLOBALS['BE_USER']->user['email'] . '>' : '';
		}

		return self::DEFAULT_AUTHOR;
	}

	/**
	 * Prepare values to avoid problems
	 *
	 * @param string $mass [default,minimalist,small,medium,large] Site enterprise level: If you wish, select the expected size of your site here. Depending on your selection, system extensions will be installed or uninstalled to create a sane default extension collection that suits your site. The "medium" option is approximately the same as the default except without the documentation-related extensions. Choose "large" if your site is targed at multiple editors, languages or otherwise requires many CMS features.
	 * @param boolean $makeResources Create resources: Check this checkbox to create a top-level page with preset template selections, three sub-pages, one domain record based on the current host name you use and one root TypoScript record in which the necessary static TypoScript templates are pre-included.
	 * @param boolean $makeMountPoint Create FAL mount point: Check this to create a file system mount point allowing you to access your templates and asset files using the "File list" module" and reference your templates and asset files from "file" type fields in your pages and content properties.
	 * @param string $extensionKey [ext:builder] The extension key which should be generated. Must not exist.
	 * @param string $author [ext:builder] The author of the extension, in the format "Name Lastname <name@example.com>" with optional company name, in which case form is "Name Lastname <name@example.com>, Company Name"
	 * @param string $title [ext:builder] The title of the resulting extension, by default "Provider extension for $enabledFeaturesList"
	 * @param string $description [ext:builder] The description of the resulting extension, by default "Provider extension for $enabledFeaturesList"
	 * @param boolean $useVhs [ext:builder] If TRUE, adds the VHS extension as dependency - recommended, on by default
	 * @param boolean $useFluidcontentCore [ext:builder] If TRUE, adds the FluidcontentCore extension as dependency - recommended, on by default
	 * @param boolean $pages [ext:builder] If TRUE, generates basic files for implementing Fluid Page templates
	 * @param boolean $content [ext:builder] IF TRUE, generates basic files for implementing Fluid Content templates
	 * @param boolean $backend [ext:builder] If TRUE, generates basic files for implementing Fluid Backend modules
	 * @param boolean $controllers [ext:builder] If TRUE, generates controllers for each enabled feature. Enabling $backend will always generate a controller regardless of this toggle.
	 *
	 * @return array
	 */
	protected function prepareSettings($mass, $makeResources, $makeMountPoint, $extensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers) {
		return array($mass, (boolean) $makeResources, (boolean) $makeMountPoint, FALSE === is_null($extensionKey) ? $extensionKey : self::DEFAULT_EXTENSION_KEY, $this->getAuthor($author), FALSE === empty($title) ? $title : self::DEFAULT_EXTENSION_TITLE, FALSE === empty($description) ? $description : self::DEFAULT_EXTENSION_DESCRIPTION, (boolean) $useVhs, (boolean) $useFluidcontentCore, (boolean) $pages, (boolean) $content, (boolean) $backend, (boolean) $controllers);
	}

	/**
	 * Gerenates a Fluid Powered TYPO3 based site. Welcome to our world.
	 *
	 * @param string $mass [default,minimalist,small,medium,large] Site enterprise level: If you wish, select the expected size of your site here. Depending on your selection, system extensions will be installed or uninstalled to create a sane default extension collection that suits your site. The "medium" option is approximately the same as the default except without the documentation-related extensions. Choose "large" if your site is targed at multiple editors, languages or otherwise requires many CMS features.
	 * @param boolean $makeResources Create resources: Check this checkbox to create a top-level page with preset template selections, three sub-pages, one domain record based on the current host name you use and one root TypoScript record in which the necessary static TypoScript templates are pre-included.
	 * @param boolean $makeMountPoint Create FAL mount point: Check this to create a file system mount point allowing you to access your templates and asset files using the "File list" module" and reference your templates and asset files from "file" type fields in your pages and content properties.
	 * @param string $extensionKey [ext:builder] The extension key which should be generated. Must not exist.
	 * @param string $author [ext:builder] The author of the extension, in the format "Name Lastname <name@example.com>" with optional company name, in which case form is "Name Lastname <name@example.com>, Company Name"
	 * @param string $title [ext:builder] The title of the resulting extension, by default "Provider extension for $enabledFeaturesList"
	 * @param string $description [ext:builder] The description of the resulting extension, by default "Provider extension for $enabledFeaturesList"
	 * @param boolean $useVhs [ext:builder] If TRUE, adds the VHS extension as dependency - recommended, on by default
	 * @param boolean $useFluidcontentCore [ext:builder] If TRUE, adds the FluidcontentCore extension as dependency - recommended, on by default
	 * @param boolean $pages [ext:builder] If TRUE, generates basic files for implementing Fluid Page templates
	 * @param boolean $content [ext:builder] IF TRUE, generates basic files for implementing Fluid Content templates
	 * @param boolean $backend [ext:builder] If TRUE, generates basic files for implementing Fluid Backend modules
	 * @param boolean $controllers [ext:builder] If TRUE, generates controllers for each enabled feature. Enabling $backend will always generate a controller regardless of this toggle.
	 * @return string|NULL
	 */
	public function generateFluidPoweredSite($mass = EnterpriseLevelEnumeration::BY_DEFAULT, $makeResources = TRUE, $makeMountPoint = TRUE, $extensionKey = NULL, $author = NULL, $title = NULL, $description = NULL, $useVhs = TRUE, $useFluidcontentCore = TRUE, $pages = TRUE, $content = TRUE, $backend = FALSE, $controllers = TRUE) {
		list($mass, $makeResources, $makeMountPoint, $extensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers) = $this->prepareSettings($mass, $makeResources, $makeMountPoint, $extensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers);

		$vendorExtensionKey = $extensionKey;
		$extensionKey = $this->getExtensionKeyFromVendorExtensionKey($extensionKey);

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['core'] = array(
			'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\NullBackend',
		);

		if (TRUE === ExtensionManagementUtility::isLoaded('css_styled_content')) {
			$this->deleteExtensionAndFiles('css_styled_content');
		}

		if (FALSE === ExtensionManagementUtility::isLoaded($extensionKey)) {
			$this->kickstartProviderExtension($extensionKey, $vendorExtensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers);
		}

		if (TRUE === $makeResources) {
			$topPageUid = $this->createPageResources($vendorExtensionKey);
		} else {
			$topPageUid = reset($GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'pages', "pid = '0'"));
		}
		$this->createTypoScriptTemplate($topPageUid, self::DEFAULT_EXTENSION_TITLE, $extensionKey);
		if (TRUE === $makeMountPoint) {
			$this->createMountPoint($extensionKey);
		}
		$this->createDomainRecord($topPageUid);

		foreach (array_reverse($this->extensionRemovals[EnterpriseLevelEnumeration::MINIMALIST]) as $installExtensionKey) {
			if (FALSE === ExtensionManagementUtility::isLoaded($installExtensionKey)) {
				$this->installExtension($installExtensionKey);
			}
		}

		foreach ($this->extensionRemovals[$mass] as $removeExtensionKey) {
			$this->uninstallExtension($removeExtensionKey);
		}
		foreach ($this->extensionAdditions[$mass] as $installExtensionKey) {
			$this->installExtension($installExtensionKey);
		}

		return 'The steps you selected in your configuration have been performed: your Provider Extension is now ready for use and can be accessed in the extension folder or through the file list module if you selected to create a file mount point.';
	}

	/**
	 * @param string $extensionKey
	 * @return integer
	 */
	protected function createPageResources($extensionKey, $defaultTemplate = 'standard') {
		syslog(LOG_WARNING, 'Would generate four pages with template selections for ' . $extensionKey);
		$template = "'" . $extensionKey . '->' . $defaultTemplate . "'";
		$page1 = $this->createPageInsertionQuery(0, 'Front', 1, $template, $template);
		$GLOBALS['TYPO3_DB']->sql_query($page1);
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'pages', '1=1', '', 'crdate DESC');
		$topPageUid = reset($pages);
		$page2 = $this->createPageInsertionQuery($topPageUid, 'Page 1', 0, "''", "''");
		$page3 = $this->createPageInsertionQuery($topPageUid, 'Page 2', 0, "''", "''");
		$page4 = $this->createPageInsertionQuery($topPageUid, 'Page 3', 0, "''", "''");
		$GLOBALS['TYPO3_DB']->sql_query($page2);
		$GLOBALS['TYPO3_DB']->sql_query($page3);
		$GLOBALS['TYPO3_DB']->sql_query($page4);
		return $topPageUid;
	}

	/**
	 * @param integer $pid
	 * @param string $pageTitle
	 * @param integer $isRoot
	 * @param string $selectedThisTemplate
	 * @param string $selectedSubTemplate
	 * @return string
	 */
	protected function createPageInsertionQuery($pid, $pageTitle, $isRoot, $selectedThisTemplate, $selectedSubTemplate) {
		$query = 'INSERT INTO `pages` (`pid`, `tstamp`, `crdate`, `hidden`, `title`, `doktype`, `is_siteroot`, `backend_layout`, `backend_layout_next_level`, `tx_fed_page_controller_action`, `tx_fed_page_controller_action_sub`) VALUES (%d, %d, %d, 1, \'%s\', %s, %s, \'fluidpages__fluidpages\', \'fluidpages__fluidpages\', %s, %s);';
		$query = sprintf($query, $pid, time(), time(), $pageTitle, (string) $isRoot, (string) $isRoot, $selectedThisTemplate, $selectedSubTemplate);
		return $query;
	}

	/**
	 * @param integer $topPageUid
	 * @param string $extensionKey
	 * @return void
	 */
	protected function createTypoScriptTemplate($topPageUid, $title, $extensionKey) {
		$query = 'INSERT INTO `sys_template` (`pid`, `tstamp`, `crdate`, `title`, `sitetitle`, `root`, `include_static_file`) VALUES (%d, %d, %d, \'ROOT\', \'%s\', 1, \'EXT:fluidcontent_core/Configuration/TypoScript, EXT:%s/Configuration/TypoScript\');';
		$query = sprintf($query, $topPageUid, time(), time(), $title, $extensionKey);
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param integer $topPageUid
	 * @return void
	 */
	protected function createDomainRecord($topPageUid) {
		$query = 'INSERT INTO `sys_domain` (`pid`, `tstamp`, `crdate`, `domainName`) VALUES (%d, %d, %d, \'%s\');';
		$query = sprintf($query, $topPageUid, time(), time(), $_SERVER['SERVER_NAME']);
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param string $extensionKey
	 * @return void
	 */
	protected function createMountPoint($extensionKey) {
		$query = 'INSERT INTO `sys_file_storage` (`pid`, `tstamp`, `crdate`, `name`, `description`, `driver`, `configuration`, `is_default`, `is_browsable`, `is_public`, `is_writable`, `is_online`, `processingfolder`) VALUES 	(0, %d, %d, \'%s assets\', \'Access to site asset files\', \'Local\', \'<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"	?>\n<T3FlexForms>\n    <data>\n        <sheet index=\"sDEF\">\n            <language index=\"lDEF\">\n                <field index=\"basePath\">\n                    <value index=\"vDEF\">%s</value>\n                </field>\n                <field index=\"pathType\">\n                    <value index=\"vDEF\">relative</value>\n                </field>\n                <field index=\"caseSensitive\">\n                    <value index=\"vDEF\">1</value>\n                </field>\n            </language>\n        </sheet>\n    </data>\n</T3FlexForms>\', 0, 1, 1, 1, 1, NULL);';
		$query = sprintf($query, time(), time(), $extensionKey, ExtensionManagementUtility::siteRelPath($extensionKey));
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param string $extensionKey
	 */
	protected function deleteExtensionAndFiles($extensionKey) {
		$extensionPath = ExtensionManagementUtility::extPath($extensionKey);
		$this->uninstallExtension($extensionKey);
		if (FALSE !== strpos($extensionPath, 'typo3conf/ext/')) {
			system('rm -rf ' . escapeshellarg($extensionPath));
		}
	}

	/**
	 * @param string $extensionKey
	 */
	protected function uninstallExtension($extensionKey) {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $installUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$installUtility->uninstall($extensionKey);
	}

	/**
	 * @param string $extensionKey
	 */
	protected function installExtension($extensionKey) {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $installUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$installUtility->install($extensionKey);
	}

	/**
	 * @param string $extensionKey
	 * @throws \Exception
	 * @internal param bool $keepBuilder
	 */
	protected function kickstartProviderExtension($extensionKey, $vendorExtensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers) {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$extensions = $this->gatherInformation();

		if (FALSE === array_key_exists($extensionKey, $extensions)) {
			/** @var \FluidTYPO3\Builder\Service\ExtensionService $extensionService */
			$extensionService = $objectManager->get('FluidTYPO3\\Builder\\Service\\ExtensionService');
			$generator = $extensionService->buildProviderExtensionGenerator($vendorExtensionKey, $author, $title, $description, $controllers, $pages, $content, $backend, $useVhs, $useFluidcontentCore);
			$generator->generate();
		}

		/** @var ListUtility $service */
		$service = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ListUtility');
		$service->reloadAvailableExtensions();

		/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $installUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$installUtility->install($extensionKey);
	}

	/**
	 * @return string
	 */
	protected function getExtensionKeyFromVendorExtensionKey($extensionKey) {
		if (FALSE !== strpos($extensionKey, '.')) {
			$extensionKey = array_pop(explode('.', $extensionKey));
		}
		return GeneralUtility::camelCaseToLowerCaseUnderscored($extensionKey);
	}


	/**
	 * Gathers Extension Information
	 *
	 * @return array
	 */
	private function gatherInformation() {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var ListUtility $service */
		$service = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ListUtility');

		$extensionInformation = $service->getAvailableExtensions();
		foreach ($extensionInformation as $extensionKey => $info) {
			if (TRUE === array_key_exists($extensionKey, $GLOBALS['TYPO3_LOADED_EXT'])) {
				$extensionInformation[$extensionKey]['installed'] = 1;
			} else {
				$extensionInformation[$extensionKey]['installed'] = 0;
			}
		}
		return $extensionInformation;
	}
}
