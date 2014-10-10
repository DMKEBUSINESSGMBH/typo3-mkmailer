<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 das MedienKombinat GmbH (kontakt@das-medienkombinat.de)
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

tx_rnbase::load('tx_mkmailer_mail_IMailJob');
tx_rnbase::load('tx_mkmailer_mail_IAttachment');


/**
 * Ein MailJob kann in die MailQueue eingestellt werden und wird zu einem späteren Zeitpunkt verarbeitet.
 */
class tx_mkmailer_mail_Factory {
	/**
	 * @param	array[tx_mkmailer_receiver_IMailReceiver] 	$receiver
	 * @param	tx_mkmailer_models_Template 						$templateObj
	 * @return 	tx_mkmailer_mail_MailJob
	 */
	public static function createMailJob(array $receiver = array(), tx_mkmailer_models_Template &$templateObj=null) {
		return tx_rnbase::makeInstance('tx_mkmailer_mail_MailJob', $receiver, $templateObj);
	}
	/**
	 * Erstellt ein Datei-Attachment. Wenn ein relativer Pfad übergeben wird, dann wird dieser automatisch in
	 * einen absoluten TYPO3-Pfad umgewandelt.
	 * @param string $path
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createAttachment($path, $name = '', $encoding = 'base64', $mimeType = 'application/octet-stream') {
		$att = self::createAttachmentInstance(tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT);
		$att->setPathOrContent(self::makeAbsPath($path));
		$att->setName($name);
		$att->setMimeType($mimeType);
		$att->setEncoding($encoding);
		
		return $att;
	}
	/**
	 * Erstellt einen absoluten TYPO3-Pfad
	 * @param string $path
	 */
	public static function makeAbsPath($path) {
		return t3lib_div::isAbsPath($path) ? $path : t3lib_div::getFileAbsFileName(t3lib_div::fixWindowsFilePath($path));
	}
	/**
	 *
	 * @param string $content
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createStringAttachment($content, $name = '', $encoding = 'base64', $mimeType = 'application/octet-stream') {
		$att = self::createAttachmentInstance(tx_mkmailer_mail_IAttachment::TYPE_STRING);

		$att->setPathOrContent($content);
		$att->setName($name);
		$att->setMimeType($mimeType);
		$att->setEncoding($encoding);
		
		return $att;
	}
	/**
	 *
	 * @param string $path
	 * @param string $embedId Content ID of the attachment.  Use this to identify
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createEmbeddedAttachment($path, $embedId, $name = '', $encoding = 'base64', $mimeType = 'application/octet-stream') {
		$att = self::createAttachmentInstance(tx_mkmailer_mail_IAttachment::TYPE_EMBED);

		$att->setPathOrContent(self::makeAbsPath($path));
		$att->setEmbedId($embedId);
		$att->setName($name);
		$att->setMimeType($mimeType);
		$att->setEncoding($encoding);
		
		return $att;
	}
	/**
	 *
	 * @param int $type
	 * @return tx_mkmailer_mail_Attachment
	 */
	private static function createAttachmentInstance($type) {
		return tx_rnbase::makeInstance('tx_mkmailer_mail_Attachment', $type);
	}
	/**
	 *
	 * @param string $address
	 * @param string $name
	 * @return tx_mkmailer_mail_Address
	 */
	public static function createAddressInstance($address, $name = '') {
		return tx_rnbase::makeInstance('tx_mkmailer_mail_Address', $address, $name);
	}
}


if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_Factory.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_Factory.php']);
}
