<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

// Iconregistrieren
\Sys25\RnBase\Backend\Utility\Icons::getIconRegistry()->registerIcon(
    'ext-mkmailer-wizard-icon',
    'TYPO3\\CMS\Core\\Imaging\\IconProvider\\BitmapIconProvider',
    ['source' => 'EXT:mkmailer/Resources/Public/Icons/Extension.gif']
);
// Wizardkonfiguration hinzufügen
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/Configuration/TSconfig/ContentElementWizard.txt">'
);

// register user_MkmailerBackend
\Sys25\RnBase\Utility\Extensions::registerModule(
    'mkmailer',
    'web',
    'backend',
    'bottom',
    [],
    [
        'access' => 'user,group',
        'routeTarget' => 'tx_mkmailer_mod1_Module',
        'icon' => 'EXT:mkmailer/Resources/Public/Icons/moduleicon.png',
        'labels' => 'LLL:EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf',
    ]
);

\Sys25\RnBase\Utility\Extensions::insertModuleFunction(
    'web_MkmailerBackend',
    'tx_mkmailer_mod1_FuncOverview',
    \Sys25\RnBase\Utility\Extensions::extPath('mkmailer', 'mod1/class.tx_mkmailer_mod1_FuncOverview.php'),
    'LLL:EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf:func_overview'
);
