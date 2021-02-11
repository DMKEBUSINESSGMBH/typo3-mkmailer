<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

$tempPath = tx_rnbase_util_Extensions::extPath('mkmailer');
require_once $tempPath.'services/ext_localconf.php';

// Einbindung einer PageTSConfig
tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/mod1/pageTSconfig.txt">');

tx_rnbase::load('tx_rnbase_util_TYPO3');
if (TYPO3_MODE == 'BE' &&
    tx_rnbase_util_Extensions::isLoaded('mklib') &&
    tx_rnbase_util_TYPO3::isTYPO62OrHigher()
) {
    tx_rnbase::load('tx_mkmailer_scheduler_SendMails');
    tx_rnbase::load('tx_mkmailer_scheduler_SendMailsFieldProvider');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_mkmailer_scheduler_SendMails'] = [
        'extension' => 'mkmailer',
        'title' => 'LLL:EXT:mkmailer/scheduler/locallang.xml:scheduler_SendMails_name',
        'description' => 'LLL:EXT:mkmailer/scheduler/locallang.xml:scheduler_SendMails_taskinfo',
        'additionalFields' => 'tx_mkmailer_scheduler_SendMailsFieldProvider',
    ];
}
