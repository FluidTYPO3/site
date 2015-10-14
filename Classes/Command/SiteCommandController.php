<?php
namespace FluidTYPO3\Site\Command;

/*
 * This file is part of the FluidTYPO3/Site project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Site\Service\EnterpriseLevelEnumeration;
use FluidTYPO3\Site\Service\KickStarterService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Class SiteCommandController
 * @package FluidTYPO3\Site\Command
 */
class SiteCommandController extends CommandController {

	/**
	 * @var KickStarterService
	 */
	protected $kickStarterService;
	/**
	 * @param KickStarterService $kickStarterService
	 * @return void
	 */
	public function injectExtensionService(KickStarterService $kickStarterService) {
		$this->kickStarterService = $kickStarterService;
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
	 * @return void
	 */
	public function kickStarterCommand($mass = EnterpriseLevelEnumeration::BY_DEFAULT, $makeResources = TRUE, $makeMountPoint = TRUE, $extensionKey = NULL, $author = NULL, $title = NULL, $description = NULL, $useVhs = TRUE, $useFluidcontentCore = TRUE, $pages = TRUE, $content = TRUE, $backend = FALSE, $controllers = TRUE) {
		$output = $this->kickStarterService->generateFluidPoweredSite($mass, $makeResources, $makeMountPoint, $extensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers);
		$this->outputLine($output);
	}
}
