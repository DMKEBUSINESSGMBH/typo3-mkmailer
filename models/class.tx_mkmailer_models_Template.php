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

use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Utility\TSFAL;

/**
 * tx_mkmailer_models_Template.
 *
 * Model für einen Datensatz der Tabelle tx_mkmailer_templates.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_models_Template extends BaseModel
{
    /**
     * (non-PHPdoc).
     *
     * @see BaseModel::getTableName()
     */
    public function getTableName(): string
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
        return $this->getProperty('contenttext');
    }

    /**
     * Returns the Mail-Template HTML-Part.
     *
     * @return string
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    public function getContentHtml($plain = false)
    {
        if ($plain) {
            return $this->getProperty('contenthtml');
        }

        return tx_mkmailer_util_Misc::getRTEText($this->getProperty('contenthtml'));
    }

    /**
     * Liefert die BCCs als Adress-Array.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getBccAddress(): array
    {
        return $this->getAddresses($this->getBcc());
    }

    /**
     * @param string $addrStr
     *
     * @return multitype:|multitype:tx_mkmailer_mail_Address
     * @return tx_mkmailer_mail_Address[]
     */
    private function getAddresses($addrStr): array
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

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->getProperty('mail_bcc');
    }

    /**
     * Liefert die CCs als Adress-Array.
     *
     * @return array[tx_mkmailer_mail_IAddress]
     */
    public function getCcAddress(): array
    {
        return $this->getAddresses($this->getCc());
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->getProperty('mail_cc');
    }

    /**
     * Liefert den Absender als Adresse.
     *
     * @return tx_mkmailer_mail_IAddress
     */
    public function getFromAddress(): tx_mkmailer_mail_Address
    {
        return new tx_mkmailer_mail_Address($this->getProperty('mail_from'), $this->getProperty('mail_fromName'));
    }

    /**
     * Returns the Mail-Template From E-Mail-Address.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->getProperty('mail_from');
    }

    /**
     * Returns the Mail-Template From name.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->getProperty('mail_fromName');
    }

    /**
     * Returns the Mail-Template Subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->getProperty('subject');
    }

    /**
     * Liefert die FAL-Attachments.
     *
     * @todo testen
     */
    private function getFalAttachmentPaths(): array
    {
        $attachmentPaths = [];
        if ($this->isPersisted()) {
            $falFiles = TSFAL::getReferences(
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
     * Liefert die Pfade zu den Anhängen.
     */
    protected function getAttachmentPaths(): array
    {
        return $this->getFalAttachmentPaths();
    }

    /**
     * Liefert die Attachments.
     *
     * @return  array[tx_mkmailer_mail_IAttachment]
     */
    public function getAttachments(): array
    {
        $files = $this->getAttachmentPaths();
        if ([] === $files) {
            return $files;
        }

        foreach ($files as &$file) {
            $file = tx_mkmailer_mail_Factory::createAttachment($file);
        }

        return $files;
    }
}
