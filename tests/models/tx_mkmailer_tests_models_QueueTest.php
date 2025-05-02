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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_tests_models_Queue_testcase.
 *
 * @author          Hannes Bochmann <hannes.bochmann@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_tests_models_QueueTest extends BaseTestCase
{
    public function testAttachmentWithStrings(): void
    {
        $queue = GeneralUtility::makeInstance('tx_mkmailer_models_Queue', ['uid' => 123, 'attachments' => '/uploadfolder/myfile.jpg, /uploadfolder/yourfile.jpg']);
        $attachments = $queue->getUploads();

        $this->assertEquals(2, count($attachments), 'Wrong size of attachments');
        $this->assertTrue($attachments[0] instanceof tx_mkmailer_mail_IAttachment, 'Interface not implemented.');
        $this->assertEquals('/uploadfolder/myfile.jpg', $attachments[0]->getPathOrContent(), 'File is wrong.');
    }
}
