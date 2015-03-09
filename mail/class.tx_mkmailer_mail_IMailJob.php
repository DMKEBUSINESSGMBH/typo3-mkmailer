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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

/**
 *
 * tx_mkmailer_mail_IMailJob
 *
 * Ein MailJob kann in die MailQueue eingestellt werden und
 * wird zu einem späteren Zeitpunkt verarbeitet.
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
interface tx_mkmailer_mail_IMailJob {

	/**
	 * @return string
	 */
	public function getReceiver();

	/**
	 * @return string
	 */
	public function getContentText();

	/**
	 * @return string
	 */
	public function getContentHtml();

	/**
	 * @return string
	 */
	public function getSubject();

	/**
	 * @return array
	 */
	public function getAttachments();

	/**
	 * Liefert die Absenderadresse
	 * @return tx_mkmailer_mail_IAddress
	 */
	public function getFrom();

	/**
	 * Liefert die TO-Empfänger
	 * @return array[tx_mkmailer_mail_IAddress]
	 */
	public function getTOs();

	/**
	 * Liefert die CC-Empfänger
	 * @return array[tx_mkmailer_mail_IAddress]
	 */
	public function getCCs();

	/**
	 * Liefert die BCC-Empfänger
	 * @return array[tx_mkmailer_mail_IAddress]
	 */
	public function getBCCs();
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IMailJob.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IMailJob.php']);
}