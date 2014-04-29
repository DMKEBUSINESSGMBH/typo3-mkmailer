<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


require_once(t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php'));
tx_rnbase::load('tx_rnbase_util_TSDAM');

$TCA['tx_mkmailer_templates'] = array (
	'ctrl' => $TCA['tx_mkmailer_templates']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,mailtype,subject,description',
		'maxDBListItems' => '5'
	),
	'feInterface' => $TCA['tx_mkmailer_templates']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l18n_parent' => array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_mkmailer_templates',
				'foreign_table_where' => 'AND tx_mkmailer_templates.pid=###CURRENT_PID### AND tx_mkmailer_templates.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => array (
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'mailtype' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mailtype',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim,required',
			)
		),
		'subject' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_subject',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'contenttext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_contenttext',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'contenthtml' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_contenthtml',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_description',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'mail_from' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_from',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'mail_fromName' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_fromName',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'mail_bcc' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_bcc',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
			)
		),
		'templatetype' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype',
			'config' => Array (
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_0', 0),
					array('LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_1', 1),
					array('LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_2', 2),
					#array('LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_3', 3),
				)
			)
		),
// 		'attachments' => tx_mklib_util_TCA::getDamMediaTCAOnePic('attachments', array(
// 				'exclude' => 1,
// 				'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_attachments',
// 				'config' => array('allowed_types' => 'zip'),
// 			)
// 		),
		'attachments' => tx_rnbase_util_TSDAM::getMediaTCA('attachments'),
		'attachmentst3' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_attachments',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'GIF, JPG, JPEG, TIF, TIFF, BMP, PCX, TGA, PNG, PDF, AI, FLV, SWF, RTMP, MP3, RGG',
				'disallowed' => '',
				'uploadfolder' => 'uploads/tx_mkmailer/attachments',
				'size' => 5,
				'minitems' => 0,
 				'maxitems' => 10,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, '.
											'mailtype, subject, '.
											'contenthtml;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], contenttext, '.
											// je nachdem, ob dam installiert ist, das entsprechende feld darstellen
											(t3lib_extMgm::isLoaded('dam') ? 'attachments' : 'attachmentst3').', '.
											'description, mail_from,mail_fromName, mail_bcc, '.
											'templatetype')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>