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
class tx_mkmailer_scheduler_SendMails
	extends tx_mklib_scheduler_Generic
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
		$domain = $GLOBALS['TSFE']->getDomainNameForPid($pageUid);
		$user = $this->getOption('user');
		$pwd = $this->getOption('passwd');
		$auth = ($user && $pwd) ? $user . ':' . $pwd . '@' : '';

		return sprintf(
			'http://%1$s%2$s/index.php?id=%3$s',
			$auth,
			$domain,
			$pageUid
		);
	}

	/**
	 * Extension key, used for devlog.
	 *
	 * @return 	string
	 */
	protected function getExtKey()
	{
		return 'mkmailer';
	}
}
