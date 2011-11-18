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

tx_rnbase::load('tx_mkmailer_receiver_Base');
/**
 * Implementierung für einen Mailempfänger vom Typ FeUser.
 * 
 * @TODO: auf tx_mkmailer_receiver_BaseTemplate umstellen
 */
class tx_mkmailer_receiver_FeUser extends tx_mkmailer_receiver_Base {
	protected $obj;
	function setValueString($value) {
		tx_rnbase::load('tx_t3users_models_feuser');
		$this->setFeUser(tx_t3users_models_feuser::getInstance(intval($value)));
		
	}
	function setFeUser($feuser) {
		$this->obj = $feuser;
	}
	function getAddressCount() {
		return is_object($this->obj) ? 1 : 0; // Immer nur eine Person
	}
	function getAddresses() {
		if(!$this->getEmail()) return array();
		return array($this->getEmail());
	}
	function getName() {
		if(!is_object($this->obj) || !$this->obj->isValid()) return 'unknown';
		
		return $this->obj->record['username'];
	}

	function getSingleAddress($idx) {
		$member = $this->obj;
		$ret['address'] = $this->getEmail();
		$ret['addressName'] = $member->record['vname'] . ' ' . $member->record['nname'];
		// TODO: Die AddressID ist notwendig, um beim Versionswechsel kein Mails doppelt zu verschicken.
		$ret['addressid'] = $ret['address'] . '_'. $ret['addressName'];
		return $ret;
	}

	/**
	 * Erstellt eine individuelle Email für einen Empfänger der Email.
	 *
	 * @param tx_mkmailer_models_Queue $queue 1. Zeile wird als Betreff verwendet!
	 * @param tx_rnbase_util_FormatUtil $formatter
	 * @param string $confId 
	 * @param int $idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return tx_mkmailer_mail_IMessage
	 */
	function getSingleMail($queue, &$formatter, $confId, $idx) {
		$markerClass = tx_rnbase::makeInstanceClassName('tx_t3users_util_FeUserMarker');
		$marker = new $markerClass;
		$mailText = $marker->parseTemplate($queue->getContentText(), $this->obj, $formatter, $confId.'receiver.', 'RECEIVER');
		$mailHtml = $marker->parseTemplate($queue->getContentHtml(), $this->obj, $formatter, $confId.'receiver.', 'RECEIVER');
		$mailSubject = $marker->parseTemplate($queue->getSubject(), $this->obj, $formatter, $confId.'receiver.', 'RECEIVER');

		$msg = tx_rnbase::makeInstance('tx_mkmailer_mail_SimpleMessage');
		$msg->addTo($this->getEmail());
		$msg->setTxtPart($mailText);
		$msg->setHtmlPart($mailHtml);
		$msg->setSubject($mailSubject);
		
		return $msg;
	}
	
	protected function getEmail() {
		if(!is_object($this->obj) || !isset($this->obj->record['email'])) return false;
		//else
		return $this->obj->record['email'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_FeUser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_FeUser.php']);
}
?>