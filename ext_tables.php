<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

if (!\Sys25\RnBase\Utility\TYPO3::isTYPO60OrHigher()) {
    $GLOBALS['TCA']['tx_mkmailer_templates'] = require \Sys25\RnBase\Utility\Extensions::extPath(
        'mkmailer',
        'Configuration/TCA/tx_mkmailer_templates.php'
    );
}

////////////////////////////////
// Plugin anmelden
////////////////////////////////
// Einige Felder ausblenden
$TCA['tt_content']['types']['list']['subtypes_excludelist']['tx_mkmailer'] = 'layout,select_key,pages';

// Das tt_content-Feld pi_flexform einblenden
$TCA['tt_content']['types']['list']['subtypes_addlist']['tx_mkmailer'] = 'pi_flexform';

\Sys25\RnBase\Utility\Extensions::addPiFlexFormValue('tx_mkmailer', 'FILE:EXT:mkmailer/flexform_main.xml');
\Sys25\RnBase\Utility\Extensions::addPlugin(
    ['LLL:EXT:mkmailer/locallang_db.php:plugin.mkmailer.label', 'tx_mkmailer'],
    'list_type',
    'mkmailer'
);

if (TYPO3_MODE == 'BE') {
    // Add plugin wizards
    if (!\Sys25\RnBase\Utility\TYPO3::isTYPO80OrHigher()) {
        tx_mkmailer_util_wizicon::addWizicon(
            'tx_mkmailer_util_wizicon',
            \Sys25\RnBase\Utility\Extensions::extPath(
                'mkmailer',
                'util/class.tx_mkmailer_util_wizicon.php'
            )
        );
    } else {
        // Iconregistrieren
        \Sys25\RnBase\Backend\Utility\Icons::getIconRegistry()->registerIcon(
            'ext-mkmailer-wizard-icon',
            'TYPO3\\CMS\Core\\Imaging\\IconProvider\\BitmapIconProvider',
            ['source' => 'EXT:mkmailer/ext_icon.gif']
        );
        // Wizardkonfiguration hinzufügen
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/Configuration/TSconfig/ContentElementWizard.txt">'
        );
    }

    // register user_MkmailerBackend
    \Sys25\RnBase\Utility\Extensions::registerModule(
        'mkmailer',
        'web',
        'backend',
        'bottom',
        [
        ],
        [
            'access' => 'user,group',
            'routeTarget' => 'tx_mkmailer_mod1_Module',
            'icon' => 'EXT:mkmailer/mod1/moduleicon.png',
            'labels' => 'LLL:EXT:mkmailer/mod1/locallang_mod.xml',
        ]
    );

    \Sys25\RnBase\Utility\Extensions::insertModuleFunction(
        'web_MkmailerBackend',
        'tx_mkmailer_mod1_FuncOverview',
        \Sys25\RnBase\Utility\Extensions::extPath('mkmailer', 'mod1/class.tx_mkmailer_mod1_FuncOverview.php'),
        'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_overview'
    );

//     \Sys25\RnBase\Utility\Extensions::insertModuleFunction(
//         'user_MkmailerBackend',
//         'tx_mkmailer_mod1_FuncTest',
//         \Sys25\RnBase\Utility\Extensions::extPath('mkmailer') . 'mod1/class.tx_mkmailer_mod1_FuncTest.php',
//         'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_test'
//     );
}

\Sys25\RnBase\Utility\Extensions::addStaticFile('mkmailer', 'static/ts/', 'MK Mailer');
