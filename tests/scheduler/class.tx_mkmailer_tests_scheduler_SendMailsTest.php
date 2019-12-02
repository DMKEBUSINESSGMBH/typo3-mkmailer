<?php
/**
 *  Copyright notice
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
tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_rnbase_util_TYPO3');
tx_rnbase::load('tx_rnbase_util_Typo3Classes');

/**
 * Test for tx_mkmailer_scheduler_SendMails
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @author          Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_scheduler_SendMailsTest extends tx_rnbase_tests_BaseTestCase
{
    private $tsfeBackup;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        if (!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
            $this->markTestSkipped('Der Schedulker funktioniert erst ab TYPO3 6.2');
        }
        if (!tx_rnbase_util_Extensions::isLoaded('mklib')) {
            $this->markTestSkipped('mklib muss installiert sein');
        }

        tx_rnbase::load('tx_mkmailer_scheduler_SendMails');

        \DMK\Mklib\Utility\Tests::storeExtConf('mkmailer');

        $this->tsfeBackup = $GLOBALS['TSFE'];
    }

    /**
     * (non-PHPdoc)
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
        self::markTestIncomplete("RuntimeException: The requested database connection named Default has not been configured");

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 0, 'mkmailer');

        $devLog = array();

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            array('callCronpageUrl')
        );

        $this->callInaccessibleMethod(
            array($scheduler, 'executeTask'),
            array(array(), &$devLog)
        );

        $this->assertEquals(
            array(tx_rnbase_util_Logger::LOGLEVEL_FATAL => array(
                'message' =>    'Der Mailversand von mkmailer sollte über den Scheduler ' .
                                'angestoßen werden, die cronpage ist aber nicht konfiguriert' .
                                ' in den Extensioneinstellungen. Bitte beheben.',
            )),
            $devLog,
            'devlog Meldungen falsch'
        );
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenCronpageIsConfiguredAndSiteAvailable()
    {
        self::markTestIncomplete("RuntimeException: The requested database connection named Default has not been configured");

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 123, 'mkmailer');

        $devLog = array();

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            array('callCronpageUrl')
        );

        $scheduler->expects($this->once())
            ->method('callCronpageUrl')
            ->will($this->returnValue(array('error' => 0)));

        $this->callInaccessibleMethod(
            array($scheduler, 'executeTask'),
            array(array(), &$devLog)
        );

        $this->assertEmpty($devLog, 'devlog Meldungen vorhanden');
    }

    /**
     * @group unit
     */
    public function testExecuteTaskWhenCronpageIsConfiguredWithUnaccessiblePage()
    {
        self::markTestIncomplete("RuntimeException: The requested database connection named Default has not been configured");

        \DMK\Mklib\Utility\Tests::setExtConfVar('cronpage', 'http://www.google.com', 'mkmailer');

        $devLog = array();

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            array('callCronpageUrl')
        );

        $scheduler->expects($this->once())
            ->method('callCronpageUrl')
            ->will($this->returnValue(array('error' => 1)));

        $this->callInaccessibleMethod(
            array($scheduler, 'executeTask'),
            array(array(), &$devLog)
        );

        $this->assertEquals(
            'Der Mailversand von mkmailer ist fehlgeschlagen',
            $devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL]['message'],
            'devlog Meldungen falsch'
        );

        $this->assertNotEmpty(
            $devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL]['dataVar'],
            'devlog dataVar falsch'
        );
    }

    /**
     * @group unit
     */
    public function testGetCronpageUrlByPageUid()
    {
        self::markTestIncomplete("RuntimeException: The requested database connection named \"Default\" has not been configured.");

        $GLOBALS['TSFE'] = $this->getMock(
            tx_rnbase_util_Typo3Classes::getTypoScriptFrontendControllerClass(),
            array('getDomainNameForPid'),
            array(),
            '',
            false
        );
        $GLOBALS['TSFE']->expects($this->once())
            ->method('getDomainNameForPid')
            ->with(123)
            ->will($this->returnValue('my.host'));

        $scheduler = $this->getMock(
            'tx_mkmailer_scheduler_SendMails',
            array('getCronPageId')
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
