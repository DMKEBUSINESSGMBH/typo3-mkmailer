<?php

namespace DMK\MkMailer\Model;

use DMK\MkMailer\Mail\Factory;
use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Utility\Strings;

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
class Queue extends BaseModel
{
    /**
     * (non-PHPdoc).
     *
     * @see BaseModel::getTableName()
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
        return $this->getProperty('subject');
    }

    /**
     * Liefert den Mailtext für den Textpart.
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->getProperty('contenttext');
    }

    /**
     * Liefert den Mailtext für den HTML-Part.
     *
     * @return string
     */
    public function getContentHtml()
    {
        return $this->getProperty('contenthtml');
    }

    /**
     * Liefert ein Array mit Instanzen von tx_mkmailer_mail_IAttachment.
     *
     * @return string or array[tx_mkmailer_mail_IAttachment]
     */
    public function getUploads()
    {
        $ret = [];
        $attachments = $this->getProperty('attachments');
        if (!$attachments) {
            return $ret;
        }
        // Hier muss geprüft werden ob serialisierte Daten vorliegen.
        if ($attachments && 'a' === $attachments[0] && ':' === $attachments[1]) {
            $ret = unserialize($attachments);
        } else {
            // Alle Strings zu Attachments umformen
            $files = Strings::trimExplode(',', $attachments);
            foreach ($files as $file) {
                $ret[] = Factory::createAttachment($file);
            }
        }

        return $ret;
    }

    /**
     * @return number
     */
    public function getMailCount()
    {
        return (int) $this->getProperty('mailcount');
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->getProperty('mail_cc');
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->getProperty('mail_bcc');
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->getProperty('mail_from');
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->getProperty('mail_fromName');
    }

    /**
     * Prüft, ob die Mail beschleunigt versendet wird.
     *
     * @return bool
     */
    public function isPrefer()
    {
        return intval($this->getProperty('prefer')) > 0;
    }

    /**
     * Liefert den Zeitpunkt der Erstellung.
     *
     * @return string
     */
    public function getCreationDate()
    {
        return $this->getProperty('cr_date');
    }

    /**
     * Liefert den Zeitpunkt der letzten Aktualisierung.
     *
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->getProperty('lastupdate');
    }

    /**
     * Liefert die Receiver dieser Mail als Array.
     *
     * @return array
     */
    public function getReceivers()
    {
        $mailServ = \tx_mkmailer_util_ServiceRegistry::getMailService();

        return $mailServ->getMailReceivers($this);
    }
}
