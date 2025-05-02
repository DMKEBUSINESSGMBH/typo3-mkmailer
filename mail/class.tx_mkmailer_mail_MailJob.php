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

/**
 * tx_mkmailer_mail_MailJob.
 *
 * Ein MailJob kann in die MailQueue eingestellt werden und
 * wird zu einem späteren Zeitpunkt verarbeitet.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.ExcessivePublicCount")
 */
class tx_mkmailer_mail_MailJob implements tx_mkmailer_mail_IMailJob
{
    public $contentText;

    public $contentHtml;

    public $subject;

    public $from;

    public $tos;

    public $ccs;

    public $bccs;

    private ?array $attach = null;

    /**
     * Initialisiert den mailjob.
     * Optional können bereits die MeiE-Mail-Empfänger und ein Template mitgegeben werden.
     *
     * @param   array[tx_mkmailer_receiver_IMailReceiver]   $receiver
     */
    public function __construct(
        private array $receiver = [],
        ?tx_mkmailer_models_Template &$templateObj = null,
    ) {
        // set template data, if given
        if (is_object($templateObj)) {
            $this->setFrom($templateObj->getFromAddress());
            $this->setCCs($templateObj->getCcAddress());
            $this->setBCCs($templateObj->getBccAddress());
            $this->setSubject($templateObj->getSubject());
            $this->setContentText($templateObj->getContentText());
            $this->setContentHtml($templateObj->getContentHtml());
            // anhänge verarbeiten
            foreach ($templateObj->getAttachments() as $attachment) {
                $this->addAttachment($attachment);
            }
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMailJob::getReceiver()
     */
    public function getReceiver(): array
    {
        return $this->receiver;
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function addReceiver($value)
    {
        return $this->receiver[] = $value;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMailJob::getContentText()
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * @param string $value
     */
    public function setContentText($value): void
    {
        $this->contentText = $value;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMailJob::getContentHtml()
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * @param string $value
     * @param string $filename
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function setContentHtml($value, $filename = ''): void
    {
        $this->contentHtml = $value;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IMailJob::getSubject()
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $value
     */
    public function setSubject($value): void
    {
        $this->subject = $value;
    }

    /**
     * Liefert die Absenderadresse.
     *
     * @return tx_mkmailer_mail_IAddress
     */
    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom(tx_mkmailer_mail_IAddress $value): void
    {
        $this->from = $value;
    }

    /**
     * Liefert die TO-Empfänger.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getTOs()
    {
        return $this->tos;
    }

    /**
     * Setzt die TO-Empfänger. Achtung: schon vorhandene Daten werden überschrieben.
     *
     * @param array[tx_mkmailer_mail_IAddress] $value
     */
    public function setTOs($value): void
    {
        $this->tos = $value;
    }

    public function addTO(tx_mkmailer_mail_IAddress $value): void
    {
        $this->tos[] = $value;
    }

    /**
     * Liefert die CC-Empfänger.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getCCs()
    {
        return $this->ccs;
    }

    /**
     * Setzt die CC-Empfänger. Achtung: schon vorhandene Daten werden überschrieben.
     *
     * @param array[tx_mkmailer_mail_IAddress] $value
     */
    public function setCCs($value): void
    {
        $this->ccs = $value;
    }

    public function addCC(tx_mkmailer_mail_IAddress $value): void
    {
        $this->ccs[] = $value;
    }

    /**
     * Liefert die BCC-Empfänger.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getBCCs()
    {
        return $this->bccs;
    }

    /**
     * Setzt die CC-Empfänger. Achtung: schon vorhandene Daten werden überschrieben.
     *
     * @param array[tx_mkmailer_mail_IAddress] $value
     */
    public function setBCCs($value): void
    {
        $this->bccs = $value;
    }

    public function addBCC(tx_mkmailer_mail_IAddress $value): void
    {
        $this->bccs[] = $value;
    }

    /**
     * Liefert die BCC-Empfänger.
     *
     * @return array[string]
     */
    public function getAttachments(): ?array
    {
        return $this->attach;
    }

    /**
     * Attachment an Email anhängen.
     */
    public function addAttachment(tx_mkmailer_mail_IAttachment $attachment): void
    {
        if (!is_array($this->attach)) {
            $this->attach = [];
        }

        $this->attach[] = $attachment;
    }
}
