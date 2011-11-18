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

tx_rnbase::load('tx_rnbase_model_base');
tx_rnbase::load('tx_mkmailer_mail_Address');

/**
 * Model für einen Datensatz der Tabelle tx_mkmailer_mailqueue.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 */
class tx_mkmailer_models_Template extends tx_rnbase_model_base {
	function getTableName(){return 'tx_mkmailer_templates';}
	/**
	 * Returns the Mail-Template.
	 *
	 * @return string
	 */
	function getContentText() {
		return $this->record['contenttext'];
	}
	/**
	 * Returns the Mail-Template HTML-Part.
	 *
	 * @return string
	 */
	function getContentHtml($plain=false) {
		if($plain) return $this->record['contenthtml'];

		tx_rnbase::load('tx_mkmailer_util_Misc');
		$ret = tx_mkmailer_util_Misc::getRTEText($this->record['contenthtml']);
		return $ret;
	}
	/**
	 * Liefert die BCCs als Adress-Array
	 *
	 * @return array[tx_mkmailer_mail_IAddress]
	 */
	public function getBccAddress() {
		return $this->getAddresses($this->getBcc());
	}
	private function getAddresses($addrStr) {
		$ret = array();
		if(!strlen(trim($addrStr))) return $ret;
		$addrArr = t3lib_div::trimExplode(',', $addrStr);
		foreach($addrArr As $addr) {
			$ret[] = new tx_mkmailer_mail_Address($addr);
		}
		return $ret;
	}
	public function getBcc() {
		return $this->record['mail_bcc'];
	}
	/**
	 * Liefert die CCs als Adress-Array
	 *
	 * @return array[tx_mkmailer_mail_IAddress]
	 */
	public function getCcAddress() {
		return $this->getAddresses($this->getCc());
	}
	public function getCc() {
		return $this->record['mail_cc'];
	}

	/**
	 * Liefert den Absender als Adresse
	 *
	 * @return tx_mkmailer_mail_IAddress
	 */
	function getFromAddress() {
		return new tx_mkmailer_mail_Address($this->record['mail_from'], $this->record['mail_fromName']);
	}
	function getFrom() {
		return $this->record['mail_from'];
	}
	function getFromName() {
		return $this->record['mail_fromName'];
	}
	function getSubject() {
		return $this->record['subject'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Template.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Template.php']);
}

?>