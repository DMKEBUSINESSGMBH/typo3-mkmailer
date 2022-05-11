<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *  Copyright notice.
 *
 *  (c) 2014 DMK E-BUSINESS GmbH
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

/*
 * benötigte Klassen einbinden
 */

/**
 * @author Hannes Bochmann
 */
class tx_mkmailer_tests_util_MailsTest extends tx_mkmailer_tests_util_MailsBaseTestCase
{
    /**
     * @group unit
     */
    public function testGetMailService()
    {
        $method = new ReflectionMethod('tx_mkmailer_util_Mails', 'getMailService');
        $method->setAccessible(true);
        $this->assertInstanceOf(
            'tx_mkmailer_services_Mail',
            $method->invoke(GeneralUtility::makeInstance('tx_mkmailer_util_Mails'))
        );
    }

    /**
     * @group unit
     */
    public function testSendModelReceiverMailSpoolsCorrectMailJobWhenTemplateKeyGiven()
    {
        $mailService = $this->getMailServiceMock();

        $templateObj = GeneralUtility::makeInstance(
            'tx_mkmailer_models_Template',
            [
                'contenttext' => '###MODEL_NAME###',
                'contenthtml' => '###MODEL_NAME### html',
                'mail_from' => 'typo3site',
                'mail_cc' => 'gchq',
                'mail_bcc' => 'nsa',
                'subject' => 'test mail',
            ]
        );
        $mailService->expects($this->once())
            ->method('getTemplate')
            ->with('mailTemplate')
            ->will($this->returnValue($templateObj));

        $receiver = GeneralUtility::makeInstance('tx_mkmailer_tests_util_ReceiverDummy', 'testReceiver', 123);

        $expectedJob = GeneralUtility::makeInstance('tx_mkmailer_mail_MailJob');
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
        $mailUtil->sendModelReceiverMail(
            'tx_mkmailer_tests_util_ReceiverDummy',
            123,
            'testReceiver',
            'mailTemplate'
        );
    }

    /**
     * @group unit
     */
    public function testSendModelReceiverMailSpoolsCorrectMailJobWhenTemplateObjectGiven()
    {
        $mailService = $this->getMailServiceMock();

        $templateObj = GeneralUtility::makeInstance(
            'tx_mkmailer_models_Template',
            [
                'contenttext' => '###MODEL_NAME###',
                'contenthtml' => '###MODEL_NAME### html',
                'mail_from' => 'typo3site',
                'mail_cc' => 'gchq',
                'mail_bcc' => 'nsa',
                'subject' => 'test mail',
            ]
        );
        $mailService->expects($this->never())
            ->method('getTemplate');

        $receiver = GeneralUtility::makeInstance('tx_mkmailer_tests_util_ReceiverDummy', 'testReceiver', 123);

        $expectedJob = GeneralUtility::makeInstance('tx_mkmailer_mail_MailJob');
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
        $mailUtil->sendModelReceiverMail(
            'tx_mkmailer_tests_util_ReceiverDummy',
            123,
            'testReceiver',
            $templateObj
        );
    }

    protected function getMailUtilClass()
    {
        return 'tx_mkmailer_util_Mails';
    }
}

class tx_mkmailer_tests_util_ReceiverDummy extends tx_mkmailer_receiver_Email
{
    protected function getModel()
    {
        return 'model';
    }

    protected function getModelMarker()
    {
        return 'modelMarker';
    }

    protected function getMarkerClass()
    {
        return 'markerClass';
    }
}
