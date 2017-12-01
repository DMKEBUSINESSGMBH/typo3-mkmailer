<?php

tx_rnbase::load('Tx_Rnbase_Utility_TcaTool');
tx_rnbase::load('tx_rnbase_util_TYPO3');

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_receiver',
        'label' => 'receivers',
        'label_alt' => 'resolver',
        'default_sortby' => 'ORDER BY receivers',
        'enablecolumns' => array(),
        'iconfile' => 'EXT:mkmailer/icon_tx_mkmailer_templates.gif'
    ),
    'interface' => array(
        'showRecordFieldList' => 'resolver, receivers',
        'maxDBListItems' => '5'
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource'
    ),
    'columns' => array(
        'resolver' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_receiver_resolver',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim'
            )
        ),
        'receivers' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkmailer/locallang_db.xml:tx_mkmailer_receiver_receiver',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim'
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'resolver, receivers'
        )
    ),
    'palettes' => array(
        '1' => array(
            'showitem' => ''
        )
    )
);
