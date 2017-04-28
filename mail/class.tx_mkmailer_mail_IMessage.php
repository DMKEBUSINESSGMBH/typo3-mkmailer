<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (rene@system25.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/



/**
 * tx_mkmailer_mail_IMessage
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
interface tx_mkmailer_mail_IMessage
{

    /**
     * Returns the subject.
     * @return string
     */
    public function getSubject();

    /**
     *
     * @param string $html
     */
    public function setHtmlPart($html);

    /**
     *
     * @param string $text
     */
    public function setTxtPart($text);

    /**
     * @return string
     */
    public function getHtmlPart();

    /**
     * @return string
     */
    public function getTxtPart();

    /**
     * Adds an attachment file
     *
     * @param tx_mkmailer_mail_IAttachment $attachment path to file
     */
    public function addAttachment(tx_mkmailer_mail_IAttachment $attachment);

    /**
     * Returns all attachments
     *
     * @return array[tx_mkmailer_mail_IAttachment]
     */
    public function getAttachments();

    /**
     * Setzt die CC Adressen
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setCc(array $addresses);

    /**
     * Setzt die BCC Adressen
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setBcc(array $addresses);

    /**
     * Setzt den Absender
     *
     * @param string $address
     * @param string $name
     */
    public function setFrom($address, $name = '');

    /**
     * Returns options
     *
     * @return array[string]
     */
    public function getOptions();

    /**
     * Set options
     *
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value);
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IMessage.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IMessage.php']);
}
