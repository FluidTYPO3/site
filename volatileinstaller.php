<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/**
 * Class VolatileInstaller
 */
class VolatileInstaller {

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @param array $settings
	 * @param \TYPO3\CMS\Extensionmanager\Controller\ConfigurationController $controller
	 * @return void
	 */
	public function process(array $settings, \TYPO3\CMS\Extensionmanager\Controller\ConfigurationController $controller) {
		$this->settings = $this->remapSettings($settings);
		if (TRUE === isset($this->settings['kickstart']) && TRUE === (boolean) $this->settings['kickstart']) {
			if (TRUE === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($this->settings['extensionKey'])) {
				return;
			}
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['core'] = array(
				'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\NullBackend',
			);
			/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
			/** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
			$cacheManager = $objectManager->get('TYPO3\\CMS\\Core\\Cache\\CacheManager');
			$cacheManager->flushCaches();
			\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadConfigurationAndInitialize(TRUE);
			if (TRUE === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('css_styled_content')) {
				$this->deleteExtensionAndFiles('css_styled_content');
			}
			$this->kickstartProviderExtension($this->settings['extensionKey']);
			if (TRUE === (boolean) $this->settings['makeResources']) {
				$topPageUid = $this->createPageResources($this->settings['extensionKey']);
			} else {
				$topPageUid = reset($GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'pages', "pid = '0'"));
			}
			$this->createTypoScriptTemplate($topPageUid, $this->settings['extensionKey']);
			if (TRUE === (boolean) $this->settings['makeMountPoint']) {
				$this->createMountPoint($this->settings['extensionKey']);
			}
			$this->createDomainRecord($topPageUid);
			$copiedConfiguration = $this->copyFluidContentCoreConfiguration();
			if (FALSE === $copiedConfiguration) {
				$controller->addFlashMessage('The FluidcontentCore "AdditionalConfiguration.php" file was *NOT* copied to
				typo3conf. It appears you already have this file and we do not wish to overwrite it. Please consult the README
				.md file that is shipped with EXT:fluidcontent_core for manual install instructions!',
					'',
					\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
			}
			$this->cleanup();
			$controller->addFlashMessage('The steps you selected in your configuration have been performed: your Provider Extension
			 is now ready for use and can be accessed in the extension folder or through the file list module if you selected to
			 create a file mount point.');
			$controller->addFlashMessage('EXT:site self-destructed; re-download if you need to kickstart another site in this
			 TYPO3 installation!');
			header('Location: ?M=tools_ExtensionmanagerExtensionmanager&moduleToken=' . \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('moduleToken'));
			exit();
		}
	}

	/**
	 * @param array $settings
	 * @return array
	 */
	protected function remapSettings(array $settings) {
		$mapped = array();
		foreach ($settings as $name => $array) {
			$mapped[$name] = $array['value'];
		}
		return $mapped;
	}

	/**
	 * @return boolean
	 */
	protected function copyFluidContentCoreConfiguration() {
		$configuration = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('fluidcontent_core', 'Build/AdditionalConfiguration.php');
		$targetFile = PATH_site . 'typo3conf/AdditionalConfiguration.php';
		if (FALSE === file_exists($targetFile)) {
			copy($configuration, $targetFile);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @throws \TYPO3\CMS\Extensionmanager\Exception\ExtensionManagerException
	 */
	protected function cleanup() {
		$this->deleteExtensionAndFiles('site');
		if (FALSE === (boolean) $this->settings['keepBuilder']) {
			$this->deleteExtensionAndFiles('builder');
		}
	}

	/**
	 * @param string $extensionKey
	 * @return integer
	 */
	protected function createPageResources($extensionKey) {
		syslog(LOG_WARNING, 'Would generate four pages with template selections for ' . $extensionKey);
		$template = "'" . $extensionKey . "->MyPageTemplate'";
		$page1 = $this->createPageInsertionQuery(0, 'Front', 1, $template, $template);
		$GLOBALS['TYPO3_DB']->sql_query($page1);
		$topPageUid = (integer) reset($GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid', 'pages', '1=1', '', 'crdate DESC'));
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
		$query = <<< QUERY
INSERT INTO `pages` (`pid`, `tstamp`, `crdate`, `hidden`, `title`, `doktype`, `is_siteroot`, `backend_layout`,
`backend_layout_next_level`, `tx_fed_page_controller_action`, `tx_fed_page_controller_action_sub`)
VALUES (%d, %d, %d, 0, '%s', %s, 'fluidpages__fluidpages', 'fluidpages__fluidpages', %s, %s);
QUERY;
		$query = sprintf($query, $pid, time(), time(), $pageTitle, (string) $isRoot, (string) $isRoot, $selectedThisTemplate, $selectedSubTemplate);
		return $query;
	}

	/**
	 * @param integer $topPageUid
	 * @param string $extensionKey
	 * @return void
	 */
	protected function createTypoScriptTemplate($topPageUid, $extensionKey) {
		$query = <<< QUERY
INSERT INTO `sys_template` (`pid`, `tstamp`, `crdate`, `title`, `sitetitle`, `root`, `include_static_file`)
VALUES (%d, %d, %d, 'ROOT', 'My FluidTYPO3 site', 1, 'EXT:fluidcontent_core/Configuration/TypoScript, EXT:%s/Configuration/TypoScript');
QUERY;
		$query = sprintf($query, $topPageUid, time(), time(), $extensionKey);
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param integer $topPageUid
	 * @return void
	 */
	protected function createDomainRecord($topPageUid) {
		$query = <<< QUERY
INSERT INTO `sys_domain` (`pid`, `tstamp`, `crdate`, `domainName`)
VALUES (%d, %d, %d, '%s');
QUERY;
		$query = sprintf($query, $topPageUid, time(), time(), $_SERVER['SERVER_NAME']);
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param string $extensionKey
	 * @return void
	 */
	protected function createMountPoint($extensionKey) {
		$query = <<< QUERY
INSERT INTO `sys_file_storage` (`pid`, `tstamp`, `crdate`, `name`, `description`, `driver`, `configuration`, `is_default`, `is_browsable`, `is_public`, `is_writable`, `is_online`, `processingfolder`)
VALUES
	(0, %d, %d, '%s assets', 'Access to site asset files', 'Local', '<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"
	?>\n<T3FlexForms>\n    <data>\n        <sheet index=\"sDEF\">\n            <language index=\"lDEF\">\n                <field index=\"basePath\">\n                    <value index=\"vDEF\">%s</value>\n                </field>\n                <field index=\"pathType\">\n                    <value index=\"vDEF\">relative</value>\n                </field>\n                <field index=\"caseSensitive\">\n                    <value index=\"vDEF\">1</value>\n                </field>\n            </language>\n        </sheet>\n    </data>\n</T3FlexForms>', 0, 1, 1, 1, 1, NULL);
QUERY;
		$query = sprintf($query, time(), time(), $extensionKey, \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($extensionKey));
		$GLOBALS['TYPO3_DB']->sql_query($query);
	}

	/**
	 * @param string $extensionKey
	 */
	protected function deleteExtensionAndFiles($extensionKey) {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $installUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
		$installUtility->uninstall($extensionKey);
		if (FALSE !== strpos($extensionPath, 'typo3conf/ext/')) {
			system('rm -rf ' . escapeshellarg($extensionPath));
		}
	}

	/**
	 * @param string $extensionKey
	 * @throws Exception
	 * @internal param bool $keepBuilder
	 */
	protected function kickstartProviderExtension($extensionKey) {
		/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var \TYPO3\CMS\Extensionmanager\Utility\InstallUtility $installUtility */
		$installUtility = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
		/** @var \FluidTYPO3\Builder\Service\ExtensionService $extensionService */
		$extensionService = $objectManager->get('FluidTYPO3\\Builder\\Service\\ExtensionService');
		$extensionAuthorName = $GLOBALS['BE_USER']->user['name'];
		$extensionAuthorEmail = $GLOBALS['BE_USER']->user['email'];
		$extensionAuthor = sprintf('%s <%s>', $extensionAuthorName, $extensionAuthorEmail);
		$extensionTitle = 'Site templates';
		$description = 'Template files and assets for this site';
		$generator = $extensionService->buildProviderExtensionGenerator($extensionKey, $extensionAuthor, $extensionTitle, $description);
		$generator->generate();
		$installUtility->install($extensionKey);
	}

}
