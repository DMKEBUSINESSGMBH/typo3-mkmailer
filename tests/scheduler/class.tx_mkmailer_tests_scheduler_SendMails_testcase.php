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
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_rnbase_util_TYPO3');

/**
 *
 * tx_mkmailer_tests_scheduler_SendMails_testcase
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @author 			Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_scheduler_SendMails_testcase extends tx_rnbase_tests_BaseTestCase {

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		if (!tx_rnbase_util_TYPO3::isTYPO62OrHigher()) {
			$this->markTestSkipped('Der Schedulker funktioniert erst ab TYPO3 6.2');
		}
		if (!t3lib_extMgm::isLoaded('mklib')) {
			$this->markTestSkipped('mklib muss installiert sein');
		}

		tx_rnbase::load('tx_mklib_tests_Util');
		tx_rnbase::load('tx_mkmailer_scheduler_SendMails');

		tx_mklib_tests_Util::storeExtConf('mkmailer');
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown() {
		tx_mklib_tests_Util::restoreExtConf('mkmailer');
	}

	/**
	 * @group unit
	 */
	public function testExecuteTaskWhenNoCronpageIsConfigured() {
		tx_mklib_tests_Util::setExtConfVar('cronpage', 0, 'mkmailer');

		$devLog = $this->callExecuteTask();

		$this->assertEquals(
			array(tx_rnbase_util_Logger::LOGLEVEL_FATAL => array(
				'message' => 	'Der Mailversand von mkmailer sollte über den Scheduler ' .
								'angestoßen werden, die cronpage ist aber nicht konfiguriert' .
								' in den Extensioneinstellungen. Bitte beheben.',
			))
			, $devLog, 'devlog Meldungen falsch'
		);
	}

	/**
	 * @group unit
	 */
	public function testExecuteTaskWhenCronpageIsConfigured() {
		tx_mklib_tests_Util::setExtConfVar('cronpage', 123, 'mkmailer');

		$devLog = $this->callExecuteTask();

		$this->assertEmpty($devLog, 'devlog Meldungen vorhanden');
	}

	/**
	 * @group unit
	 */
	public function testExecuteTaskWhenCronpageIsConfiguredWithUnaccessiblePage() {
		tx_mklib_tests_Util::setExtConfVar('cronpage', 'http://www.google.com', 'mkmailer');

		$devLog = $this->callExecuteTask();

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
	public function testGetCronpageUrlByPageUid() {
		$GLOBALS['TSFE'] = $this->getMock(
			'tslib_fe', array('getDomainNameForPid'), array(), '', FALSE
		);
		$GLOBALS['TSFE']->expects($this->once())
			->method('getDomainNameForPid')
			->with(123)
			->will($this->returnValue('my.host'));

		$cronpageUrl = $this->callInaccessibleMethod(
			tx_rnbase::makeInstance('tx_mkmailer_scheduler_SendMails'),
			'getCronpageUrlByPageUid',
			123
		);

		$this->assertEquals('http://my.host/index.php?id=123', $cronpageUrl);
	}

	/**
	 * @group unit
	 */
	public function testExecuteTaskCallsGetCronpageUrlByPageUidCorrect() {
		tx_mklib_tests_Util::setExtConfVar('cronpage', 123, 'mkmailer');

		$schedulerTask = $this->getMock(
			'tx_mkmailer_scheduler_SendMails', array('getCronpageUrlByPageUid')
		);
		$schedulerTask->expects($this->once())
			->method('getCronpageUrlByPageUid')
			->with(123)
			->will($this->returnValue('http://my.host'));

		$devLog = array();
		$method = new ReflectionMethod(
			'tx_mkmailer_scheduler_SendMails', 'executeTask'
		);
		$method->setAccessible(TRUE);
		$method->invokeArgs($schedulerTask, array(array(), &$devLog));
	}

	/**
	 * @return multitype:
	 */
	private function callExecuteTask() {
		$devLog = array();
		$method = new ReflectionMethod(
			'tx_mkmailer_scheduler_SendMails', 'executeTask'
		);
		$method->setAccessible(TRUE);
		$method->invokeArgs(
			tx_rnbase::makeInstance('tx_mkmailer_scheduler_SendMails'),
			array(array(), &$devLog)
		);

		return$devLog;
	}
}