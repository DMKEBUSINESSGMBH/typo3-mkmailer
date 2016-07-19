<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 DMK E-BUSINESS GmbH (dev@dmk-ebusiness.de)
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
tx_rnbase::load('tx_mkmailer_mail_IMailJob');
tx_rnbase::load('tx_mkmailer_mail_IAttachment');
tx_rnbase::load('tx_rnbase_util_Files');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');

/**
 * Mail Factory
 *
 * Ein MailJob kann in die MailQueue eingestellt werden
 * und wird zu einem späteren Zeitpunkt verarbeitet.
 *
 * @package TYPO3
 * @subpackage mkmailer
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mail_Factory
{
	/**
	 * Creates a mail job.
	 *
	 * @param array[tx_mkmailer_receiver_IMailReceiver] $receiver
	 * @param tx_mkmailer_models_Template $templateObj
	 *
	 * @return tx_mkmailer_mail_MailJob
	 */
	public static function createMailJob(
		array $receiver = array(),
		tx_mkmailer_models_Template &$templateObj = null
	) {
		return tx_rnbase::makeInstance(
			'tx_mkmailer_mail_MailJob',
			$receiver,
			$templateObj
		);
	}

	/**
	 * Erstellt ein Datei-Attachment. Wenn ein relativer Pfad übergeben wird,
	 * dann wird dieser automatisch in einen absoluten TYPO3-Pfad umgewandelt.
	 *
	 * @param string $path
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 *
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createAttachment(
		$path,
		$name = '',
		$encoding = 'base64',
		$mimeType = false
	) {
		return self::createAttachmentInstance(
			tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
			self::makeAbsPath($path),
			$name,
			'',
			$encoding,
			$mimeType
		);
	}

	/**
	 * Erstellt einen absoluten TYPO3-Pfad.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function makeAbsPath($path)
	{
		if (!tx_rnbase_util_Files::isAbsPath($path)) {
			$path = tx_rnbase_util_Files::getFileAbsFileName(
				Tx_Rnbase_Utility_T3General::fixWindowsFilePath($path)
			);
		}

		return $path;
	}

	/**
	 * Creates an Attachment by the content of the file.
	 *
	 * @param string $content
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 *
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createStringAttachment(
		$content,
		$name = '',
		$encoding = 'base64',
		$mimeType = false
	) {
		return self::createAttachmentInstance(
			tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
			$content,
			$name,
			'',
			$encoding,
			$mimeType
		);
	}

	/**
	 * Creates an embedded attachment.
	 *
	 * Will be used for images etc, those will be shorn in de mail directly
	 *
	 * @param string $path
	 * @param string $embedId Content ID of the attachment.  Use this to identify
	 * @param string $name
	 * @param string $encoding
	 * @param string $mimeType
	 *
	 * @return tx_mkmailer_mail_IAttachment
	 */
	public static function createEmbeddedAttachment(
		$path,
		$embedId,
		$name = '',
		$encoding = 'base64',
		$mimeType = false
	) {
		return self::createAttachmentInstance(
			tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
			self::makeAbsPath($path),
			$name,
			$embedId,
			$encoding,
			$mimeType
		);
	}

	/**
	 * Creates an instance of the attachment model
	 *
	 * @param int $type One const tx_mkmailer_mail_IAttachment::TYPE_*
	 * @param string $absPathOrContent
	 * @param string $name
	 * @param string $embedId
	 * @param string $encoding
	 * @param string $mimeType
	 *
	 * @return tx_mkmailer_mail_Attachment
	 */
	private static function createAttachmentInstance(
		$type,
		$absPathOrContent,
		$name = '',
		$embedId = '',
		$encoding = 'base64',
		$mimeType = false
	) {
		/* @var $attachment tx_mkmailer_mail_Attachment */
		$attachment = tx_rnbase::makeInstance(
			'tx_mkmailer_mail_Attachment',
			$type
		);

		$attachment->setPathOrContent($absPathOrContent);
		$attachment->setName($name);
		$attachment->setEmbedId($embedId);
		$attachment->setEncoding($encoding);

		if ($mimeType === false) {
			$mimeType = 'application/octet-stream';
		}
		$attachment->setMimeType($mimeType);

		return $attachment;
	}

	/**
	 * Creates an instance of an address model
	 *
	 * @param string $address
	 * @param string $name
	 *
	 * @return tx_mkmailer_mail_Address
	 */
	public static function createAddressInstance(
		$address,
		$name = ''
	) {
		return tx_rnbase::makeInstance(
			'tx_mkmailer_mail_Address',
			$address,
			$name
		);
	}
}
