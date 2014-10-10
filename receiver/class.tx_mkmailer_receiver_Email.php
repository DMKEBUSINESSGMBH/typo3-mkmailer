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
require_once(t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php'));
tx_rnbase::load('tx_mkmailer_receiver_BaseTemplate');

/**
 * Implementierung für einen Mailempfänger vom Typ E-Mail.
 */
class tx_mkmailer_receiver_Email extends tx_mkmailer_receiver_BaseTemplate {
	protected $email;

	public function tx_mkmailer_receiver_Email($email=null){
		$this->setEMail($email);
	}

	function setValueString($value) {
		$this->setEMail($value);
	}
	function getValueString() {
		return $this->getEMail();
	}
	function setEMail($value) {
		$this->email = $value;
	}
	function getEMail() {
		return $this->email;
	}
	function getAddressCount() {
		return $this->email ? 1 : 0; // Immer nur eine Mail
	}
	function getAddresses() {
		return $this->email ? array($this->email) : array();
	}
	function getName() {
		return $this->email ? $this->email : 'unknown';
	}

	function getSingleAddress($idx) {
		$ret['address'] = $this->email;
		// TODO: Die AddressID ist notwendig, um beim Versionswechsel kein Mails doppelt zu verschicken.
		$ret['addressid'] = $ret['address'];
		return $ret;
	}

	/**
	 * Liefert die ConfId für den Reciver.
	 *
	 * @return 	string
	 */
	protected function getConfId() {
		return 'email.';
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_Email.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_Email.php']);
}
