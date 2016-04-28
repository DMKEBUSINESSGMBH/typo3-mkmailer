<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (dev@dmk-ebusiness.de)
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
***************************************************************/


tx_rnbase::load('tx_rnbase_action_BaseIOC');

/**
 *
 * tx_mkmailer_actions_SendMails
 *
 * Asynchroner Versand von Emails. Bei Aufruf dieses
 * Plugins werden anstehende Aufträge in der Mailwarteschlange abgearbeitet.
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_actions_SendMails extends tx_rnbase_action_BaseIOC {

	/**
	 * (non-PHPdoc)
	 * @see tx_rnbase_action_BaseIOC::handleRequest()
	 */
	protected function handleRequest(&$parameters,&$configurations, &$viewdata) {
		$confId = $this->getConfId();
		$mailSrv = tx_mkmailer_util_ServiceRegistry::getMailService();

		return $mailSrv->executeQueue($configurations, $this->getConfId());
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_rnbase_action_BaseIOC::getTemplateName()
	 */
	protected function getTemplateName() {
		return 'sendmails';
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_rnbase_action_BaseIOC::getViewClassName()
	 */
	protected function getViewClassName() {
		return '';
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/actions/class.tx_mkmailer_actions_SendMails.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/actions/class.tx_mkmailer_actions_SendMails.php']);
}