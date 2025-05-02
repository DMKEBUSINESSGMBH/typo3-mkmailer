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

use Sys25\RnBase\Frontend\Marker\FormatUtil;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_receiver_FeUser.
 *
 * Implementierung für einen Mailempfänger vom Typ FeUser.
 *
 * @TODO: auf tx_mkmailer_receiver_BaseTemplate umstellen
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_receiver_FeUser extends tx_mkmailer_receiver_BaseTemplate
{
    /**
     * @var tx_t3users_models_feuser
     */
    protected $obj;

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::setValueString()
     */
    public function setValueString($value): void
    {
        $this->setFeUser(tx_t3users_models_feuser::getInstance(intval($value)));
    }

    /**
     * @param tx_t3users_models_feuser $feuser
     */
    public function setFeUser($feuser): void
    {
        $this->obj = $feuser;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getAddressCount()
     */
    public function getAddressCount(): int
    {
        return is_object($this->obj) ? 1 : 0; // Immer nur eine Person
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getAddresses()
     */
    public function getAddresses(): array
    {
        if (!$this->getEmail()) {
            return [];
        }

        return [$this->getEmail()];
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getName()
     */
    public function getName()
    {
        if (!is_object($this->obj) || !$this->obj->isValid()) {
            return 'unknown';
        }

        return $this->obj->record['username'];
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getSingleAddress()
     */
    public function getSingleAddress($idx)
    {
        $member = $this->obj;
        $ret['address'] = $this->getEmail();
        $ret['addressName'] = $member->record['vname'].' '.$member->record['nname'];
        // TODO: Die AddressID ist notwendig, um beim Versionswechsel kein Mails doppelt zu verschicken.
        $ret['addressid'] = $ret['address'].'_'.$ret['addressName'];

        return $ret;
    }

    /**
     * Hier können susätzliche Daten in das Template gefügt werden.
     *
     * @param string     $mailText
     * @param string     $mailHtml
     * @param string     $mailSubject
     * @param FormatUtil $formatter
     * @param string     $confId
     * @param int        $idx         Index des Empfängers von 0 bis (getAddressCount() - 1)
     *
     * @return void
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    protected function addAdditionalData(
        &$mailText,
        &$mailHtml,
        &$mailSubject,
        $formatter,
        $confId,
        $idx,
    ) {
        $marker = GeneralUtility::makeInstance('tx_t3users_util_FeUserMarker');
        $mailText = $marker->parseTemplate(
            $mailText,
            $this->obj,
            $formatter,
            $confId.'receiver.',
            'RECEIVER'
        );
        $mailHtml = $marker->parseTemplate(
            $mailHtml,
            $this->obj,
            $formatter,
            $confId.'receiver.',
            'RECEIVER'
        );
        $mailSubject = $marker->parseTemplate(
            $mailSubject,
            $this->obj,
            $formatter,
            $confId.'receiver.',
            'RECEIVER'
        );
    }

    /**
     * @return string
     */
    protected function getEmail()
    {
        if (!is_object($this->obj) || !isset($this->obj->record['email'])) {
            return false;
        }

        // else
        return $this->obj->record['email'];
    }

    /**
     * Liefert die ConfId für den Reciver.
     */
    protected function getConfId(): string
    {
        return 'receiver.';
    }
}
