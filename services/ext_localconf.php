<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

tx_rnbase::load('tx_mkmailer_util_ServiceRegistry');

tx_rnbase_util_Extensions::addService(
    $_EXTKEY,
    'mkmailer' /* sv type */ ,
    'tx_mkmailer_services_Mail' /* sv key */ ,
    [
    'title' => 'Mailing service', 'description' => 'Service functions for email handling', 'subtype' => 'mail',
    'available' => true, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'services/class.tx_mkmailer_services_Mail.php',
    'className' => 'tx_mkmailer_services_Mail',
    ]
);
