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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

/**
 * tx_mkmailer_mail_IAttachment
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
interface tx_mkmailer_mail_IAttachment {

	/**
	 * @var int
	 */
	const TYPE_ATTACHMENT = 0;

	/**
	 * @var int
	 */
	const TYPE_EMBED = 1;

	/**
	 * @var int
	 */
	const TYPE_STRING = 2;

	/**
	 * @return string
	 */
	public function getPathOrContent();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getEmbedId();

	/**
	 * @return string
	 */
	public function getMimeType();

	/**
	 * @return string
	 */
	public function getEncoding();

	/**
	 * @return int
	 */
	public function getAttachmentType();
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IAttachment.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_IAttachment.php']);
}