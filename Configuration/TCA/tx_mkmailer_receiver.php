<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_receiver',
        'label' => 'receivers',
        'label_alt' => 'resolver',
        'default_sortby' => 'ORDER BY receivers',
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
        'resolver' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_receiver_resolver',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
        'receivers' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/Resources/Private/Language/locallang_db.xlf:tx_mkmailer_receiver_receiver',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'resolver, receivers',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
];
