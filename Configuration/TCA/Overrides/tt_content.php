<?php

if (!defined('TYPO3')) {
    exit('Access denied.');
}

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tx_mkmailer'] = 'layout,select_key,pages';

// Das tt_content-Feld pi_flexform einblenden
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tx_mkmailer'] = 'pi_flexform';

\Sys25\RnBase\Utility\Extensions::addPiFlexFormValue('tx_mkmailer', 'FILE:EXT:mkmailer/flexform_main.xml');
\Sys25\RnBase\Utility\Extensions::addPlugin(
    ['LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:plugin.mkmailer.label', 'tx_mkmailer'],
    'list_type',
    'mkmailer'
);
