<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2016 DMK E-BUSINESS GmbH <kontakt@dmk-ebusiness.de>
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

tx_rnbase::load('tx_rnbase_tests_BaseTestCase');
tx_rnbase::load('tx_mkmailer_mail_Factory');

/**
 * Mail factory Tests
 *
 * @package TYPO3
 * @subpackage mkmailer
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_mail_FactoryTest extends tx_rnbase_tests_BaseTestCase
{
    /**
     * Test the createAttachment method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            "Failed asserting that two strings are identical.".
            "-'application/xml'".
            "+'text/xml'"
        );

        $model = tx_mkmailer_mail_Factory::createAttachment(
            'EXT:mkmailer/tests/phpunit.xml'
        );

        self::assertSame('application/xml', $model->getMimeType());
    }

    /**
     * Test the createEmbeddedAttachment method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateEmbeddedAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            "Failed asserting that two strings are identical.".
            "-'application/xml'".
            "+'text/xml'"
        );

        $model = tx_mkmailer_mail_Factory::createEmbeddedAttachment(
            'EXT:mkmailer/tests/phpunit.xml',
            uniqid('Embedded', true)
        );

        self::assertSame('application/xml', $model->getMimeType());
    }

    /**
     * Test the createStringAttachment method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateStringAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            "Failed asserting that two strings are identical.".
            "-'application/xml'".
            "+'text/xml'"
        );

        $xml = file_get_contents(
            tx_mkmailer_mail_Factory::makeAbsPath(
                'EXT:mkmailer/tests/phpunit.xml'
            )
        );
        $model = tx_mkmailer_mail_Factory::createStringAttachment(
            $xml
        );

        self::assertSame('application/xml', $model->getMimeType());
    }
}
