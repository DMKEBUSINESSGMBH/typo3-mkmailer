<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkmailer" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

$attachementsTca = Sys25\RnBase\Utility\TSFAL::getMediaTCA(
    'attachments',
);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates',
        'label' => 'mailtype',
        'label_alt' => 'description',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'default_sortby' => 'ORDER BY mailtype',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'iconfile' => 'EXT:mkmailer/Resources/Public/Icons/icon_tx_mkmailer_templates.gif',
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
            'config' => ['type' => 'language'],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
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
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_mailtype',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_subject',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'contenttext' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_contenttext',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'softref' => 'typolink_tag,email[subst],url',
            ],
        ],
        'contenthtml' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_contenthtml',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true,
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'softref' => 'typolink_tag,email[subst],url',
            ],
        ],
        'mail_from' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_mail_from',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'mail_fromName' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_mail_fromName',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'mail_bcc' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_mail_bcc',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        // @TODO what does company and applicant mail mean?
        'templatetype' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_templatetype',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_templatetype_0',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_templatetype_1',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_templates_templatetype_2',
                        'value' => 2,
                    ],
                ],
            ],
        ],
        'attachments' => $attachementsTca,
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
