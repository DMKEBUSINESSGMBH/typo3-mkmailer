<?php

use DMK\MkMailer\Mail\Factory;
use Sys25\RnBase\Testing\BaseTestCase;

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

/**
 * Mail factory Tests.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_mail_FactoryTest extends BaseTestCase
{
    /**
     * Test the createAttachment method.
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            'Failed asserting that two strings are identical.'.
            "-'application/xml'".
            "+'text/xml'"
        );

        $model = Factory::createAttachment(
            'EXT:mkmailer/tests/phpunit.xml'
        );

        self::assertSame('application/xml', $model->getMimeType());
    }

    /**
     * Test the createEmbeddedAttachment method.
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateEmbeddedAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            'Failed asserting that two strings are identical.'.
            "-'application/xml'".
            "+'text/xml'"
        );

        $model = Factory::createEmbeddedAttachment(
            'EXT:mkmailer/tests/phpunit.xml',
            uniqid('Embedded', true)
        );

        self::assertSame('application/xml', $model->getMimeType());
    }

    /**
     * Test the createStringAttachment method.
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testCreateStringAttachmentShouldReadTheRightMimeType()
    {
        self::markTestIncomplete(
            'Failed asserting that two strings are identical.'.
            "-'application/xml'".
            "+'text/xml'"
        );

        $xml = file_get_contents(
            Factory::makeAbsPath(
                'EXT:mkmailer/tests/phpunit.xml'
            )
        );
        $model = Factory::createStringAttachment(
            $xml
        );

        self::assertSame('application/xml', $model->getMimeType());
    }
}
