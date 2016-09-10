<?php
// Register composer autoloader
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException(
        'Could not find vendor/autoload.php, make sure you ran composer.'
    );
}

/** @var Composer\Autoload\ClassLoader $autoloader */
$autoloader = require __DIR__ . '/../vendor/autoload.php';
$autoloader->addPsr4('FluidTYPO3\\Site\\Tests\\Unit\\', __DIR__ . '/Unit/');
$autoloader->addPsr4('TYPO3\\CMS\\Core\\', __DIR__ . '/../vendor/typo3/cms/typo3/sysext/core/Classes/');
$autoloader->addPsr4('TYPO3\\CMS\\Core\\Tests\\', __DIR__ . '/../vendor/typo3/cms/typo3/sysext/core/Tests/');
$autoloader->addPsr4('TYPO3\\CMS\\Extbase\\', __DIR__ . '/../vendor/typo3/cms/typo3/sysext/extbase/Classes/');

\FluidTYPO3\Development\Bootstrap::initialize(
    $autoloader,
    array(
        'cache_core' => \FluidTYPO3\Development\Bootstrap::CACHE_PHP_NULL,
    )
);
