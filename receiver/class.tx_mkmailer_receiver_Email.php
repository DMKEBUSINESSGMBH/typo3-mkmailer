<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Rene Nitzsche (rene@system25.de)
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
 * tx_mkmailer_receiver_Email.
 *
 * Implementierung für einen Mailempfänger vom Typ E-Mail.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_receiver_Email extends tx_mkmailer_receiver_BaseTemplate
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @param string $email
     */
    public function __construct($email = null)
    {
        $this->setEMail($email);
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::setValueString()
     */
    public function setValueString($value)
    {
        $this->setEMail($value);
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_Base::getValueString()
     */
    public function getValueString()
    {
        return $this->getEMail();
    }

    /**
     * @param string $value
     */
    public function setEMail($value)
    {
        $this->email = $value;
    }

    /**
     * @return string
     */
    public function getEMail()
    {
        return $this->email;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getAddressCount()
     */
    public function getAddressCount()
    {
        return $this->email ? 1 : 0; // Immer nur eine Mail
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getAddresses()
     */
    public function getAddresses()
    {
        return $this->email ? [$this->email] : [];
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getName()
     */
    public function getName()
    {
        return $this->email ? $this->email : 'unknown';
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getSingleAddress()
     */
    public function getSingleAddress($idx)
    {
        $ret['address'] = $this->email;
        // TODO: Die AddressID ist notwendig, um beim Versionswechsel kein Mails doppelt zu verschicken.
        $ret['addressid'] = $ret['address'];

        return $ret;
    }

    /**
     * Liefert die ConfId für den Reciver.
     *
     * @return  string
     */
    protected function getConfId()
    {
        return 'email.';
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_Email.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_Email.php'];
}
