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
 * tx_mkmailer_models_Queue.
 *
 * Model für einen Datensatz der Tabelle tx_mkmailer_queue.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_models_Queue extends \Sys25\RnBase\Domain\Model\BaseModel
{
    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Domain\Model\BaseModel::getTableName()
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
        return $this->getRecord()['subject'];
    }

    /**
     * Liefert den Mailtext für den Textpart.
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->getRecord()['contenttext'];
    }

    /**
     * Liefert den Mailtext für den HTML-Part.
     *
     * @return string
     */
    public function getContentHtml()
    {
        return $this->getRecord()['contenthtml'];
    }

    /**
     * Liefert ein Array mit Instanzen von tx_mkmailer_mail_IAttachment.
     *
     * @return string or array[tx_mkmailer_mail_IAttachment]
     */
    public function getUploads()
    {
        $ret = [];
        $attachments = $this->getRecord()['attachments'];
        if (!$attachments) {
            return $ret;
        }
        // Hier muss geprüft werden ob serialisierte Daten vorliegen.
        if ($attachments && 'a' === $attachments[0] && ':' === $attachments[1]) {
            $ret = unserialize($attachments);
        } else {
            // Alle Strings zu Attachments umformen
            $files = \Sys25\RnBase\Utility\Strings::trimExplode(',', $attachments);
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
        return intval($this->getRecord()['mailcount']);
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->getRecord()['mail_cc'];
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->getRecord()['mail_bcc'];
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->getRecord()['mail_from'];
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->getRecord()['mail_fromName'];
    }

    /**
     * Prüft, ob die Mail beschleunigt versendet wird.
     *
     * @return bool
     */
    public function isPrefer()
    {
        return intval($this->getRecord()['prefer']) > 0;
    }

    /**
     * Liefert den Zeitpunkt der Erstellung.
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->getRecord()['cr_date'];
    }

    /**
     * Liefert den Zeitpunkt der letzten Aktualisierung.
     *
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->getRecord()['lastupdate'];
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
