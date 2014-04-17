<?php
/**
 *  Copyright notice
 *
 *  (c) 2014 das MedienKombinat GmbH
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
 */

/**
 * benötigte Klassen einbinden
 */
require_once(t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php'));
tx_rnbase::load('tx_mkmailer_tests_util_MailsBaseTestCase');
tx_rnbase::load('tx_mkmailer_util_Mails');
tx_rnbase::load('tx_mkmailer_services_Mail');
tx_rnbase::load('tx_mklib_tests_Util');
tx_rnbase::load('tx_mkmailer_receiver_Email');

/**
 * @author Hannes Bochmann
 */
class tx_mkmailer_tests_util_Mails_testcase extends tx_mkmailer_tests_util_MailsBaseTestCase {

	/**
	 * @group unit
	 */
	public function testMailService() {
		$method = new ReflectionMethod('tx_mkmailer_util_Mails', 'getMailService');
		$method->setAccessible(true);
		$this->assertInstanceOf(
			'tx_mkmailer_services_Mail',
			$method->invoke(null)
		);
	}

	/**
	 * @group unit
	 */
	public function testSendModelReceiverMailSpoolsCorrectMailJob() {
		$mailService = $this->getMailServiceMock();

		$templateObj = tx_rnbase::makeInstance(
			'tx_mkmailer_models_Template',
			array(
				'contenttext' => '###MODEL_NAME###',
				'contenthtml' => '###MODEL_NAME### html',
				'mail_from' => 'typo3site',
				'mail_cc' => 'gchq',
				'mail_bcc' => 'nsa',
				'subject' => 'test mail',
			)
		);
		$mailService->expects($this->once())
			->method('getTemplate')
			->with('mailTemplate')
			->will($this->returnValue($templateObj));

		$receiver = tx_rnbase::makeInstance('tx_mkmailer_tests_util_ReceiverDummy', 'testReceiver', 123);

		$expectedJob = tx_rnbase::makeInstance('tx_mkmailer_mail_MailJob');
		$expectedJob->addReceiver($receiver);
		$expectedJob->setFrom($templateObj->getFromAddress());
		$expectedJob->setCCs($templateObj->getCcAddress());
		$expectedJob->setBCCs($templateObj->getBccAddress());
		$expectedJob->setSubject($templateObj->getSubject());
		$expectedJob->setContentText($templateObj->getContentText());
		$expectedJob->setContentHtml($templateObj->getContentHtml());

		$mailService->expects($this->once())
			->method('spoolMailJob')
			->with($expectedJob);

		$mailUtil = $this->getMailUtilMock($mailService);
		$mailUtil::sendModelReceiverMail(
			'tx_mkmailer_tests_util_ReceiverDummy', 123, 'testReceiver', 'mailTemplate'
		);
	}
}

class tx_mkmailer_tests_util_ReceiverDummy extends tx_mkmailer_receiver_Email {
	protected function getModel() {
		return 'model';
	}
	protected function getModelMarker() {
		return 'modelMarker';
	}
	protected function getMarkerClass() {
		return 'markerClass';
	}
}