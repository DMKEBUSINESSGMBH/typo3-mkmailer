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

use Sys25\RnBase\Configuration\Processor;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_mail_SimpleMessage.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.ExcessivePublicCount")
 */
class tx_mkmailer_mail_SimpleMessage implements tx_mkmailer_mail_IMessage
{
    public $subject;

    public $from;

    private string $html = '';

    private string $text = '';

    private array $to = [];

    private array $cc = [];

    private array $bcc = [];

    private array $attachments = [];

    private array $options = [];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!($options && count($options))) {
            // Defaults setzen
            $options = self::getDefaultOptions();
        }

        $this->setOptions($options);
    }

    /**
     * Liefert die Default-Options.
     */
    public static function getDefaultOptions(): array
    {
        $options = [];

        // CharSet
        $charset = Processor::getExtensionCfgValue('mkmailer', 'charset');
        $options['charset'] = $charset ?: 'UTF-8';

        // Encoding
        $encoding = Processor::getExtensionCfgValue('mkmailer', 'encoding');
        $options['encoding'] = $encoding ?: '8bit';

        // returnpath // wenn 1 den Absender als Returnpath, anstonsten die angegebene Adresse
        $returnpath = Processor::getExtensionCfgValue('mkmailer', 'returnpath');
        $options['returnpath'] = $returnpath ?: 0;

        return $options;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::setOptions
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * (non-PHPdoc).
     *
     * @see mail/tx_mkmailer_mail_IMessage#setOption($key, $value)
     */
    public function setOption($key, $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * Returns options.
     *
     * @return array[string]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::setHtmlPart()
     */
    public function setHtmlPart($html): void
    {
        $this->html = $html;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::setTxtPart()
     */
    public function setTxtPart($text): void
    {
        $this->text = $text;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::getHtmlPart()
     */
    public function getHtmlPart(): string
    {
        return $this->html;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::getTxtPart()
     */
    public function getTxtPart(): string
    {
        return $this->text;
    }

    /**
     * Adds an attachment file.
     *
     * @param string $file path to file
     */
    public function addAttachment(tx_mkmailer_mail_IAttachment $file): void
    {
        $this->attachments[] = $file;
    }

    /**
     * Returns all attachments.
     *
     * @return array[tx_mkmailer_mail_IAttachment]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Set the mail subject.
     *
     * @param string $text
     */
    public function setSubject($text): void
    {
        $this->subject = $text;
    }

    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMessage::setFrom()
     */
    public function setFrom($address, $name = ''): void
    {
        $this->from = $this->createAddress($address, $name);
    }

    /**
     * Returns the from address.
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
    public function addTo($address, $name = ''): void
    {
        $this->to[] = $this->createAddress($address, $name);
    }

    /**
     * Removes all addresses.
     */
    public function clearTo(): void
    {
        $this->to[] = [];
    }

    /**
     * Returns recipients.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function addCc($address, $name = ''): void
    {
        $this->cc[] = $this->createAddress($address, $name);
    }

    /**
     * Setzt die CC Adressen.
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setCc(array $addresses): void
    {
        $this->cc = $addresses;
    }

    /**
     * Returns CCs.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @param string $address
     * @param string $name
     */
    public function addBcc($address, $name = ''): void
    {
        $this->bcc[] = $this->createAddress($address, $name);
    }

    /**
     * Setzt die BCC Adressen.
     *
     * @param array[tx_mkmailer_mail_IAddress] $addresses
     */
    public function setBcc(array $addresses): void
    {
        $this->bcc = $addresses;
    }

    /**
     * Returns BCCs.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * Creates a new address.
     *
     * @param string $address
     * @param string $name
     *
     * @return tx_mkmailer_mail_IAddress
     */
    private function createAddress($address, $name = ''): object
    {
        return GeneralUtility::makeInstance('tx_mkmailer_mail_Address', $address, $name);
    }
}
