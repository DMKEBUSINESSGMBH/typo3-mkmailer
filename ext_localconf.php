<?php

if (!defined('TYPO3')) {
    exit('Access denied.');
}

$tempPath = \Sys25\RnBase\Utility\Extensions::extPath('mkmailer');
require_once $tempPath.'services/ext_localconf.php';

// Einbindung einer PageTSConfig
\Sys25\RnBase\Utility\Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/mod1/pageTSconfig.txt">');

if (\Sys25\RnBase\Utility\Extensions::isLoaded('mklib')) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_mkmailer_scheduler_SendMails'] = [
        'extension' => 'mkmailer',
        'title' => 'LLL:EXT:mkmailer/Resources/Private/Language/Scheduler/locallang.xlf:scheduler_SendMails_name',
        'description' => 'LLL:EXT:mkmailer/Resources/Private/Language/Scheduler/locallang.xlf:scheduler_SendMails_taskinfo',
        'additionalFields' => 'tx_mkmailer_scheduler_SendMailsFieldProvider',
    ];
}
