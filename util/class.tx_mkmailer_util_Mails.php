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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_util_Mails.
 *
 * @author          Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_util_Mails
{
    /**
     * @param int                                $modelUid
     * @param string|tx_mkmailer_models_Template $templateObjectOrKey
     */
    public function sendModelReceiverMail(string $receiverClass, $modelUid, $email, $templateObjectOrKey): void
    {
        $mailSrv = $this->getMailService();

        /* @var $templateObj tx_mkmailer_models_Template */
        $templateObj = $templateObjectOrKey instanceof tx_mkmailer_models_Template
            ? $templateObjectOrKey
            : $mailSrv->getTemplate($templateObjectOrKey);

        $receiver = GeneralUtility::makeInstance($receiverClass, $email, $modelUid);

        $job = GeneralUtility::makeInstance('tx_mkmailer_mail_MailJob');
        $job->addReceiver($receiver);
        $job->setFrom($templateObj->getFromAddress());
        $job->setCCs($templateObj->getCcAddress());
        $job->setBCCs($templateObj->getBccAddress());
        $job->setSubject($templateObj->getSubject());
        $job->setContentText($templateObj->getContentText());
        $job->setContentHtml($templateObj->getContentHtml());

        $mailSrv->spoolMailJob($job);
    }

    protected function getMailService(): tx_mkmailer_services_Mail
    {
        return tx_mkmailer_util_ServiceRegistry::getMailService();
    }
}
