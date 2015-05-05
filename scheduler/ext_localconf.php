<?php
if (!defined ('TYPO3_MODE')) {
   die ('Access denied.');
}

tx_rnbase::load('tx_rnbase_util_TYPO3');
if (
	TYPO3_MODE == 'BE' &&
	t3lib_extMgm::isLoaded('mklib') &&
	tx_rnbase_util_TYPO3::isTYPO62OrHigher()
) {
	tx_rnbase::load('tx_mkmailer_scheduler_SendMails');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_mkmailer_scheduler_SendMails'] = array(
		'extension'		=> 'mkmailer',
		'title'			=> 'LLL:EXT:mkmailer/scheduler/locallang.xml:scheduler_SendMails_name',
		'description'	  => 'LLL:EXT:mkmailer/scheduler/locallang.xml:scheduler_SendMails_taskinfo',
	);
}
