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

use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\Strings;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_util_Misc.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_util_Misc
{
    /**
     * Will process the input string with the parseFunc function from TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer based on configuration set in "lib.parseFunc_RTE" in the current TypoScript template.
     * This is useful for rendering of content in RTE fields where the transformation mode is set to "ts_css" or so.
     * Notice that this requires the use of "css_styled_content" to work right.
     *
     * @param   string      The input text string to process
     *
     * @return string The processed string
     *
     * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::parseFunc()
     *
     * @SuppressWarnings("PHPMD.ElseExpression")
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public static function getRTEText($str)
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            Misc::prepareTSFE();
            $setup = self::loadTS();
            $parseFunc = $setup['lib.']['parseFunc_RTE.'];
            // TS-Config prüfen. TODO: Das sollte besser gemacht werden.
            if (!is_array($GLOBALS['TSFE']->config)) {
                $GLOBALS['TSFE']->config = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray();
            }
        } else {
            $parseFunc = $GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray()['lib.']['parseFunc_RTE.'];
        }

        $cObj = GeneralUtility::makeInstance(TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        if (is_array($parseFunc)) {
            return $cObj->parseFunc($str, $parseFunc);
        }

        return $str;
    }

    public static function loadTS(): array
    {
        return GeneralUtility::makeInstance(TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class)->getConfiguration(
            TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
    }

    /**
     * Wandelt einen String mit Mailadressen in Objekte der Klasse tx_mkmailer_mail_IAddress um.
     *
     * @param string $addrStr
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public static function parseAddressString($addrStr): array
    {
        $ret = [];
        if ('' === trim($addrStr)) {
            return $ret;
        }

        $addrArr = Strings::trimExplode(',', $addrStr);
        foreach ($addrArr as $addr) {
            $ret[] = new tx_mkmailer_mail_Address($addr);
        }

        return $ret;
    }
}
