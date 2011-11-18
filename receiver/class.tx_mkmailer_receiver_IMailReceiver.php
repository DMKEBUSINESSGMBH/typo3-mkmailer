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
 * Interface für Empfänger einer Email. Die Implementierung dieses Interface steht dabei nicht
 * unbedingt für einen einzelnen Empfänger, sondern kann auch für ganze Gruppen stehen.
 *
 */
interface tx_mkmailer_receiver_IMailReceiver {
	/**
	 * Returns the number of receivers
	 * @return int
	 */
	function getAddressCount();
	/**
	 * Returns an Array with mail addresses
	 * @return array of string
	 */
	function getAddresses();
	/**
	 * Returns a name for receiver or receiver group
	 * @return string
	 */
	function getName();
	/**
	 * Erstellt eine individuelle Email für einen Empfänger der Email.
	 *
	 * @param tx_mkmailer_models_Queue $queue 1. Zeile wird als Betreff verwendet!
	 * @param tx_rnbase_util_FormatUtil $formatter
	 * @param string $confId 
	 * @param int $idx Index des Empfängers von 0 bis (getAddressCount() - 1)
   * @return tx_mkmailer_mail_IMessage
	 */
	function getSingleMail($queue, &$formatter, $confId, $idx);
	/**
	 * Liefert die Mailadresse mit dem gewünschten Index! Die Klasse muss sicherstellen, daß 
	 * für den identischen Index bei den Methode getSingleAddress und getSingleMail der identische
	 * Empfänger geliefert wird!
	 *
	 * @param int $idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 */
	function getSingleAddress($idx);
	
	/**
	 * Liefert den Wert für die Speicherung der Daten in der DB. üblicherweise sollte das die
	 * UID des Datenobjektes sein.
	 *
	 */
	function getValueString();
	/**
	 * Initialisiert das Objekt mit einem Datenwert. Das ist üblicherweise eine UID. Die konkrete
	 * Instanz sollte daraus das passenden Datenobjekt erstellen können.
	 *
	 * @param string $value
	 */
	function setValueString($value);
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_IMailReceiver.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_IMailReceiver.php']);
}

?>