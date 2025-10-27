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

use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Frontend\Marker\FormatUtil;
use Sys25\RnBase\Frontend\Marker\SimpleMarker;
use Sys25\RnBase\Testing\BaseTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Hannes Bochmann <hannes.bochmann@dmk-business.de>
 */
class tx_mkmailer_tests_receiver_ModelTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $property = new ReflectionProperty(Sys25\RnBase\Frontend\Marker\Templates::class, 'substCacheEnabled');
        $property->setValue(null, false);
    }

    /**
     * @group unit
     */
    public function testConstructSetsEmail(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'email');

        $this->assertEquals('testMail', $property->getValue($receiver), 'falsche Email');
    }

    /**
     * @group unit
     */
    public function testConstructSetsModelUid(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');

        $this->assertEquals('123', $property->getValue($receiver), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testSetModelUid(): void
    {
        $receiver = $this->getReceiver();

        $receiver->setModelUid(456);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');

        $this->assertEquals('456', $property->getValue($receiver), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testGetModelUid(): void
    {
        $receiver = $this->getReceiver();

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
        $property->setValue($receiver, 456);

        $this->assertEquals('456', $receiver->getModelUid(), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testGetValueString(): void
    {
        $receiver = $this->getReceiver(['test_Mail', 123]);
        $this->assertEquals(
            'test_Mail'.tx_mkmailer_receiver_Model::EMAIL_MODEL_DELIMTER.'123',
            $receiver->getValueString(),
            'falscher value string'
        );
    }

    /**
     * @group unit
     */
    public function testSetValueStringSetsCorrectEmail(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $receiver->setValueString('newTest_Mail'.tx_mkmailer_receiver_Model::EMAIL_MODEL_DELIMTER.'456');
        $this->assertEquals('newTest_Mail', $receiver->getEmail(), 'falsche Email');
    }

    /**
     * @group unit
     */
    public function testSetValueStringSetsCorrectModelUid(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $receiver->setValueString('newTest_Mail'.tx_mkmailer_receiver_Model::EMAIL_MODEL_DELIMTER.'456');
        $this->assertEquals(456, $receiver->getModelUid(), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailTextCorrect(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailText = '###MODEL_UID###';
        $formatter = GeneralUtility::makeInstance(
            FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = '';
        $idx = null;
        $mailHtml = '';
        $mailSubject = '';

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailText, 'mailText falsch geparsed');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailHtmlCorrect(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailHtml = '###MODEL_UID###';
        $formatter = GeneralUtility::makeInstance(
            FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = '';
        $idx = null;
        $mailText = '';
        $mailSubject = '';

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailHtml, 'mailHtml falsch geparsed');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailSubjectCorrect(): void
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailSubject = '###MODEL_UID###';
        $formatter = GeneralUtility::makeInstance(
            FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = '';
        $idx = null;
        $mailText = '';
        $mailHtml = '';

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailSubject, 'mailSubject falsch geparsed');
    }

    /**
     * @return tx_mkmailer_receiver_Model
     */
    private function getReceiver(array $constuctorAgruments = []): PHPUnit\Framework\MockObject\MockObject
    {
        $receiver = $this->getMockBuilder('tx_mkmailer_receiver_Model')
            ->setConstructorArgs($constuctorAgruments)
            ->onlyMethods(['getModel', 'getModelMarker', 'getMarkerClass'])
            ->getMock();

        $model = $this->getModel(['uid' => 123], BaseModel::class, ['getColumnNames']);

        $receiver->expects($this->any())
            ->method('getModel')
            ->willReturn($model);

        $receiver->expects($this->any())
            ->method('getModelMarker')
            ->willReturn('MODEL');

        $receiver->expects($this->any())
            ->method('getMarkerClass')
            ->willReturn(SimpleMarker::class);

        return $receiver;
    }
}
