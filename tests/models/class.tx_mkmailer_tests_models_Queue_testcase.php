<?php
/**
 *  Copyright notice
 *
 *  (c) 2011 das MedienKombinat <kontakt@das-medienkombinat.de>
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
tx_rnbase::load('tx_mkmailer_models_Queue');


/**
 * 
 */
class tx_mkmailer_tests_models_Queue_testcase extends tx_phpunit_testcase {


	public function testAttachmentWithStrings() {
		$queue = tx_rnbase::makeInstance('tx_mkmailer_models_Queue', array('uid'=>123, 'attachments' => '/uploadfolder/myfile.jpg, /uploadfolder/yourfile.jpg'));
		$attachments = $queue->getUploads();

		$this->assertEquals(2, count($attachments), 'Wrong size of attachments');
		$this->assertTrue($attachments[0] instanceof tx_mkmailer_mail_IAttachment, 'Interface not implemented.');
		$this->assertEquals('/uploadfolder/myfile.jpg', $attachments[0]->getPathOrContent() , 'File is wrong.');
	}

	public function tesstAttachmentWithSerializedObjects() {
		// geht leider nicht. Deserialisierung klappt nicht.
		$serData = 'a:2:{i:0;O:27:"tx_mkmailer_mail_Attachment":6:{s:33:"tx_mkmailer_mail_Attachmenttype";i:0;s:42:"tx_mkmailer_mail_AttachmentpathOrContent";s:24:"/uploadfolder/myfile.jpg";s:33:"tx_mkmailer_mail_Attachmentname";s:0:"";s:36:"tx_mkmailer_mail_AttachmentembedId";N;s:37:"tx_mkmailer_mail_AttachmentmimeType";s:24:"application/octet-stream";s:37:"tx_mkmailer_mail_Attachmentencoding";s:6:"base64";}i:1;O:27:"tx_mkmailer_mail_Attachment":6:{s:33:"tx_mkmailer_mail_Attachmenttype";i:0;s:42:"tx_mkmailer_mail_AttachmentpathOrContent";s:26:"/uploadfolder/yourfile.jpg";s:33:"tx_mkmailer_mail_Attachmentname";s:0:"";s:36:"tx_mkmailer_mail_AttachmentembedId";N;s:37:"tx_mkmailer_mail_AttachmentmimeType";s:24:"application/octet-stream";s:37:"tx_mkmailer_mail_Attachmentencoding";s:6:"base64";}}';

		$queue = tx_rnbase::makeInstance('tx_mkmailer_models_Queue', array('uid'=>123, 'attachments' => $serData));
		$attachments = $queue->getUploads();
		$this->assertEquals(2, count($attachments), 'Wrong size of attachments');
		$this->assertTrue($attachments[0] instanceof tx_mkmailer_mail_IAttachment, 'Interface not implemented.');
		$this->assertEquals('/uploadfolder/myfile.jpg', $attachments[0]->getPathOrContent() , 'File is wrong.');
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/models/class.tx_mkmailer_tests_models_Queue_testcase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/models/class.tx_mkmailer_tests_models_Queue_testcase.php']);
}