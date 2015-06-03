<?php

########################################################################
# Extension Manager/Repository config file for ext: "mkmailer"
#
# Auto generated 19-07-2009 13:45
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'MK Mailer',
	'description' => 'Provides a asynchronous mail system with full template support',
	'category' => 'services',
	'author' => 'René Nitzsche,Michael Wagner,Hannes Bochmann',
	'author_email' => 'dev@dmk-ebusiness.de',
	'version' => '1.0.6',
	'shy' => '',
	'dependencies' => 'rn_base',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_mkmailer/attachments,typo3temp/mkmailer',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'DMK E-BUSINESS GmbH',
	'constraints' => array(
		'depends' => array(
			'rn_base' => '0.14.17-0.0.0',
			'typo3' => '4.5.0-6.2.99'
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3users' => '',
			'mklib' => '0.9.70-0.0.0'
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"afa5";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"ae26";s:20:"doc/wizard_form.html";s:4:"b08b";}',
);

?>
