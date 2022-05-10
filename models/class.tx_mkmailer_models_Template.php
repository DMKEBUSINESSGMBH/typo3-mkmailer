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
 * tx_mkmailer_models_Template.
 *
 * Model für einen Datensatz der Tabelle tx_mkmailer_templates.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_models_Template extends \Sys25\RnBase\Domain\Model\BaseModel
{
    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Domain\Model\BaseModel::getTableName()
     */
    public function getTableName()
    {
        return 'tx_mkmailer_templates';
    }

    /**
     * Returns the Mail-Template.
     *
     * @return string
     */
    public function getContentText()
    {
        return $this->getRecord()['contenttext'];
    }

    /**
     * Returns the Mail-Template HTML-Part.
     *
     * @return string
     */
    public function getContentHtml($plain = false)
    {
        if ($plain) {
            return $this->getRecord()['contenthtml'];
        }

        $ret = tx_mkmailer_util_Misc::getRTEText($this->getRecord()['contenthtml']);

        return $ret;
    }

    /**
     * Liefert die BCCs als Adress-Array.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getBccAddress()
    {
        return $this->getAddresses($this->getBcc());
    }

    /**
     * @param string $addrStr
     *
     * @return multitype:|multitype:tx_mkmailer_mail_Address
     */
    private function getAddresses($addrStr)
    {
        $ret = [];
        if (!strlen(trim($addrStr))) {
            return $ret;
        }
        $addrArr = \Sys25\RnBase\Utility\Strings::trimExplode(',', $addrStr);
        foreach ($addrArr as $addr) {
            $ret[] = new tx_mkmailer_mail_Address($addr);
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->getRecord()['mail_bcc'];
    }

    /**
     * Liefert die CCs als Adress-Array.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getCcAddress()
    {
        return $this->getAddresses($this->getCc());
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->getRecord()['mail_cc'];
    }

    /**
     * Liefert den Absender als Adresse.
     *
     * @return tx_mkmailer_mail_IAddress
     */
    public function getFromAddress()
    {
        return new tx_mkmailer_mail_Address($this->getRecord()['mail_from'], $this->getRecord()['mail_fromName']);
    }

    /**
     * Returns the Mail-Template From E-Mail-Address.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->getRecord()['mail_from'];
    }

    /**
     * Returns the Mail-Template From name.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->getRecord()['mail_fromName'];
    }

    /**
     * Returns the Mail-Template Subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->getRecord()['subject'];
    }

    /**
     * Liefert die FAL-Attachments.
     *
     * @return  array
     *
     * @todo testen
     */
    private function getFalAttachmentPaths()
    {
        $attachmentPaths = [];
        if ($this->isPersisted()) {
            $falFiles = \Sys25\RnBase\Utility\TSFAL::getReferences(
                $this->getTableName(),
                $this->getUid(),
                'attachments'
            );

            /* @var $falFile \TYPO3\CMS\Core\Resource\FileReference */
            foreach ($falFiles as $falFile) {
                $attachmentPaths[] = $falFile->getPublicUrl();
            }
        }

        return $attachmentPaths;
    }

    /**
     * Liefert den Pfad zu den Attachments.
     *
     * @return  string
     */
    private function getT3AttachmentUploadFolder()
    {
        $fields = $this->getTCAColumns();

        return $fields['attachmentst3']['config']['uploadfolder'];
    }

    /**
     * Liefert die T3-Attachments.
     *
     * @return  array
     */
    private function getT3AttachmentPaths()
    {
        $files = \Sys25\RnBase\Utility\Strings::trimExplode(',', $this->getRecord()['attachmentst3'], true);
        if (empty($files)) {
            return $files;
        }
        // den uploadpfad mit anhängen
        $uploadfolder = $this->getT3AttachmentUploadFolder();
        foreach ($files as &$file) {
            $file = $uploadfolder.'/'.$file;
        }

        return $files;
    }

    /**
     * Liefert die Pfade zu den Anhängen.
     *
     * @return  array
     */
    protected function getAttachmentPaths()
    {
        return array_merge(
            $this->getFalAttachmentPaths(),
            $this->getT3AttachmentPaths()
        );
    }

    /**
     * Liefert die Attachments.
     *
     * @return  array[tx_mkmailer_mail_IAttachment]
     */
    public function getAttachments()
    {
        $files = $this->getAttachmentPaths();
        if (empty($files)) {
            return $files;
        }
        foreach ($files as &$file) {
            $file = tx_mkmailer_mail_Factory::createAttachment($file);
        }

        return $files;
    }
}
