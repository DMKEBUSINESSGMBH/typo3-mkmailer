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
    'web_MkmailerBackend' => [
        'parent' => 'web',
        'position' => ['bottom'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/mkmailer',
        'icon' => 'EXT:mkmailer/Resources/Public/Icons/moduleicon.png',
        'labels' => 'LLL:EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf',
        'extensionName' => 'Mkmailer',
    ],
    'web_MkmailerBackend_overview' => [
        'parent' => 'web_MkmailerBackend',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/mkmailer/overview',
        'icon' => 'EXT:mkmailer/Resources/Public/Icons/moduleicon.png',
        'labels' => [
            'title' => 'LLL:EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf:func_overview',
        ],
        'routes' => [
            '_default' => [
                'target' => 'tx_mkmailer_mod1_FuncOverview::main',
            ],
        ],
    ],
];
