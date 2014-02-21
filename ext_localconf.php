<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tempPath = t3lib_extMgm::extPath('mkmailer');
require_once($tempPath.'services/ext_localconf.php');

// Einbindung einer PageTSConfig
t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/mod1/pageTSconfig.txt">');
// Einbindung einer UserTSConfig
//t3lib_extMgm::addUserTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkmailer/mod1/userTSconfig.txt">');


?>
