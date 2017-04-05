<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
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
tx_rnbase_util_Extensions::addPlugin(array('LLL:EXT:mkmailer/locallang_db.php:plugin.mkmailer.label', 'tx_mkmailer'));

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
			array('source' => 'EXT:mkmailer/ext_icon.gif')
		);
		// Wizardkonfiguration hinzufügen
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
			'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/Configuration/TSconfig/ContentElementWizard.txt">'
		);
	}

	// Einbindung des eigentlichen BE-Moduls. Dieses bietet eine Hülle für die eigentlichen Modulfunktionen
	tx_rnbase_util_Extensions::addModule('user', 'txmkmailerM1', '', tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod1/');

	// Achtung: Damit die Einbindung klappt muss das Hauptmodul folgende Methode aufrufen
	// $SOBE->checkExtObj();
	tx_rnbase_util_Extensions::insertModuleFunction(
		'user_txmkmailerM1',
		'tx_mkmailer_mod1_FuncOverview',
		tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod1/class.tx_mkmailer_mod1_FuncOverview.php',
		'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_overview'
	);
// 	tx_rnbase_util_Extensions::insertModuleFunction(
// 		'user_txmkmailerM1',
// 		'tx_mkmailer_mod1_FuncTest',
// 		tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod1/class.tx_mkmailer_mod1_FuncTest.php',
// 		'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_test'
// 	);
}

tx_rnbase_util_Extensions::addStaticFile($_EXTKEY, 'static/ts/', 'MK Mailer');
