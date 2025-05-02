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

use Sys25\RnBase\Backend\Module\BaseModule;

/**
 * tx_mkmailer_module1.
 *
 * Module 'MK Mailer' for the 'mkmailer' extension.
 *
 * @author          René Nitzsche <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mod1_Module extends BaseModule
{
    public $pageinfo;

    /**
     * Initializes the backend module by setting internal variables, initializing the menu.
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function init(): void
    {
        if (!isset($this->MCONF['name'])) {
            $this->MCONF = array_merge((array) $GLOBALS['MCONF'], [
                'name' => 'web_MkmailerBackend',
                'access' => 'user',
            ]);
        }

        $this->getLanguageService()->includeLLFile('EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf');
        $this->getBackendUser()->modAccess($this->MCONF);

        parent::init();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Backend\Module\BaseModule::getExtensionKey()
     */
    public function getExtensionKey()
    {
        return 'mkmailer';
    }

    public function getTitle()
    {
        return 'LLL:EXT:mkmailer/Resources/Private/Language/Backend/locallang_mod.xlf';
    }

    public function getRouteIdentifier()
    {
        return 'web_MkmailerBackend';
    }
}
