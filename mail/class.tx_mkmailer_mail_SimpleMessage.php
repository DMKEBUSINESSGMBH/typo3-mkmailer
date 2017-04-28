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


tx_rnbase::load('tx_mkmailer_mail_IMessage');

/**
 * tx_mkmailer_mail_SimpleMessage
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mail_SimpleMessage implements tx_mkmailer_mail_IMessage
{

    /**
     * @var array
     */
    private $to = array();

    /**
     * @var array
     */
    private $cc = array();
    /**
     * @var array
     */
    private $bcc = array();

    /**
     * @var array
     */
    private $attachments = array();

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (!($options && count($options))) {
            // Defaults setzen
            $options = self::getDefaultOptions();
        }
        $this->setOptions($options);
    }

    /**
     * Liefert die Default-Options
     *
     * @return  array
     */
    public static function getDefaultOptions()
    {
        $options = array();
        tx_rnbase::load('tx_rnbase_configurations');

        // CharSet
        $charset = tx_rnbase_configurations::getExtensionCfgValue('mkmailer', 'charset');
        $options['charset'] = $charset ? $charset : 'UTF-8';

        // Encoding
        $encoding = tx_rnbase_configurations::getExtensionCfgValue('mkmailer', 'encoding');
        $options['encoding'] = $encoding ? $encoding : '8bit';

        // returnpath // wenn 1 den Absender als Returnpath, anstonsten die angegebene Adresse
        $returnpath = tx_rnbase_configurations::getExtensionCfgValue('mkmailer', 'returnpath');
        $options['returnpath'] = $returnpath ? $returnpath : 0;

        return $options;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::setOptions
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * (non-PHPdoc)
     * @see mail/tx_mkmailer_mail_IMessage#setOption($key, $value)
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Returns options
     *
     * @return array[string]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::setHtmlPart()
     */
    public function setHtmlPart($html)
    {
        $this->html = $html;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::setTxtPart()
     */
    public function setTxtPart($text)
    {
        $this->text = $text;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::getHtmlPart()
     */
    public function getHtmlPart()
    {
        return $this->html;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::getTxtPart()
     */
    public function getTxtPart()
    {
        return $this->text;
    }

    /**
     * Adds an attachment file
     *
     * @param string $file path to file
     */
    public function addAttachment(tx_mkmailer_mail_IAttachment $file)
    {
        $this->attachments[] = $file;
    }

    /**
     * Returns all attachments
     *
     * @return array[tx_mkmailer_mail_IAttachment]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set the mail subject
     * @param string $text
     */
    public function setSubject($text)
    {
        $this->subject = $text;
    }

    /**
     * Returns the subject.
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMessage::setFrom()
     */
    public function setFrom($address, $name = '')
    {
        $this->from = $this->createAddress($address, $name);
    }

    /**
     * Returns the from address
     *
     * @return tx_mkmailer_mail_IAddress
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function addTo($address, $name = '')
    {
        $this->to[] = $this->createAddress($address, $name);
    }

    /**
     * Removes all addresses
     */
    public function clearTo()
    {
        $this->to[] = array();
    }

    /**
     * Returns recipients
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function addCc($address, $name = '')
    {
        $this->cc[] = $this->createAddress($address, $name);
    }

    /**
     * Setzt die CC Adressen
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setCc(array $addresses)
    {
        $this->cc = $addresses;
    }

    /**
     * Returns CCs
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function addBcc($address, $name = '')
    {
        $this->bcc[] = $this->createAddress($address, $name);
    }

    /**
     * Setzt die BCC Adressen
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setBcc(array $addresses)
    {
        $this->bcc = $addresses;
    }

    /**
     * Returns BCCs
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Creates a new address
     *
     * @param string $address
     * @param string $name
     * @return tx_mkmailer_mail_IAddress
     */
    private function createAddress($address, $name = '')
    {
        return tx_rnbase::makeInstance('tx_mkmailer_mail_Address', $address, $name);
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_SimpleMessage.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_SimpleMessage.php']);
}
