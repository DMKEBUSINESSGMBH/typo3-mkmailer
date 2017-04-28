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


tx_rnbase::load('tx_mkmailer_mail_IMailJob');

/**
 * tx_mkmailer_mail_MailJob
 *
 * Ein MailJob kann in die MailQueue eingestellt werden und
 * wird zu einem späteren Zeitpunkt verarbeitet.
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mail_MailJob implements tx_mkmailer_mail_IMailJob
{

    /**
     * @var array
     */
    private $receiver = array();

    /**
     * Initialisiert den mailjob.
     * Optional können bereits die MeiE-Mail-Empfänger und ein Template mitgegeben werden.
     *
     * @param   array[tx_mkmailer_receiver_IMailReceiver]   $receiver
     * @param   tx_mkmailer_models_Template                 $templateObj
     */
    public function tx_mkmailer_mail_MailJob(
        array $receiver = array(),
        tx_mkmailer_models_Template &$templateObj = null
    ) {
        $this->receiver = $receiver;

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
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMailJob::getReceiver()
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     *
     * @param string $value
     * @return array
     */
    public function addReceiver($value)
    {
        return $this->receiver[] = $value;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMailJob::getContentText()
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * @param string $value
     */
    public function setContentText($value)
    {
        $this->contentText = $value;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMailJob::getContentHtml()
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     *
     * @param string $value
     * @param string $filename
     */
    public function setContentHtml($value, $filename = '')
    {
        $this->contentHtml = $value;
    }

    /**
     * (non-PHPdoc)
     * @see tx_mkmailer_mail_IMailJob::getSubject()
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $value
     */
    public function setSubject($value)
    {
        $this->subject = $value;
    }

    /**
     * Liefert die Absenderadresse
     * @return tx_mkmailer_mail_IAddress
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param tx_mkmailer_mail_IAddress $value
     */
    public function setFrom(tx_mkmailer_mail_IAddress $value)
    {
        $this->from = $value;
    }

    /**
     * Liefert die TO-Empfänger
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
    public function setTOs($value)
    {
        $this->tos = $value;
    }

    /**
     * @param tx_mkmailer_mail_IAddress $value
     */
    public function addTO(tx_mkmailer_mail_IAddress $value)
    {
        $this->tos[] = $value;
    }

    /**
     * Liefert die CC-Empfänger
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
    public function setCCs($value)
    {
        $this->ccs = $value;
    }

    /**
     * @param tx_mkmailer_mail_IAddress $value
     */
    public function addCC(tx_mkmailer_mail_IAddress $value)
    {
        $this->ccs[] = $value;
    }

    /**
     * Liefert die BCC-Empfänger
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
    public function setBCCs($value)
    {
        $this->bccs = $value;
    }

    /**
     * @param tx_mkmailer_mail_IAddress $value
     */
    public function addBCC(tx_mkmailer_mail_IAddress $value)
    {
        $this->bccs[] = $value;
    }

    /**
     * Liefert die BCC-Empfänger
     * @return array[string]
     */
    public function getAttachments()
    {
        return $this->attach;
    }

    /**
     * Attachment an Email anhängen.
     * @param tx_mkmailer_mail_IAttachment $attachment
     */
    public function addAttachment(tx_mkmailer_mail_IAttachment $attachment)
    {
        if (!is_array($this->attach)) {
            $this->attach = array();
        }

        $this->attach[] = $attachment;
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_MailJob.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_MailJob.php']);
}
