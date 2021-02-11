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
tx_rnbase::load('tx_rnbase_util_Strings');
tx_rnbase::load('tx_rnbase_model_base');

/**
 * tx_mkmailer_models_Queue.
 *
 * Model für einen Datensatz der Tabelle tx_mkmailer_queue.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_models_Queue extends tx_rnbase_model_base
{
    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_model_base::getTableName()
     */
    public function getTableName()
    {
        return 'tx_mkmailer_queue';
    }

    /**
     * Liefert den Betreff.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->record['subject'];
    }

    /**
     * Liefert den Mailtext für den Textpart.
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->record['contenttext'];
    }

    /**
     * Liefert den Mailtext für den HTML-Part.
     *
     * @return string
     */
    public function getContentHtml()
    {
        return $this->record['contenthtml'];
    }

    /**
     * Liefert ein Array mit Instanzen von tx_mkmailer_mail_IAttachment.
     *
     * @return string or array[tx_mkmailer_mail_IAttachment]
     */
    public function getUploads()
    {
        $ret = [];
        $attachments = $this->record['attachments'];
        if (!$attachments) {
            return $ret;
        }
        // Hier muss geprüft werden ob serialisierte Daten vorliegen.
        if ($attachments && 'a' === $attachments[0] && ':' === $attachments[1]) {
            tx_rnbase::load('tx_mkmailer_mail_Attachment');
            $ret = unserialize($attachments);
        } else {
            // Alle Strings zu Attachments umformen
            tx_rnbase::load('tx_mkmailer_mail_Factory');
            $files = tx_rnbase_util_Strings::trimExplode(',', $attachments);
            foreach ($files as $file) {
                $ret[] = tx_mkmailer_mail_Factory::createAttachment($file);
            }
        }

        return $ret;
    }

    /**
     * @return number
     */
    public function getMailCount()
    {
        return intval($this->record['mailcount']);
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->record['mail_cc'];
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->record['mail_bcc'];
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->record['mail_from'];
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->record['mail_fromName'];
    }

    /**
     * Prüft, ob die Mail beschleunigt versendet wird.
     *
     * @return bool
     */
    public function isPrefer()
    {
        return intval($this->record['prefer']) > 0;
    }

    /**
     * Liefert den Zeitpunkt der Erstellung.
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->record['cr_date'];
    }

    /**
     * Liefert den Zeitpunkt der letzten Aktualisierung.
     *
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->record['lastupdate'];
    }

    /**
     * Liefert die Receiver dieser Mail als Array.
     *
     * @return array
     */
    public function getReceivers()
    {
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();

        return $mailServ->getMailReceivers($this);
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Queue.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Queue.php'];
}
