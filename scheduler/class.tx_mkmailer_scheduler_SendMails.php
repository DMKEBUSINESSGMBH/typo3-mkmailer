<?php
/**
 *  @author Hannes Bochmann
 *
 *  Copyright notice
 *
 *  (c) 2014 Hannes Bochmann <hannes.bochmann@dmk-ebusiness.de>
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
tx_rnbase::load('tx_mklib_scheduler_Generic');

/**
 * tx_mkmailer_scheduler_SendMails
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @author 			Hannes Bochmann <dev@dmk-ebusiness.de>
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_scheduler_SendMails extends tx_mklib_scheduler_Generic {

	/**
	 * (non-PHPdoc)
	 * @see tx_mklib_scheduler_Generic::executeTask()
	 */
	protected function executeTask(array $options, array &$devLog) {
		if ($cronPage = tx_rnbase_configurations::getExtensionCfgValue('mkmailer', 'cronpage')) {
			$report = array();
			t3lib_div::getUrl(
				t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'index.php?id=' . $cronPage,
				0, FALSE, $report
			);
			if ($report['error'] != 0) {
				$devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL] = array(
					'message' => 'Der Mailversand von mkmailer ist fehlgeschlagen',
					'dataVar' => array('report' => $report)
				);
			}
		} else {
			$devLog[tx_rnbase_util_Logger::LOGLEVEL_FATAL] = array(
				'message' => 	'Der Mailversand von mkmailer sollte über den Scheduler ' .
								'angestoßen werden, die cronpage ist aber nicht konfiguriert' .
								' in den Extensioneinstellungen. Bitte beheben.',
			);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_mklib_scheduler_Generic::getExtKey()
	 */
	protected function getExtKey() {
		return 'mkmailer';
	}
}