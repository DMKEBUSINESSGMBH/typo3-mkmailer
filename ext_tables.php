<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

if (!tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
    $GLOBALS['TCA']['tx_mkmailer_templates'] = require tx_rnbase_util_Extensions::extPath(
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

tx_rnbase_util_Extensions::addPiFlexFormValue('tx_mkmailer', 'FILE:EXT:mkmailer/flexform_main.xml');
tx_rnbase_util_Extensions::addPlugin(
    ['LLL:EXT:mkmailer/locallang_db.php:plugin.mkmailer.label', 'tx_mkmailer'],
    'list_type',
    'mkmailer'
);

if (TYPO3_MODE == 'BE') {
    // Add plugin wizards
    tx_rnbase::load('tx_rnbase_util_TYPO3');
    if (!tx_rnbase_util_TYPO3::isTYPO80OrHigher()) {
        tx_rnbase::load('tx_mkmailer_util_wizicon');
        tx_mkmailer_util_wizicon::addWizicon(
            'tx_mkmailer_util_wizicon',
            tx_rnbase_util_Extensions::extPath(
                'mkmailer',
                'util/class.tx_mkmailer_util_wizicon.php'
            )
        );
    } else {
        // Iconregistrieren
        Tx_Rnbase_Backend_Utility_Icons::getIconRegistry()->registerIcon(
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
    tx_rnbase::load('tx_mkmailer_mod1_Module');
    tx_rnbase_util_Extensions::registerModule(
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

    tx_rnbase::load('tx_mkmailer_mod1_FuncOverview');
    tx_rnbase_util_Extensions::insertModuleFunction(
        'web_MkmailerBackend',
        'tx_mkmailer_mod1_FuncOverview',
        tx_rnbase_util_Extensions::extPath('mkmailer', 'mod1/class.tx_mkmailer_mod1_FuncOverview.php'),
        'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_overview'
    );

//     tx_rnbase_util_Extensions::insertModuleFunction(
//         'user_MkmailerBackend',
//         'tx_mkmailer_mod1_FuncTest',
//         tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod1/class.tx_mkmailer_mod1_FuncTest.php',
//         'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_test'
//     );
}

tx_rnbase_util_Extensions::addStaticFile($_EXTKEY, 'static/ts/', 'MK Mailer');
