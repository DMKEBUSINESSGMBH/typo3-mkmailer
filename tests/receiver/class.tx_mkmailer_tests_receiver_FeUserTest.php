<?php

use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Domain\Model\BaseModel;
use Sys25\RnBase\Frontend\Marker\Templates;
use Sys25\RnBase\Testing\BaseTestCase;
use Sys25\RnBase\Utility\Extensions;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 * Copyright notice
 *
 * (c) 2013-2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Tests zum E-Mail-Versand.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_receiver_FeUserTest extends BaseTestCase
{
    /**
     * Constructs a test case with the given name.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        // TODO: fix me
//        \DMK\Mklib\Utility\Tests::prepareTSFE(array('force' => true));
    }

    protected function setUp(): void
    {
        if (!Extensions::isLoaded('t3users')) {
            $this->markTestSkipped('t3users ist nicht geladen');
        }

        // bei älteren t3 versionen ist der backpath falsch!
        $this->getFileName_backPath = $GLOBALS['TSFE']->tmpl->getFileName_backPath;
        $GLOBALS['TSFE']->tmpl->getFileName_backPath =
            $GLOBALS['TSFE']->tmpl->getFileName_backPath ?
            $GLOBALS['TSFE']->tmpl->getFileName_backPath : Environment::getPublicPath().'/';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        // backpath zurücksetzen
        $GLOBALS['TSFE']->tmpl->getFileName_backPath = $this->getFileName_backPath;

        parent::tearDown();
    }

    /**
     * @return Processor
     */
    private function getConfigurations($confId, array $configArray = [])
    {
        $configArray = [$confId => $configArray];

        $configurations = GeneralUtility::makeInstance(Processor::class);
        $configurations->init(
            $configArray,
            $configurations->getCObj(1),
            'mkmailer',
            'mkmailer' // die mails werden
        );

        return $configurations;
    }

    /**
     * @return tx_mkmailer_receiver_FeUser
     */
    private function getReceiver(
        BaseModel $feuser
    ) {
        $receiver = GeneralUtility::makeInstance(
            'tx_mkmailer_receiver_FeUser'
        );
        $receiver->setFeUser($feuser);

        return $receiver;
    }

    /**
     * @param string $sFileName
     *
     * @return array
     */
    protected function getTemplates($sFileName)
    {
        $data = [];
        $template = @file_get_contents($sFileName);
        $data['contenttext'] = trim(Templates::getSubpart($template, '###CONTENTTEXT###'));
        $data['contenthtml'] = trim(Templates::getSubpart($template, '###CONTENTHTML###'));
        $data['subject'] = trim(Templates::getSubpart($template, '###CONTENTSUBJECT###'));
        $data['resulttext'] = trim(Templates::getSubpart($template, '###RESULTTEXT###'));
        $data['resulthtml'] = trim(Templates::getSubpart($template, '###RESULTHTML###'));
        $data['resultsubject'] = trim(Templates::getSubpart($template, '###RESULTSUBJECT###'));

        return $data;
    }

    /**
     * @return  tx_mkmailer_receiver_BaseTemplate
     */
    private function getQueue(array $data = [])
    {
        // wir könnten die queue auch aus der datenbank holen, das template müsste allerdings dort vorhanden sein!
        $data['uid'] = $data['uid'] ? $data['uid'] : 0;

        $data['contenttext'] = $data['contenttext'] ? $data['contenttext'] : 'contenttext';
        $data['contenthtml'] = $data['contenthtml'] ? $data['contenthtml'] : 'contenthtml';
        $data['subject'] = $data['subject'] ? $data['subject'] : 'subject';

        return GeneralUtility::makeInstance('tx_mkmailer_models_Queue', $data);
    }

    /* *** ********************** *** *
     * *** Die eigentlichen Tests *** *
     * *** ********************** *** */

    public function testGetSingleMailForFeUser()
    {
        $configurations = $this->getConfigurations(
            $confId = 'sendmails.',
            [
                'feuserTemplate' => 'EXT:mkmailer/tests/fixtures/mailfeuser.html',
            ]
        );
        $feuser = GeneralUtility::makeInstance('tx_t3users_models_feuser', [
            'uid' => '1',
            'email' => 'test@localhost.net',
        ]);
        $templates = $this->getTemplates(Extensions::extPath('mkmailer', 'tests/fixtures/mailfeuser.html'));

        $receiver = $this->getReceiver($feuser);
        $queue = $this->getQueue($templates);
        $msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

        $contentHtml = ($msg->getHtmlPart());
        $contentText = ($msg->getTxtPart());
        $subject = ($msg->getSubject());

        $this->assertEquals($templates['resulttext'], $contentText, 'Wrong content text.');
        $this->assertEquals($templates['resulthtml'], $contentHtml, 'Wrong content html.');
        $this->assertEquals($templates['resultsubject'], $subject, 'Wrong content html.');
    }
}
