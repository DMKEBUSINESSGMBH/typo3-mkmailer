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

use DMK\Mklib\Utility\Tests;
use Sys25\RnBase\Testing\BaseTestCase;
use Sys25\RnBase\Utility\Logger;
use Sys25\RnBase\Utility\TYPO3Classes;

/**
 * Test for tx_mkmailer_scheduler_SendMails.
 *
 * @author          Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_scheduler_SendMailsTest extends BaseTestCase
{
    private mixed $tsfeBackup;

    protected function setUp(): void
    {
        Tests::storeExtConf('mkmailer');

        $this->tsfeBackup = $GLOBALS['TSFE'] ?? null;
    }

    protected function tearDown(): void
    {
        Tests::storeExtConf('mkmailer');

        $GLOBALS['TSFE'] = $this->tsfeBackup;
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenNoCronpageIsConfigured(): void
    {
        Tests::setExtConfVar('cronpage', 0, 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMockBuilder('tx_mkmailer_scheduler_SendMails')
            ->onlyMethods(['callCronpageUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->callInaccessibleMethod(
            [$scheduler, 'executeTask'],
            [[], &$devLog]
        );

        $this->assertEquals(
            [Logger::LOGLEVEL_FATAL => [
                'message' => 'Der Mailversand von mkmailer sollte über den Scheduler '.
                                'angestoßen werden, die cronpage ist aber nicht konfiguriert'.
                                ' in den Extensioneinstellungen. Bitte beheben.',
            ]],
            $devLog,
            'devlog Meldungen falsch'
        );
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenCronpageIsConfiguredAndSiteAvailable(): void
    {
        self::markTestSkipped('RuntimeException: The requested database connection named Default has not been configured');

        Tests::setExtConfVar('cronpage', 123, 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMockBuilder('tx_mkmailer_scheduler_SendMails')
            ->setMethods(['callCronpageUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $scheduler->expects($this->once())
            ->method('callCronpageUrl')
            ->will($this->returnValue(['error' => 0]));

        $this->callInaccessibleMethod(
            [$scheduler, 'executeTask'],
            [[], &$devLog]
        );

        $this->assertEmpty($devLog, 'devlog Meldungen vorhanden');
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenCronpageIsConfiguredWithUnaccessiblePage(): void
    {
        self::markTestSkipped('RuntimeException: The requested database connection named Default has not been configured');

        Tests::setExtConfVar('cronpage', 'http://www.google.com', 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMockBuilder('tx_mkmailer_scheduler_SendMails')
            ->setMethods(['callCronpageUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $scheduler->expects($this->once())
            ->method('callCronpageUrl')
            ->will($this->returnValue(['error' => 1]));

        $this->callInaccessibleMethod(
            [$scheduler, 'executeTask'],
            [[], &$devLog]
        );

        $this->assertEquals(
            'Der Mailversand von mkmailer ist fehlgeschlagen',
            $devLog[Logger::LOGLEVEL_FATAL]['message'],
            'devlog Meldungen falsch'
        );

        $this->assertNotEmpty(
            $devLog[Logger::LOGLEVEL_FATAL]['dataVar'],
            'devlog dataVar falsch'
        );
    }

    /**
     * @group unit
     */
    public function testGetCronpageUrlByPageUid(): void
    {
        self::markTestSkipped('RuntimeException: The requested database connection named "Default" has not been configured.');

        $GLOBALS['TSFE'] = $this->getMock(
            TYPO3Classes::getTypoScriptFrontendControllerClass(),
            ['getDomainNameForPid'],
            [],
            '',
            false
        );
        $GLOBALS['TSFE']->expects($this->once())
            ->method('getDomainNameForPid')
            ->with(123)
            ->will($this->returnValue('my.host'));

        $scheduler = $this->getMockBuilder('tx_mkmailer_scheduler_SendMails')
            ->setMethods(['callCronpageUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $scheduler->expects($this->once())
            ->method('getCronPageId')
            ->will($this->returnValue(123));

        $scheduler->expects($this->once())
            ->method('getProtocol')
            ->will($this->returnValue('http'));

        $cronpageUrl = $this->callInaccessibleMethod(
            $scheduler,
            'getCronpageUrl'
        );

        $this->assertEquals('http://my.host/index.php?id=123', $cronpageUrl);
    }
}
