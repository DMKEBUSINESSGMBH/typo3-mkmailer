<?php

use DMK\Mklib\Utility\Tests;
use Sys25\RnBase\Testing\BaseTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class tx_mkmailer_tests_services_MailTest extends BaseTestCase
{
    protected function setUp(): void
    {
        Tests::disableDevlog();
    }

    /**
     * @group unit
     */
    public function testAddAddress()
    {
        $email = 'alf@localhost.de';
        $name = 'Alf';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);

        $mail->expects($this->once())
            ->method('addAddress')
            ->with($email, $name);

        $this->invoke($mail, $address, 'addAddress');
    }

    /**
     * @group unit
     */
    public function testAddAddressWithInvalidEmail()
    {
        $email = 'alf@@localhost.de';
        $name = 'Alf';
        $exceptionMsg = '[Method: addAddress] Invalid Email address ('.$email.') given. Mail not sent!';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);
        $mail->expects($this->never())
            ->method('addAddress');

        $this->expectException(Exception::class);
        $this->expectErrorMessage($exceptionMsg);

        $this->invoke($mail, $address, 'addAddress');
    }

    /**
     * @group unit
     */
    public function testAddBCC()
    {
        $email = 'alf@localhost.de';
        $name = 'Alf';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);

        $mail->expects($this->once())
            ->method('addBCC')
            ->with($email, $name);

        $this->invoke($mail, $address, 'addBCCAddress');
    }

    /**
     * @group unit
     */
    public function testAddBCCWithInvalidEmail()
    {
        $email = 'alf@@localhost.de';
        $name = 'Alf';
        $exceptionMsg = '[Method: addBCC] Invalid Email address ('.$email.') given. Mail not sent!';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);
        $mail->expects($this->never())
            ->method('addBCC');

        $this->expectException(Exception::class);
        $this->expectErrorMessage($exceptionMsg);

        $this->invoke($mail, $address, 'addBCCAddress');
    }

    /**
     * @group unit
     */
    public function testAddCC()
    {
        $email = 'alf@localhost.de';
        $name = 'Alf';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);

        $mail->expects($this->once())
            ->method('addCC')
            ->with($email, $name);

        $this->invoke($mail, $address, 'addCCAddress');
    }

    /**
     * @group unit
     */
    public function testAddCCWithInvalidEmail()
    {
        $email = 'alf@@localhost.de';
        $name = 'Alf';
        $exceptionMsg = '[Method: addCC] Invalid Email address ('.$email.') given. Mail not sent!';

        $mail = $this->getMail();
        $address = $this->getAddress($email, $name);
        $mail->expects($this->never())
            ->method('addCC');

        $this->expectException(Exception::class);
        $this->expectErrorMessage($exceptionMsg);

        $this->invoke($mail, $address, 'addCCAddress');
    }

    /**
     * @group unit
     */
    public function testGetUploadDir()
    {
        $srv = GeneralUtility::makeInstance('tx_mkmailer_services_Mail');
        $this->assertTrue(is_dir($srv->getUploadDir()), '"'.$srv->getUploadDir().'" is not a Directory!');
        $this->assertTrue(is_writable($srv->getUploadDir()), '"'.$srv->getUploadDir().'" is not writeble');
    }

    private function getMail()
    {
        return $this->getMock(\PHPMailer\PHPMailer\PHPMailer::class, ['addAddress', 'addCC', 'addBCC']);
    }

    private function getAddress($email, $name)
    {
        return tx_mkmailer_mail_Factory::createAddressInstance($email, $name);
    }

    private function invoke($mail, $address, $methodName)
    {
        $method = new ReflectionMethod('tx_mkmailer_services_Mail', $methodName);
        $method->setAccessible(true);
        $method->invokeArgs(
            GeneralUtility::makeInstance('tx_mkmailer_services_Mail'),
            [$mail, $address]
        );
    }
}
