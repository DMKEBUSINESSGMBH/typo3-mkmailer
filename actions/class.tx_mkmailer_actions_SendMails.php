<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (nitzsche@das-medienkombinat.de)
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_action_BaseIOC');

/**
 * Asynchroner Versand von Emails. Bei Aufruf dieses 
 * Plugins werden anstehende Aufträge in der Mailwarteschlange abgearbeitet.
 */
class tx_mkmailer_actions_SendMails extends tx_rnbase_action_BaseIOC {
  function getTemplateName() { return 'sendmails';}
	function getViewClassName() { return '';}

	/**
	 * 
	 *
	 * @param array_object $parameters
	 * @param tx_rnbase_configurations $configurations
	 * @return string error msg or null
	 */
	function handleRequest(&$parameters,&$configurations, &$viewdata) {
		$confId = $this->getConfId();

//		$ret = 'Mailer Queue';
//		$msg = 'Das ist eine Email
//		Und das ist der Inhalt.';
//		tx_rnbase::load('tx_t3users_models_feuser');
//		$feuser = tx_t3users_models_feuser::getInstance(1);
//		$this->sendFEUserMail($feuser, $msg);

		$mailSrv = tx_mkmailer_util_ServiceRegistry::getMailService();
		$ret .= $mailSrv->executeQueue($configurations, $this->getConfId());
		return $ret;
	}


	function sendFEUserMail($feuser, $message) {
		$mailSrv = tx_mkmailer_util_ServiceRegistry::getMailService();

//		$templateObj = $this->getTemplate($mailTemplate);
//		$message = $templateObj->getTemplateComplete();
//		$from = $templateObj->getFrom();
		$from = 'test@egal.de';

		tx_rnbase::load('tx_mkmailer_util_Misc');
		tx_rnbase::load('tx_mkmailer_mail_MailJob');
		$job = new tx_mkmailer_mail_MailJob();

		// Den Empfänger der Mail als Receiver anlegen
		tx_rnbase::load('tx_mkmailer_receiver_FeUser');
		$receiver = new tx_mkmailer_receiver_FeUser();
		$receiver->setFeUser($feuser);
		$job->addReceiver($receiver);
		$job->setCCs(tx_mkmailer_util_Misc::parseAddressString($ccs));
		$job->setContentText($message);

//		$options['bcc'] = $templateObj->getBcc();
//		$options['fromname'] = $templateObj->getFromName();
		$formatter = null;
		// Und nun geht alles in den Versand
		$mailSrv->spoolMailJob($job);

		//$mailSrv->spoolMailJob($message, $receivers, $formatter, $from, $options);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/actions/class.tx_mkmailer_actions_SendMails.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/actions/class.tx_mkmailer_actions_SendMails.php']);
}
?>