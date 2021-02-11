<?php
/**
 *  Copyright notice.
 *
 *  (c) 2014 DMK E-BUSINESS GmbH
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

tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_mkmailer_util_Mails');
tx_rnbase::load('tx_mkmailer_services_Mail');

/**
 * @author Hannes Bochmann
 */
abstract class tx_mkmailer_tests_util_MailsBaseTestCase extends tx_rnbase_tests_BaseTestCase
{
    /**
     * (non-PHPdoc).
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        self::markTestIncomplete('RuntimeException: The requested database connection named "Default" has not been configured.');

        \DMK\Mklib\Utility\Tests::prepareTSFE();
    }

    /**
     * @return tx_mkmailer_services_Mail
     */
    protected function getMailServiceMock()
    {
        $mailService = $this->getMock(
            'tx_mkmailer_services_Mail',
            ['spoolMailJob', 'getTemplate']
        );

        return $mailService;
    }

    /**
     * @param tx_mkmailer_services_Mail $mailService
     *
     * @return tx_mkmailer_util_Mails
     */
    protected function getMailUtilMock(tx_mkmailer_services_Mail $mailService)
    {
        $mailUtil = $this->getMock(
            $this->getMailUtilClass(),
            ['getMailService']
        );

        $mailUtil->expects($this->once())
            ->method('getMailService')
            ->will($this->returnValue($mailService));

        return $mailUtil;
    }

    /**
     * @return string
     */
    abstract protected function getMailUtilClass();
}
