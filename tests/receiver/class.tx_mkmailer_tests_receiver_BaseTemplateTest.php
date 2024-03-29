<?php

use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Testing\BaseTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *  Copyright notice.
 *
 *  (c) 2011 DMK E-BUSINESS <dev@dmk-ebusiness.de>
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

/**
 *  Test Receiver Object.
 */
class tx_mkmailer_tests_receiver_BaseTemplate extends tx_mkmailer_receiver_BaseTemplate
{
    public $addAdditionalData = false;

    public function getConfId()
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

    public function getSingleAddress($idx)
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
class tx_mkmailer_tests_receiver_BaseTemplateTest extends BaseTestCase
{
    /**
     * @return Processor
     */
    private function getConfigurations(array $configArray = [])
    {
        $configurations = GeneralUtility::makeInstance(Processor::class);
        $configArray = ['sendmails.' => $configArray];

        $configurations->init(
            $configArray,
            $configurations->getCObj(1),
            'mkmailer',
            'mkmailer'
        );

        return $configurations;
    }

    /**
     * @param string $class
     *
     * @return  tx_mkmailer_tests_receiver_BaseTemplate
     */
    private function getReceiver($class = 'tx_mkmailer_tests_receiver_BaseTemplate')
    {
        return GeneralUtility::makeInstance($class);
    }

    /**
     * @return  tx_mkmailer_receiver_BaseTemplate
     */
    private function getQueue(array $data = [])
    {
        $data['uid'] = $data['uid'] ?? 0;
        $data['contenttext'] = $data['contenttext'] ?? 'Text für TEXT<br />';
        $data['contenthtml'] = $data['contenthtml'] ?? 'Text für HTML<br />';
        $data['subject'] = $data['subject'] ?? 'Subject';

        return GeneralUtility::makeInstance('tx_mkmailer_models_Queue', $data);
    }

    public function testGetSingleMailWithoutWrap()
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

    public function testGetSingleMailWithWrongTemplate()
    {
        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->no_cache = 1;

        $confId = 'sendmails.';
        $configArray = [
                'basetemplate.' => [
                        'wrapTemplate' => '1',
                        'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/wrongtext.html',
                        'textTemplate' => 'EXT:mkmailer/tests/fixtures/wronghtml.html',
                    ],
            ];
        $configurations = $this->getConfigurations($configArray);
        $receiver = $this->getReceiver();
        $queue = $this->getQueue();
        $formatter = $configurations->getFormatter();
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('<!-- TEMPLATE NOT FOUND: EXT:mkmailer/tests/fixtures/wrongtext.html -->Text für HTML<br />', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('Text für TEXT', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailWithWrappedTemplate()
    {
        $confId = 'sendmails.';
        $configArray = [
                'basetemplate.' => [
                        'wrapTemplate' => '1',
                        'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
                        'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
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

    public function testGetSingleMailWithWrappedTemplateAndAdditionalData()
    {
        $confId = 'sendmails.';
        $configArray = [
                'basetemplate.' => [
                        'wrapTemplate' => '1',
                        'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
                        'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
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

    public function testGetSingleMailWithWrappedDefaultTemplateAndAdditionalData()
    {
        $confId = 'sendmails.';
        $configArray = [
                'basetemplateTemplate' => 'EXT:mkmailer/tests/fixtures/mailwrap.html',
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

    public function testGetSingleMailWithWrappedDefaultTemplateAndCustomSubpart()
    {
        $confId = 'sendmails.';
        $configArray = [
                'basetemplateTemplate' => 'EXT:mkmailer/tests/fixtures/mailwrap.html',
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

    public function testGetSingleMailWithWrappedTemplateAndDcMarker()
    {
        if (!\Sys25\RnBase\Utility\TYPO3::isTYPO115OrHigher()) {
            self::markTestSkipped('The DC marker is not parsed in TYPO3 10.4');
        }

        $GLOBALS['TYPO3_REQUEST'] = new \TYPO3\CMS\Core\Http\ServerRequest();
        $GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects']['TEXT'] = \TYPO3\CMS\Frontend\ContentObject\TextContentObject::class;

        $confId = 'sendmails.';
        $configArray = [
                'basetemplate.' => [
                        'wrapTemplate' => '1',
                        'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
                        'textSubpart' => '###CONTENTTEXT_DCMARKER###',
                        'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
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
        $msg = $receiver->getSingleMail($queue, $formatter, $confId, 0);

        $contentHtml = $msg->getHtmlPart();
        $contentText = $msg->getTxtPart();

        $this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html> ich@da.com ich@da.com Hallo Welt', $contentHtml, 'HTML part wrong.');
        $this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".' ich@da.com ich@da.com Hallo Welt', $contentText, 'TEXT part wrong.');
    }

    public function testGetSingleMailUsesGetSingleAdressIfObjectVariableEmailNotSet()
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

    public function testGetSingleMailUsesObjectVariableEmailIfSet()
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
