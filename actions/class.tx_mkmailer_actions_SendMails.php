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

use Sys25\RnBase\Frontend\Controller\AbstractAction;
use Sys25\RnBase\Frontend\Request\RequestInterface;

/**
 * tx_mkmailer_actions_SendMails.
 *
 * Asynchroner Versand von Emails. Bei Aufruf dieses
 * Plugins werden anstehende Aufträge in der Mailwarteschlange abgearbeitet.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_actions_SendMails extends AbstractAction
{
    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Frontend\Controller\AbstractAction::handleRequest()
     */
    protected function handleRequest(RequestInterface $request): string
    {
        $mailSrv = tx_mkmailer_util_ServiceRegistry::getMailService();

        return $mailSrv->executeQueue($request->getConfigurations(), $this->getConfId());
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Frontend\Controller\AbstractAction::getTemplateName()
     */
    protected function getTemplateName(): string
    {
        return 'sendmails';
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Frontend\Controller\AbstractAction::getViewClassName()
     */
    protected function getViewClassName(): string
    {
        return '';
    }
}
