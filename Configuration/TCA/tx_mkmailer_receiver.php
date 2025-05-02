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
