<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'FluidTYPO3\\Site\\Command\\SiteCommandController';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['backend'] = serialize([
    'loginLogo' => 'EXT:site/Resources/Public/Images/logo.png',
    'loginHighlightColor' => '#17385E',
    'loginBackgroundImage' => 'EXT:site/Resources/Public/Images/background.png',
]);
