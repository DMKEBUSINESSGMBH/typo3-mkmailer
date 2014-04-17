<?php
/**
 *  Copyright notice
 *
 *  (c) 2014 Hannes Bochmann <hannes.bochmann@das-medienkombinat.de>
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
 * benötigte Klassen einbinden
 */
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_mkmailer_receiver_Model');

/**
 *
 * @author Hannes Bochmann <hannes.bochmann@dmk-business.de>
 *
 */
class tx_mkmailer_tests_receiver_Model_testcase extends tx_rnbase_tests_BaseTestCase {

	/**
	 * @group unit
	 */
	public function testConstructSetsEmail() {
		$receiver = $this->getReceiver(array('testMail', 123));

		$property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'email');
		$property->setAccessible(true);

		$this->assertEquals('testMail', $property->getValue($receiver), 'falsche Email');
	}

	/**
	 * @group unit
	 */
	public function testConstructSetsModelUid() {
		$receiver = $this->getReceiver(array('testMail', 123));

		$property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
		$property->setAccessible(true);

		$this->assertEquals('123', $property->getValue($receiver), 'falsche model Uid');
	}

	/**
	 * @group unit
	 */
	public function testSetModelUid() {
		$receiver = $this->getReceiver();

		$receiver->setModelUid(456);

		$property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
		$property->setAccessible(true);

		$this->assertEquals('456', $property->getValue($receiver), 'falsche model Uid');
	}

	/**
	 * @group unit
	 */
	public function testGetModelUid() {
		$receiver = $this->getReceiver();

		$property = new ReflectionProperty('tx_mkmailer_receiver_Model', 'modelUid');
		$property->setAccessible(true);
		$property->setValue($receiver, 456);

		$this->assertEquals('456', $receiver->getModelUid(), 'falsche model Uid');
	}

	/**
	 * @group unit
	 */
	public function testGetValueString() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$this->assertEquals('testMail_123', $receiver->getValueString(), 'falscher value string');
	}

	/**
	 * @group unit
	 */
	public function testSetValueStringSetsCorrectEmail() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$receiver->setValueString('newTestMail_456');
		$this->assertEquals('newTestMail', $receiver->getEmail(), 'falsche Email');
	}

	/**
	 * @group unit
	 */
	public function testSetValueStringSetsCorrectModelUid() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$receiver->setValueString('newTestMail_456');
		$this->assertEquals(456, $receiver->getModelUid(), 'falsche model Uid');
	}

	/**
	 * @group unit
	 */
	public function testAddAdditionalParsesMailTextCorrect() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$mailText = '###MODEL_UID###';
		$formatter = tx_rnbase::makeInstance(
			'tx_rnbase_util_FormatUtil', $this->createConfigurations(array(), 'mkmailer')
		);

		$method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
		$method->setAccessible(true);
		$method->invokeArgs(
			$receiver, array(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx)
		);

		$this->assertEquals(123, $mailText, 'mailText falsch geparsed');
	}

	/**
	 * @group unit
	 */
	public function testAddAdditionalParsesMailHtmlCorrect() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$mailHtml = '###MODEL_UID###';
		$formatter = tx_rnbase::makeInstance(
			'tx_rnbase_util_FormatUtil', $this->createConfigurations(array(), 'mkmailer')
		);

		$method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
		$method->setAccessible(true);
		$method->invokeArgs(
			$receiver, array(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx)
		);

		$this->assertEquals(123, $mailHtml, 'mailHtml falsch geparsed');
	}

	/**
	 * @group unit
	 */
	public function testAddAdditionalParsesMailSubjectCorrect() {
		$receiver = $this->getReceiver(array('testMail', 123));
		$mailSubject = '###MODEL_UID###';
		$formatter = tx_rnbase::makeInstance(
			'tx_rnbase_util_FormatUtil', $this->createConfigurations(array(), 'mkmailer')
		);

		$method = new ReflectionMethod('tx_mkmailer_receiver_Model', 'addAdditionalData');
		$method->setAccessible(true);
		$method->invokeArgs(
			$receiver, array(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx)
		);

		$this->assertEquals(123, $mailSubject, 'mailSubject falsch geparsed');
	}

	/**
	 * @param array $constuctorAgruments
	 * @return tx_mkmailer_receiver_Model
	 */
	private function getReceiver(array $constuctorAgruments = array()) {
		$receiver = $this->getMockForAbstractClass(
			'tx_mkmailer_receiver_Model', $constuctorAgruments, '', TRUE, TRUE, TRUE,
			array('getModel', 'getModelMarker', 'getMarkerClass')
		);

		$receiver->expects($this->any())
			->method('getModel')
			->will($this->returnValue(tx_rnbase::makeInstance(
				'tx_rnbase_model_base', array('uid' => 123)
			)));

		$receiver->expects($this->any())
			->method('getModelMarker')
			->will($this->returnValue('MODEL'));

		$receiver->expects($this->any())
			->method('getMarkerClass')
			->will($this->returnValue('tx_rnbase_util_SimpleMarker'));

		return $receiver;
	}
}