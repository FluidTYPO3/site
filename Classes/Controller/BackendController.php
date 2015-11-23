<?php
namespace FluidTYPO3\Site\Controller;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Builder\Analysis\Fluid\TemplateAnalyzer;
use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Result\ParserResult;
use FluidTYPO3\Builder\Service\ExtensionService;
use FluidTYPO3\Builder\Service\SyntaxService;
use FluidTYPO3\Builder\Utility\ExtensionUtility;
use FluidTYPO3\Site\Service\EnterpriseLevelEnumeration;
use FluidTYPO3\Site\Service\KickStarterService;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class BackendController
 * @package FluidTYPO3\Site\Controller
 */
class BackendController extends ActionController {



    /**
     * @var KickStarterService
     */
    protected $kickStarterService;


    /**
     * @param KickStarterService $kickStarterService
     * @return void
     */
    public function injectKickStarterService(KickStarterService $kickStarterService) {
        $this->kickStarterService = $kickStarterService;
    }

    /**
     * @param string $view
     * @return void
     */
    public function indexAction($view = 'Index') {
        $extensions = [
            EnterpriseLevelEnumeration::BY_DEFAULT => EnterpriseLevelEnumeration::BY_DEFAULT,
            EnterpriseLevelEnumeration::MINIMALIST => EnterpriseLevelEnumeration::MINIMALIST,
            EnterpriseLevelEnumeration::SMALL => EnterpriseLevelEnumeration::SMALL,
            EnterpriseLevelEnumeration::MEDIUM => EnterpriseLevelEnumeration::MEDIUM,
            EnterpriseLevelEnumeration::LARGE => EnterpriseLevelEnumeration::LARGE
        ];

        $this->view->assign('csh', BackendUtility::wrapInHelp('site', 'modules'));
        $this->view->assign('view', $view);
        $this->view->assign('extensionSelectorOptions', $extensions);

    }

    /**
     * @param string $mass
     * @param bool $makeResources
     * @param bool $makeMountPoint
     * @param string $extensionKey
     * @param null $author
     * @param null $title
     * @param null $description
     * @param bool $useVhs
     * @param bool $useFluidcontentCore
     * @param bool $pages
     * @param bool $content
     * @param bool $backend
     * @param bool $controllers
     */
    public function buildSiteAction($mass = EnterpriseLevelEnumeration::BY_DEFAULT, $makeResources = TRUE, $makeMountPoint = TRUE, $extensionKey = NULL, $author = NULL, $title = NULL, $description = NULL, $useVhs = TRUE, $useFluidcontentCore = TRUE, $pages = TRUE, $content = TRUE, $backend = FALSE, $controllers = TRUE) {
        $view = 'buildSite';
        $this->view->assign('csh', BackendUtility::wrapInHelp('builder', 'modules'));
        $this->view->assign('view', $view);
        $output = $this->kickStarterService->generateFluidPoweredSite($mass, $makeResources, $makeMountPoint, $extensionKey, $author, $title, $description, $useVhs, $useFluidcontentCore, $pages, $content, $backend, $controllers);
        $this->view->assign('output', $output);
		// Note: remapping some arguments to match values that will be displayed in the receipt; display uses template from EXT:builder
		$attributes = array(
			'name' => array('value' => $extensionKey),
			'author' => array('value' => $author),
			'level' => array('value' => $level),
			'vhs' => array('value' => $useVhs),
			'pages' => array('value' => $pages),
			'content' => array('value' => $content),
			'backend' => array('value' => $backend),
			'controllers' => array('value' => $controllers),
		);
		$attributes['name'] = $extensionKey;
		$attributes['vhs'] = $useVhs;
		$this->view->assign('attributes', $attributes);
    }




}
