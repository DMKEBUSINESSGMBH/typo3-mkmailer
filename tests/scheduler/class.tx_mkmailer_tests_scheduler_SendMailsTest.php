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

/**
 * Test for tx_mkmailer_scheduler_SendMails.
 *
 * @author          Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_scheduler_SendMailsTest extends \Sys25\RnBase\Testing\BaseTestCase
{
    private $tsfeBackup;

    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        if (!\Sys25\RnBase\Utility\TYPO3::isTYPO62OrHigher()) {
            $this->markTestSkipped('Der Schedulker funktioniert erst ab TYPO3 6.2');
        }
        if (!\Sys25\RnBase\Utility\Extensions::isLoaded('mklib')) {
            $this->markTestSkipped('mklib muss installiert sein');
        }

        \DMK\Mklib\Utility\Tests::storeExtConf('mkmailer');

        $this->tsfeBackup = $GLOBALS['TSFE'];
    }

    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        \DMK\Mklib\Utility\Tests::storeExtConf('mkmailer');

        $GLOBALS['TSFE'] = $this->tsfeBackup;
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenNoCronpageIsConfigured()
    {
        self::markTestIncomplete('RuntimeException: The requested database connection named Default has not been configured');

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 0, 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            ['callCronpageUrl']
        );

        $this->callInaccessibleMethod(
            [$scheduler, 'executeTask'],
            [[], &$devLog]
        );

        $this->assertEquals(
            [\Sys25\RnBase\Utility\Logger::LOGLEVEL_FATAL => [
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
    public function testExecuteTaskWhenCronpageIsConfiguredAndSiteAvailable()
    {
        self::markTestIncomplete('RuntimeException: The requested database connection named Default has not been configured');

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 123, 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            ['callCronpageUrl']
        );

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
    public function testExecuteTaskWhenCronpageIsConfiguredWithUnaccessiblePage()
    {
        self::markTestIncomplete('RuntimeException: The requested database connection named Default has not been configured');

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 'http://www.google.com', 'mkmailer');

        $devLog = [];

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            ['callCronpageUrl']
        );

        $scheduler->expects($this->once())
            ->method('callCronpageUrl')
            ->will($this->returnValue(['error' => 1]));

        $this->callInaccessibleMethod(
            [$scheduler, 'executeTask'],
            [[], &$devLog]
        );

        $this->assertEquals(
            'Der Mailversand von mkmailer ist fehlgeschlagen',
            $devLog[\Sys25\RnBase\Utility\Logger::LOGLEVEL_FATAL]['message'],
            'devlog Meldungen falsch'
        );

        $this->assertNotEmpty(
            $devLog[\Sys25\RnBase\Utility\Logger::LOGLEVEL_FATAL]['dataVar'],
            'devlog dataVar falsch'
        );
    }

    /**
     * @group unit
     */
    public function testGetCronpageUrlByPageUid()
    {
        self::markTestIncomplete('RuntimeException: The requested database connection named "Default" has not been configured.');

        $GLOBALS['TSFE'] = $this->getMock(
            \Sys25\RnBase\Utility\TYPO3Classes::getTypoScriptFrontendControllerClass(),
            ['getDomainNameForPid'],
            [],
            '',
            false
        );
        $GLOBALS['TSFE']->expects($this->once())
            ->method('getDomainNameForPid')
            ->with(123)
            ->will($this->returnValue('my.host'));

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            ['getCronPageId']
        );

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
