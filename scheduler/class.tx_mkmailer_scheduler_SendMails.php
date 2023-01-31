<?php

use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Utility\Logger;
use Sys25\RnBase\Utility\Misc;
use Sys25\RnBase\Utility\Strings;
use Sys25\RnBase\Utility\T3General;
use Sys25\RnBase\Utility\TYPO3;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 * Copyright notice
 *
 * (c) 2014-2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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
 * Send-Mails scheduler task.
 *
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_scheduler_SendMails extends tx_mklib_scheduler_Generic
{
    /**
     * This is the main method that is called when a task is executed.
     *
     * @param array $options
     * @param array $devLog put some informations for the logging here
     *
     * @return string
     */
    protected function executeTask(array $options, array &$devLog)
    {
        $cronPage = $this->getCronPageId();
        if ($cronPage) {
            Misc::prepareTSFE();
            $report = $this->callCronpageUrl();
            if (0 != ($report['error'] ?? 0)) {
                $devLog[Logger::LOGLEVEL_FATAL] = [
                    'message' => 'Der Mailversand von mkmailer ist fehlgeschlagen',
                    'dataVar' => ['report' => $report],
                ];
            }
        } else {
            $devLog[Logger::LOGLEVEL_FATAL] = [
                'message' => 'Der Mailversand von mkmailer sollte über den Scheduler '.
                    'angestoßen werden, die cronpage ist aber nicht konfiguriert'.
                    ' in den Extensioneinstellungen. Bitte beheben.',
            ];
        }

        return '';
    }

    /**
     * Performs the http request to the cronpage and returns the report.
     *
     * @return array
     */
    protected function callCronpageUrl()
    {
        $report = [];
        T3General::getUrl(
            $this->getCronpageUrl(),
            0,
            false,
            $report
        );

        return $report;
    }

    /**
     * Returns the configured cron page uid.
     *
     * @return string
     */
    protected function getCronPageId()
    {
        $cronPage = $this->getOption('cronpage');

        if (!$cronPage) {
            $cronPage = Processor::getExtensionCfgValue('mkmailer', 'cronpage');
        }

        return $cronPage;
    }

    /**
     * Builds the CronUrl.
     *
     * @return string
     *
     * @throws SiteNotFoundException
     */
    protected function getCronpageUrl()
    {
        $pageUid = $this->getCronPageId();
        $user = $this->getOption('user');
        $pwd = $this->getOption('passwd');
        $auth = ($user && $pwd) ? $user.':'.$pwd.'@' : '';
        $protocol = $this->getProtocol();

        // seems like we have an alias
        if (!Strings::isInteger($pageUid)) {
            $pageUid = TYPO3::getSysPage()->getPageIdFromAlias($pageUid);
        }

        $domain = (new SiteFinder())->getSiteByPageId($pageUid)->getBase()->getHost();

        return sprintf(
            '%1$s://%2$s%3$s/index.php?id=%4$s',
            $protocol,
            $auth,
            $domain,
            $pageUid
        );
    }

    protected function getProtocol()
    {
        return GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https' : 'http';
    }

    /**
     * Extension key, used for devlog.
     *
     * @return  string
     */
    protected function getExtKey()
    {
        return 'mkmailer';
    }
}
