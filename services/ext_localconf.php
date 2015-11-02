<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_mkmailer_util_ServiceRegistry');

t3lib_extMgm::addService($_EXTKEY,  'mkmailer' /* sv type */,  'tx_mkmailer_services_Mail' /* sv key */,
  array(
    'title' => 'Mailing service', 'description' => 'Service functions for email handling', 'subtype' => 'mail',
    'available' => TRUE, 'priority' => 50, 'quality' => 50,
    'os' => '', 'exec' => '',
    'classFile' => t3lib_extMgm::extPath($_EXTKEY).'services/class.tx_mkmailer_services_Mail.php',
    'className' => 'tx_mkmailer_services_Mail',
  )
);
