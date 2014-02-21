<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_mkmailer_mail_SimpleMessage');

/**
 */
class tx_mkmailer_demo_TestMail {
	static function sendMail($to, $toName) {
		$msg = new tx_mkmailer_mail_SimpleMessage();
		$msg->addTo($to, $toName);
		$html = "
Das ist der <b>HTML-Teil</b> der Email.<br />
Das ist <a href=\"http://www.google.de/\">Google</a>.
		";
		$text = "Das ist eine Textmail.\nZeilenumbrüche gehen auch...";
		$msg->setTxtPart($text);
		$msg->setHtmlPart($html);
		$msg->setSubject('Das ist eine Testmail.');
		$msg->setFrom('egal@system25.de', 'Servermail');
		$srv = tx_mkmailer_util_ServiceRegistry::getMailService();
		$srv->sendEmail($msg);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_demo_TestMail.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_demo_TestMail.php']);
}

?>