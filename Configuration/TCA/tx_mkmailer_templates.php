<?php

$attachementsTca = \Sys25\RnBase\Utility\TSFAL::getMediaTCA(
    'attachments',
    ['config' => ['softref' => 'images']]
);

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
        'maxDBListItems' => '5',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                    ],
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value',
                        0,
                    ],
                ],
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
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
                'enableRichtext' => true,
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
                'type' => 'inline',
                'foreign_table' => 'sys_file_reference',
                'foreign_field' => 'uid_foreign',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_table_field' => 'tablenames',
                'foreign_match_fields' => [
                    'fieldname' => 'inline_1',
                ],
                'foreign_label' => 'uid_local',
                'foreign_selector' => 'uid_local',
                'overrideChildTca' => [
                    'columns' => [
                        'uid_local' => [
                            'config' => [
                                'appearance' => [
                                    'elementBrowserType' => 'file',
                                    'elementBrowserAllowed' => 'gif, jpg, jpeg, tif, tiff, bmp, pcx, tga, png, pdf, ai, flv, swf, rtmp, mp3, rgg',
                                ],
                            ],
                        ],
                        'crop' => [
                            'description' => 'field description',
                        ],
                    ],
                    'types' => [
                        2 => [
                            'showitem' => '
                                --palette--;;imageoverlayPalette,
                                --palette--;;filePalette',
                        ],
                    ],
                ],
                'filter' => [
                    [
                        'userFunc' => 'TYPO3\\CMS\\Core\\Resource\\Filter\\FileExtensionFilter->filterInlineChildren',
                        'parameters' => [
                            'allowedFileExtensions' => 'gif, jpg, jpeg, tif, tiff, bmp, pcx, tga, png, pdf, ai, flv, swf, rtmp, mp3, rgg',
                            'disallowedFileExtensions' => '',
                        ],
                    ],
                ],
                'appearance' => [
                    'useSortable' => true,
                    'headerThumbnail' => [
                        'field' => 'uid_local',
                        'height' => '45m',
                    ],
                    'enabledControls' => [
                        'info' => true,
                        'new' => false,
                        'dragdrop' => true,
                        'sort' => false,
                        'hide' => true,
                        'delete' => true,
                    ],
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                ],
            ],



//                [
//                'type' => 'group',
//                'internal_type' => 'file',
//                'allowed' => 'GIF, JPG, JPEG, TIF, TIFF, BMP, PCX, TGA, PNG, PDF, AI, FLV, SWF, RTMP, MP3, RGG',
//                'disallowed' => '',
//                'uploadfolder' => 'uploads/tx_mkmailer/attachments',
//                'size' => 5,
//                'minitems' => 0,
//                'maxitems' => 10,
//                'softref' => 'images',
//            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid,--palette--,l18n_parent,l18n_diffsource,mailtype,subject,contenthtml,--palette--,contenttext,description,mail_from,mail_fromName,mail_bcc,templatetype',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];
