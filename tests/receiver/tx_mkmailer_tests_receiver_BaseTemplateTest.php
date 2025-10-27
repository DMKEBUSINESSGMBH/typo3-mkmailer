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

use Sys25\RnBase\Configuration\Processor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectFactory;

/**
 *  Test Receiver Object.
 */
class tx_mkmailer_tests_receiver_BaseTemplate extends tx_mkmailer_receiver_BaseTemplate
{
    public $addAdditionalData = false;

    protected function getConfId(): string
    {
        return 'basetemplate.';
    }

    public function getAddressCount()
    {
    }

    public function getAddresses()
    {
    }

    public function getName()
    {
    }

    public function getSingleAddress($idx): array
    {
        return ['address' => 'ich@da.com', 'addressid' => 'ich@da.com'];
    }

    public function getValueString()
    {
    }

    public function setValueString($value)
    {
    }

    protected function addAdditionalData(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx)
    {
        if ($this->addAdditionalData) {
            $mailText .= 'addAdditionalData';
            $mailHtml .= 'addAdditionalData';
        }
    }
}

/**
 *  Test Receiver Object mit email variable.
 */
class tx_mkmailer_tests_receiver_BaseTemplateWithEmailObjectVariable extends tx_mkmailer_tests_receiver_BaseTemplate
{
    protected $email = 'john@doe.com';
}

/**
 * tx_mkmailer_tests_receiver_BaseTemplate_testcase.
 *
 * @author          Hannes Bochmann <hannes.bochmann@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_receiver_BaseTemplateTest extends TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['mkmailer'] = [
            'charset' => '',
            'encoding' => '',
            'returnpath' => '',
        ];
        $GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] = realpath(__DIR__.'/..');

        $this->injectMarkerBasedTemplateService();

        $property = new ReflectionProperty(Sys25\RnBase\Frontend\Marker\Templates::class, 'substCacheEnabled');
        $property->setValue(null, false);

        $GLOBALS['TSFE'] = new stdClass();
        $GLOBALS['TSFE']->no_cache = 1;
    }

    protected function injectMarkerBasedTemplateService(): void
    {
        $markerBasedTemplateService = $this->getMockBuilder(TYPO3\CMS\Core\Service\MarkerBasedTemplateService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fillInMarkerArray'])
            ->getMock();

        GeneralUtility::addInstance(TYPO3\CMS\Core\Service\MarkerBasedTemplateService::class, $markerBasedTemplateService);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();

        parent::tearDown();

        if (isset($GLOBALS['TYPO3_REQUEST'])) {
            unset($GLOBALS['TYPO3_REQUEST']);
        }
    }

    /**
     * @return Processor
     */
    private function getConfigurations(array $configArray = [])
    {
        $configArray = ['sendmails.' => $configArray];

        return Sys25\RnBase\Testing\TestUtility::createConfigurations($configArray, 'mkmailer');
    }

    /**
     * @return tx_mkmailer_tests_receiver_BaseTemplate
     */
    private function getReceiver(string $class = 'tx_mkmailer_tests_receiver_BaseTemplate'): object
    {
        return GeneralUtility::makeInstance($class);
    }

    /**
     * @return tx_mkmailer_receiver_BaseTemplate
     */
    private function getQueue(array $data = []): object
    {
        $data['uid'] ??= 0;
        $data['contenttext'] ??= 'Text für TEXT<br />';
        $data['contenthtml'] ??= 'Text für HTML<br />';
        $data['subject'] ??= 'Subject';

        return GeneralUtility::makeInstance('tx_mkmailer_models_Queue', $data);
    }

    public function testGetSingleMailWithoutWrap(): void
    {
        $confId = 'sendmails.';
        $configurations = $this->getConfigurations();
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('Text für HTML<br />', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('Text für TEXT', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrongTemplate(): void
    {
        $confId = 'sendmails.';
        $configArray = [
            'basetemplate.' => [
                'wrapTemplate' => '1',
                'htmlTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/wrongtext.html',
                'textTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/wronghtml.html',
            ],
        ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('<!-- TEMPLATE NOT FOUND: '.TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/wrongtext.html -->Text für HTML<br />', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('Text für TEXT', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedTemplate(): void
    {
        $this->injectMarkerBasedTemplateService();

        $confId = 'sendmails.';
        $configArray = [
            'basetemplate.' => [
                'wrapTemplate' => '1',
                'textTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraptext.html',
                'htmlTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraphtml.html',
            ],
        ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTEMPLATEText für TEXT', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedTemplateAndAdditionalData(): void
    {
        $this->injectMarkerBasedTemplateService();

        $confId = 'sendmails.';
        $configArray = [
            'basetemplate.' => [
                'wrapTemplate' => '1',
                'textTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraptext.html',
                'htmlTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraphtml.html',
            ],
        ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $receiver->addAdditionalData = true;

        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>addAdditionalData', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".'addAdditionalData', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedDefaultTemplateAndAdditionalData(): void
    {
        $this->injectMarkerBasedTemplateService();

        $confId = 'sendmails.';
        $configArray = [
            'basetemplateTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwrap.html',
            'basetemplate.' => [
                'wrapTemplate' => '1',
            ],
        ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $receiver->addAdditionalData = true;

        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>addAdditionalData', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".'addAdditionalData', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedDefaultTemplateAndCustomSubpart(): void
    {
        $this->injectMarkerBasedTemplateService();

        $confId = 'sendmails.';
        $configArray = [
            'basetemplateTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwrap.html',
            'basetemplate.' => [
                'wrapTemplate' => '1',
                'textSubpart' => '###TESTTEXT###',
                'htmlSubpart' => '###TESTHTML###',
            ],
        ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTESTTEMPLATE<html>Text für HTML<br /></html>', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTESTTEMPLATEText für TEXT', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedTemplateAndDcMarker(): void
    {
        // @todo why is it neccessary to add the instance twice?
        $this->injectMarkerBasedTemplateService();

        if (!Sys25\RnBase\Utility\TYPO3::isTYPO115OrHigher()) {
            self::markTestSkipped('The DC marker is not parsed in TYPO3 10.4');
        }

        $confId = 'sendmails.';
        $configArray = [
            'basetemplate.' => [
                'wrapTemplate' => '1',
                'textTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraptext.html',
                'textSubpart' => '###CONTENTTEXT_DCMARKER###',
                'htmlTemplate' => TYPO3\CMS\Core\Core\Environment::getProjectPath().'/TestFixtures/mailwraphtml.html',
                'htmlSubpart' => '###CONTENTHTML_DCMARKER###',
                'receivertext.' => [],
            ],
        ];
        $configArray['basetemplate.']['receivertext.']['dctest']
            = $configArray['basetemplate.']['receiverhtml.']['dctest']
                = 'TEXT';
        $configArray['basetemplate.']['receivertext.']['dctest.']['value']
            = $configArray['basetemplate.']['receiverhtml.']['dctest.']['value']
                = 'Hallo Welt';
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();

        $GLOBALS['TYPO3_REQUEST'] = new TYPO3\CMS\Core\Http\ServerRequest();
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['TEXT'] = TYPO3\CMS\Frontend\ContentObject\TextContentObject::class;

        $contentObjectFactory = $this->getMockBuilder(ContentObjectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getContentObject'])
            ->getMock();
        $contentObjectFactory->expects(self::any())
            ->method('getContentObject')
            ->with('TEXT')
            ->willReturn(new TYPO3\CMS\Frontend\ContentObject\TextContentObject());

        // @todo why is it neccessary to add the instance twice?
        GeneralUtility::addInstance(ContentObjectFactory::class, $contentObjectFactory);
        GeneralUtility::addInstance(ContentObjectFactory::class, $contentObjectFactory);

        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html> ich@da.com ich@da.com Hallo Welt', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".' ich@da.com ich@da.com Hallo Welt', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailUsesGetSingleAdressIfObjectVariableEmailNotSet(): void
    {
        $confId = 'sendmails.';
        $configurations = $this->getConfigurations();
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $expectedTo = GeneralUtility::makeInstance('tx_mkmailer_mail_Address', 'ich@da.com', '');
        $this->assertEquals([$expectedTo], $msg->getTo(), 'to wrong.');
    }

    public function testGetSingleMailUsesObjectVariableEmailIfSet(): void
    {
        $confId = 'sendmails.';
        $configurations = $this->getConfigurations();
        $receiver = $this->getReceiver('tx_mkmailer_tests_receiver_BaseTemplateWithEmailObjectVariable');
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $expectedTo = GeneralUtility::makeInstance('tx_mkmailer_mail_Address', 'john@doe.com', '');
        $this->assertEquals([$expectedTo], $msg->getTo(), 'to wrong.');
    }
}
