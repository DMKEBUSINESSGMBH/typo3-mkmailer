<?php

if (!defined('TYPO3')) {
    exit('Access denied.');
}

\Sys25\RnBase\Utility\Extensions::addService(
    'mkmailer',
    'mkmailer' /* sv type */ ,
    'tx_mkmailer_services_Mail' /* sv key */ ,
    [
    'title' => 'Mailing service', 'description' => 'Service functions for email handling', 'subtype' => 'mail',
    'available' => true, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => \Sys25\RnBase\Utility\Extensions::extPath('mkmailer').'services/class.tx_mkmailer_services_Mail.php',
    'className' => 'tx_mkmailer_services_Mail',
    ]
);
