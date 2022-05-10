<?php
/**
 *  Copyright notice.
 *
 *  (c) 2014 Hannes Bochmann <dev@dmk-ebusiness.de>
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
 * @author Hannes Bochmann <hannes.bochmann@dmk-business.de>
 */
class tx_mkmailer_tests_receiver_ModelTest extends \Sys25\RnBase\Testing\BaseTestCase
{
    /**
     * @group unit
     */
    public function testConstructSetsEmail()
    {
        $receiver = $this->getReceiver(['testMail', 123]);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'email');
        $property->setAccessible(true);

        $this->assertEquals('testMail', $property->getValue($receiver), 'falsche Email');
    }

    /**
     * @group unit
     */
    public function testConstructSetsModelUid()
    {
        $receiver = $this->getReceiver(['testMail', 123]);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
        $property->setAccessible(true);

        $this->assertEquals('123', $property->getValue($receiver), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testSetModelUid()
    {
        $receiver = $this->getReceiver();

        $receiver->setModelUid(456);

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
        $property->setAccessible(true);

        $this->assertEquals('456', $property->getValue($receiver), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testGetModelUid()
    {
        $receiver = $this->getReceiver();

        $property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
        $property->setAccessible(true);
        $property->setValue($receiver, 456);

        $this->assertEquals('456', $receiver->getModelUid(), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testGetValueString()
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
    public function testSetValueStringSetsCorrectEmail()
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $receiver->setValueString('newTest_Mail'.tx_mkmailer_receiver_Model::EMAIL_MODEL_DELIMTER.'456');
        $this->assertEquals('newTest_Mail', $receiver->getEmail(), 'falsche Email');
    }

    /**
     * @group unit
     */
    public function testSetValueStringSetsCorrectModelUid()
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $receiver->setValueString('newTest_Mail'.tx_mkmailer_receiver_Model::EMAIL_MODEL_DELIMTER.'456');
        $this->assertEquals(456, $receiver->getModelUid(), 'falsche model Uid');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailTextCorrect()
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailText = '###MODEL_UID###';
        $formatter = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \Sys25\RnBase\Frontend\Marker\FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = $idx = null;

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->setAccessible(true);
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailText, 'mailText falsch geparsed');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailHtmlCorrect()
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailHtml = '###MODEL_UID###';
        $formatter = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \Sys25\RnBase\Frontend\Marker\FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = $idx = null;

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->setAccessible(true);
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailHtml, 'mailHtml falsch geparsed');
    }

    /**
     * @group unit
     */
    public function testAddAdditionalParsesMailSubjectCorrect()
    {
        $receiver = $this->getReceiver(['testMail', 123]);
        $mailSubject = '###MODEL_UID###';
        $formatter = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \Sys25\RnBase\Frontend\Marker\FormatUtil::class,
            $this->createConfigurations([], 'mkmailer')
        );
        $confId = $idx = null;

        $method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
        $method->setAccessible(true);
        $method->invokeArgs(
            $receiver,
            [&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx]
        );

        $this->assertEquals(123, $mailSubject, 'mailSubject falsch geparsed');
    }

    /**
     * @param array $constuctorAgruments
     *
     * @return tx_mkmailer_receiver_Model
     */
    private function getReceiver(array $constuctorAgruments = [])
    {
        $receiver = $this->getMockForAbstractClass(
            'tx_mkmailer_receiver_Model',
            $constuctorAgruments,
            '',
            true,
            true,
            true,
            ['getModel', 'getModelMarker', 'getMarkerClass']
        );

        $model = $this->getModel(['uid' => 123], \Sys25\RnBase\Domain\Model\BaseModel::class, ['getColumnNames']);

        $receiver->expects($this->any())
            ->method('getModel')
            ->will($this->returnValue($model));

        $receiver->expects($this->any())
            ->method('getModelMarker')
            ->will($this->returnValue('MODEL'));

        $receiver->expects($this->any())
            ->method('getMarkerClass')
            ->will($this->returnValue(\Sys25\RnBase\Frontend\Marker\SimpleMarker::class));

        return $receiver;
    }
}
