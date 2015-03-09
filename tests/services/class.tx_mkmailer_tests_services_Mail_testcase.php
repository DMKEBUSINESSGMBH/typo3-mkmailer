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

/**
 * benötigte Klassen einbinden
 */
require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_mkmailer_receiver_Model');
tx_rnbase::load('tx_mklib_tests_Util');

/**
 *
 * @author Hannes Bochmann <hannes.bochmann@dmk-business.de>
 *
 */
class tx_mkmailer_tests_services_Mail_testcase extends tx_rnbase_tests_BaseTestCase {

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp() {
		tx_mklib_tests_Util::disableDevlog();
	}

	/**
	 * @group unit
	 */
	public function testAddAddress() {
		$email = 'alf@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);

		$mail->expects($this->once())
			->method('AddAddress')
			->with($email, $name);

		 $this->invoke($mail, $address, 'addAddress');
	}

	/**
	 * @group unit
	 */
	public function testAddAddressWithInvalidEmail() {
		$email = 'alf@@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);
		$mail->expects($this->never())
			->method('AddAddress');

		$this->invoke($mail, $address, 'addAddress');
	}

	/**
	 * @group unit
	 */
	public function testAddBCC() {
		$email = 'alf@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);

		$mail->expects($this->once())
			->method('AddBCC')
			->with($email, $name);

		$this->invoke($mail, $address, 'addBCCAddress');
	}

	/**
	 * @group unit
	 */
	public function testAddBCCWithInvalidEmail() {
		$email = 'alf@@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);
		$mail->expects($this->never())
			->method('AddBCC');

		 $this->invoke($mail, $address, 'addBCCAddress');
	}
	/**
	 * @group unit
	 */
	public function testAddCC() {
		$email = 'alf@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);

		$mail->expects($this->once())
			->method('AddCC')
			->with($email, $name);

		$this->invoke($mail, $address, 'addCCAddress');
	}

	/**
	 * @group unit
	 */
	public function testAddCCWithInvalidEmail() {
		$email = 'alf@@localhost.de';
		$name = 'Alf';

		$mail = $this->getMail();
		$address = $this->getAddress($email, $name);
		$mail->expects($this->never())
			->method('AddCC');

		 $this->invoke($mail, $address, 'addCCAddress');
	}

	/**
	 * @group unit
	 */
	public function testgetUploadDir() {
		$srv = tx_rnbase::makeInstance('tx_mkmailer_services_Mail');
		$this->assertTrue(is_dir($srv->getUploadDir()),'"' . $srv->getUploadDir() .'" is not a Directory!');
		$this->assertTrue(is_writable($srv->getUploadDir()), '"' . $srv->getUploadDir() .'" is not writeble');
	}

	private function getMail() {
		return $this->getMock('PHPMailer', array('AddAddress', 'AddCC', 'AddBCC'));
	}

	private function getAddress($email, $name) {
		tx_rnbase::load('tx_mkmailer_mail_Factory');
		return tx_mkmailer_mail_Factory::createAddressInstance($email, $name);
	}

	private function invoke($mail, $address, $methodName) {
		$method = new ReflectionMethod('tx_mkmailer_services_Mail', $methodName);
		$method->setAccessible(true);
		$method->invokeArgs(
			tx_rnbase::makeInstance('tx_mkmailer_services_Mail'),
		 	array($mail, $address)
		);
	}

}