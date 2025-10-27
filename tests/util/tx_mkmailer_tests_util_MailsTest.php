<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkmailer" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Hannes Bochmann
 */
class tx_mkmailer_tests_util_MailsTest extends tx_mkmailer_tests_util_MailsBaseTestCase
{
    /**
     * @group unit
     */
    public function testGetMailService(): void
    {
        $method = new ReflectionMethod('tx_mkmailer_util_Mails', 'getMailService');
        $this->assertInstanceOf(
            'tx_mkmailer_services_Mail',
            $method->invoke(GeneralUtility::makeInstance('tx_mkmailer_util_Mails'))
        );
    }

    /**
     * @group unit
     */
    public function testSendModelReceiverMailSpoolsCorrectMailJobWhenTemplateKeyGiven(): void
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
    public function testSendModelReceiverMailSpoolsCorrectMailJobWhenTemplateObjectGiven(): void
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

    protected function getMailUtilClass(): string
    {
        return 'tx_mkmailer_util_Mails';
    }
}

class tx_mkmailer_tests_util_ReceiverDummy extends tx_mkmailer_receiver_Email
{
    protected function getModel(): string
    {
        return 'model';
    }

    protected function getModelMarker(): string
    {
        return 'modelMarker';
    }

    protected function getMarkerClass(): string
    {
        return 'markerClass';
    }
}
