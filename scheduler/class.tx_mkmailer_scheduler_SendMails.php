<?php
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
tx_rnbase::load('Tx_Rnbase_Utility_T3General');
tx_rnbase::load('tx_mklib_scheduler_Generic');
tx_rnbase::load('tx_rnbase_util_Misc');

/**
 * Send-Mails scheduler task
 *
 * @package TYPO3
 * @subpackage mkmailer
 * @author Hannes Bochmann
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_scheduler_SendMails extends tx_mklib_scheduler_Generic
{

    /**
     * This is the main method that is called when a task is executed
     *
     * @param array $options
     * @param array $devLog Put some informations for the logging here.
     *
     * @return string
     */
    protected function executeTask(array $options, array &$devLog)
    {
        $cronPage = $this->getCronPageId();
        if ($cronPage) {
            tx_rnbase_util_Misc::prepareTSFE();
            $report = $this->callCronpageUrl();
            if ($report['error'] != 0) {
                $devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL] = array(
                    'message' => 'Der Mailversand von mkmailer ist fehlgeschlagen',
                    'dataVar' => array('report' => $report)
                );
            }
        } else {
            $devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL] = array(
                'message' => 'Der Mailversand von mkmailer sollte über den Scheduler ' .
                    'angestoßen werden, die cronpage ist aber nicht konfiguriert' .
                    ' in den Extensioneinstellungen. Bitte beheben.',
            );
        }

        return '';
    }

    /**
     * Performs the http request to the cronpage and returns the report
     *
     * @return array
     */
    protected function callCronpageUrl()
    {
        $report = array();
        Tx_Rnbase_Utility_T3General::getUrl(
            $this->getCronpageUrl(),
            0,
            false,
            $report
        );

        return $report;
    }

    /**
     * Returns the configured cron page uid
     *
     * @return string
     */
    protected function getCronPageId()
    {
        $cronPage = $this->getOption('cronpage');

        if (!$cronPage) {
            $cronPage = tx_rnbase_configurations::getExtensionCfgValue('mkmailer', 'cronpage');
        }

        return $cronPage;
    }

    /**
     * Builds the CronUrl.
     *
     * @return string
     */
    protected function getCronpageUrl()
    {
        $pageUid = $this->getCronPageId();
        $user = $this->getOption('user');
        $pwd = $this->getOption('passwd');
        $auth = ($user && $pwd) ? $user . ':' . $pwd . '@' : '';
        $protocol = $this->getProtocol();

        if (tx_rnbase_util_TYPO3::isTYPO90OrHigher()) {
            /** @var \TYPO3\CMS\Core\Http\ServerRequest $request */
            $request = $GLOBALS['TYPO3_REQUEST'];
            $domain = $request->getServerParams()['SERVER_NAME'];
        } else {
            // seems like we have an alias
            if ( !Tx_Rnbase_Utility_Strings::isInteger($pageUid)) {
                $pageUid = tx_rnbase_util_TYPO3::getSysPage()->getPageIdFromAlias($pageUid);
            }
            $domain = $GLOBALS['TSFE']->getDomainNameForPid($pageUid);
        }

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
        return \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL')?'https':'http';
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
