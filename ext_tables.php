<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if ('BE' === TYPO3_MODE) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'FluidTYPO3.Site',
        'tools',
        'txsiteM1',
        '',
        array(
            'Backend' => 'index,buildSite',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/' .
                (6.2 === (float) substr(TYPO3_version, 0, 3) ? 'site.gif' : 'module_site.png'),
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
        )
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'site',
        'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf'
    );

}
