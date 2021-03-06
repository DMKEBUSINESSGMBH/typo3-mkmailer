<?php

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
tx_rnbase::load('tx_rnbase_util_TYPO3');

if (tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
    tx_rnbase::load('tx_rnbase_util_TSFAL');
    $attachementsTca = tx_rnbase_util_TSFAL::getMediaTCA(
        'attachments',
        ['config' => ['softref' => 'images']]
    );
} else {
    tx_rnbase::load('tx_rnbase_util_TSDAM');
    $attachementsTca = tx_rnbase_util_TSDAM::getMediaTCA(
        'attachments',
        ['config' => ['softref' => 'images']]
    );
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates',
        'label' => 'mailtype',
        'label_alt' => 'description',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'default_sortby' => 'ORDER BY mailtype',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'iconfile' => 'EXT:mkmailer/icon_tx_mkmailer_templates.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,mailtype,subject,description',
        'maxDBListItems' => '5',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xml:LGL.default_value',
                        0,
                    ],
                ],
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        '',
                        0,
                    ],
                ],
                'foreign_table' => 'tx_mkmailer_templates',
                'foreign_table_where' => 'AND tx_mkmailer_templates.pid=###CURRENT_PID### AND tx_mkmailer_templates.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'mailtype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mailtype',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim,required',
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_subject',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'contenttext' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_contenttext',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
            ],
        ],
        'contenthtml' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_contenthtml',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'wizards' => Tx_Rnbase_Utility_TcaTool::getWizards(
                    '',
                    ['RTE' => true]
                ),
                'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
            ],
        ],
        'mail_from' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_from',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'mail_fromName' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_fromName',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'mail_bcc' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_mail_bcc',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        // @TODO what does company and applicant mail mean?
        'templatetype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_0',
                        0,
                    ],
                    [
                        'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_1',
                        1,
                    ],
                    [
                        'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_templatetype_2',
                        2,
                    ],
                ],
            ],
        ],
        'attachments' => $attachementsTca,
        'attachmentst3' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_templates_attachments',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'GIF, JPG, JPEG, TIF, TIFF, BMP, PCX, TGA, PNG, PDF, AI, FLV, SWF, RTMP, MP3, RGG',
                'disallowed' => '',
                'uploadfolder' => 'uploads/tx_mkmailer/attachments',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 10,
                'softref' => 'images',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, '.
                'mailtype, subject, '.
                'contenthtml;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], contenttext, '.
                // je nachdem, ob dam installiert ist, das entsprechende feld darstellen
                (tx_rnbase_util_Extensions::isLoaded('dam') ? 'attachments' : 'attachmentst3').', '.
                'description, mail_from,mail_fromName, mail_bcc, '.
                'templatetype',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];
