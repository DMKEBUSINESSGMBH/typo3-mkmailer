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
tx_rnbase::load('tx_mkmailer_mail_IAttachment');

class tx_mkmailer_mail_Attachment implements tx_mkmailer_mail_IAttachment {

	private $type = tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT;
	private $pathOrContent;
	private $name;
	private $embedId;
	private $mimeType = 'application/octet-stream';
	private $encoding = 'base64';
	/**
	 * 
	 * @param int $type @see tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT
	 */
	public function __construct($type){
		$this->setAttachmentType($type);
	}

	public function getPathOrContent() {
		return $this->pathOrContent;
	}
	public function setPathOrContent($pathOrContent) {
		$this->pathOrContent = $pathOrContent;
	}
	
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}

	public function getEmbedId() {
		return $this->embedId;
	}
	public function setEmbedId($embedId) {
		$this->embedId = $embedId;
	}

	public function getMimeType() {
		return $this->mimeType;
	}
	public function setMimeType($mimeType) {
		$this->mimeType = $mimeType;
	}

	public function getEncoding() {
		return $this->encoding;
	}
	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}
	
	public function getAttachmentType() {
		return $this->type;
	}
	public function setAttachmentType($type) {
		$this->type = $type;
	}
	
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_Attachment.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mail_Attachment.php']);
}

?>