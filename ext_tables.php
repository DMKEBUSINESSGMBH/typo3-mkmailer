<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_mkmailer_templates'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates',
		'label' => 'mailtype',
		'label_alt' => 'description',
		'label_alt_force' => 1,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby' => 'ORDER BY mailtype',
		'delete' => 'deleted',
		'enablecolumns' => array (
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_mkmailer_templates.gif',
// 		'dividers2tabs' => 1,
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource',
	)
);

////////////////////////////////
// Plugin anmelden
////////////////////////////////
// Einige Felder ausblenden
$TCA['tt_content']['types']['list']['subtypes_excludelist']['tx_mkmailer']='layout,select_key,pages';

// Das tt_content-Feld pi_flexform einblenden
$TCA['tt_content']['types']['list']['subtypes_addlist']['tx_mkmailer']='pi_flexform';

t3lib_extMgm::addPiFlexFormValue('tx_mkmailer','FILE:EXT:'.$_EXTKEY.'/flexform_main.xml');
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:plugin.mkmailer.label','tx_mkmailer'));

if (TYPO3_MODE == 'BE') {
	# Add plugin wizards
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mkmailer_util_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'util/class.tx_mkmailer_util_wizicon.php';

	// Einbindung des eigentlichen BE-Moduls. Dieses bietet eine Hülle für die eigentlichen Modulfunktionen
	t3lib_extMgm::addModule('user', 'txmkmailerM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

	// Achtung: Damit die Einbindung klappt muss das Hauptmodul folgende Methode aufrufen
	// $SOBE->checkExtObj();
	t3lib_extMgm::insertModuleFunction('user_txmkmailerM1','tx_mkmailer_mod1_FuncOverview',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_mkmailer_mod1_FuncOverview.php',
		'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_overview'
	);
	t3lib_extMgm::insertModuleFunction('user_txmkmailerM1','tx_mkmailer_mod1_FuncTest',
		t3lib_extMgm::extPath($_EXTKEY).'mod1/class.tx_mkmailer_mod1_FuncTest.php',
		'LLL:EXT:mkmailer/mod1/locallang_mod.xml:func_test'
	);
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/', 'MK Mailer');
?>