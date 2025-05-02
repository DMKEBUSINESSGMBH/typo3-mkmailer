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

use Sys25\RnBase\Testing\BaseTestCase;

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
     * @group unit
     *
     * @test
     */
    public function testCreateAttachmentShouldReadTheRightMimeType(): void
    {
        $model = tx_mkmailer_mail_Factory::createAttachment(
            __DIR__.'/../../flexform_main.xml',
        );

        self::assertSame('text/xml', $model->getMimeType());
    }

    /**
     * Test the createEmbeddedAttachment method.
     *
     * @group unit
     *
     * @test
     */
    public function testCreateEmbeddedAttachmentShouldReadTheRightMimeType(): void
    {
        $model = tx_mkmailer_mail_Factory::createEmbeddedAttachment(
            __DIR__.'/../../flexform_main.xml',
            uniqid('Embedded', true)
        );

        self::assertSame('text/xml', $model->getMimeType());
    }

    /**
     * Test the createStringAttachment method.
     *
     * @group unit
     *
     * @test
     */
    public function testCreateStringAttachmentShouldReadTheRightMimeType(): void
    {
        $xml = file_get_contents(
            tx_mkmailer_mail_Factory::makeAbsPath(
                __DIR__.'/../../flexform_main.xml',
            )
        );
        $model = tx_mkmailer_mail_Factory::createStringAttachment(
            $xml
        );

        self::assertSame('text/xml', $model->getMimeType());
    }
}
