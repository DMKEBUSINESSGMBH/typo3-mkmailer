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

tx_rnbase::load('tx_rnbase_mod_BaseModFunc');
/**
 */
class tx_mkmailer_mod1_FuncOverview extends tx_rnbase_mod_BaseModFunc {

	function getFuncId() {
		return 'overview';
	}

	function getContent($template, &$configurations, &$formatter, $formTool) {
		$data = array();

		$this->handleDeleteMail();

		$data = array_merge($data, $this->getOpenMails());
		$data = array_merge($data, $this->getFinishedMails());

		$markerArray = $formatter->getItemMarkerArrayWrapped($data,$this->getConfId().'data.');

		$out = $configurations->getCObj()->substituteMarkerArrayCached($template, $markerArray, $subpartArray, $wrappedSubpartArray);

		return $out;
	}


	/**
	 * Liefert die offenen Aufträge in der Mailqueue
	 *
	 * @return array
	 */
	private function getOpenMails() {
		global $LANG;
		$pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

		$options = array('count'=>1);
		$mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
		$cnt = $mailServ->getMailQueue($options);
		unset($options['count']);
		$pager->setListSize($cnt);
		// Jetzt die Daten abholen
		$pager->setOptions($options);

		// Jetzt die eigentlichen Daten laut Page holen
		$queueArr = $mailServ->getMailQueue($options);

		$content['queueopen_head'] = $LANG->getLL('label_openjobs').' ('.$cnt.')';

		// Pager einblenden
		$pagerData = $pager->render();
		$content['queueopen_head'] .= '<div class="pager">' . $pagerData['limits'] . ' - ' .$pagerData['pages'] .'</div>';

		$content['queueopen_content'] = $this->showMails($queueArr, true);
		return $content;
	}

	/**
	 * Liefert die offenen Aufträge in der Mailqueue
	 *
	 * @return array
	 */
	private function getFinishedMails() {
		global $LANG;
		$pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

		$options = array('count'=>1);
		$mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
		$cnt = $mailServ->getMailQueueFinished($options);
		unset($options['count']);
		$pager->setListSize($cnt);
		// Jetzt die Daten abholen
		$pager->setOptions($options);

		// Jetzt die eigentlichen Daten laut Page holen
		$queueArr = $mailServ->getMailQueueFinished($options);

		$content['queuefinished_head'] = $LANG->getLL('label_finishedjobs').' ('.$cnt.')';

		// Pager einblenden
		$pagerData = $pager->render();
		$content['queuefinished_head'] .= '<div class="pager">' . $pagerData['limits'] . ' - ' .$pagerData['pages'] .'</div>';

		$content['queuefinished_content'] = $this->showMails($queueArr);
		return $content;
	}

	/**
	 * Creates a table of email
	 *
	 * @param array $mails
	 * @param boolean $removeButton
	 * @return string
	 */
	private function showMails(&$mails, $removeButton=false) {
		global $LANG;
		if(!count($mails)) return '';
		$cols = array();
		$cols[] = array('UID', 'Erstellung', 'Update', 'Verschickt', 'Bevorzugt', $LANG->getLL('label_receivers'), 'Betreff');
		$cnt = count($mails);
		for($i=0 ; $i < $cnt; $i++) {
			$mail = $mails[$i];
			$col = array();
			$rmBtn = '';
			if($removeButton) {
				$rmBtn = $this->getModule()->getFormTool()->createSubmit('removeMail[]['.$mail->getUid().']', $LANG->getLL('label_delete'),'Soll diese Mail wirklich gelöscht werden. Die Aktion kann nicht rückgängig gemacht werden!');
			}
			$col[] = $mail->getUid(). $rmBtn;
			$col[] = $mail->getCreationDate();
			$col[] = $mail->getLastUpdate();
			$col[] = $mail->getMailCount();
			$col[] = $mail->isPrefer() ? 'Ja' : 'Nein';
			$col[] = $this->showReceiver($mail);
			$content = $mail->getSubject();
			$col[] = substr($content, 0, 30);
			$cols[] = $col;
		}
		return $this->getModule()->getDoc()->table($cols, $this->getModule()->getTableLayout());
	}
	/**
	 * Zeigt die Empfänger der Mail an
	 * @param tx_mkmailer_models_Queue $mail
	 */
	function showReceiver(&$mail) {
		global $LANG;
		$mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
		$ret = array();
		$receivers = $mail->getReceivers();
		for($i=0, $cnt = count($receivers); $i < $cnt; $i++) {
			$receiverData = $receivers[$i];
			$receiver = $mailServ->createReceiver($receiverData);

			$addrCnt = $receiver->getAddressCount();
			$addrInfo = $addrCnt. ' '.$LANG->getLL('label_receivers');
			if($addrCnt == 1) {
				$addrArr = $receiver->getSingleAddress(0);
				$addrInfo = $addrArr['address'];
			}
			$info = $receiver->getName() . ' (';
			$info .= $addrInfo .')';

			$ret[] = $info;
		}
		return implode('<br />', $ret);
	}

	/**
	 * Löscht die angegebene Email aus der Queue
	 * @return string
	 */
	private function handleDeleteMail() {
		$out = '';
		$uid = $this->getUidFromRequest('removeMail');
		if(!$uid) return $out;
		// Die Mail löschen
		$mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
		$mailServ->deleteMail($uid);
		return $out;
	}
	/**
	 * Liefert die Mail aus dem Request oder false
	 *
	 * @param string $varName
	 * @return int
	 */
	private function getUidFromRequest($varName) {
		$uids = t3lib_div::_GP($varName);
		if(!is_array($uids) || !count($uids)) return false;
		// Es sollte immer nur eine Mail drin liegen
		list($mailUid,$label) = each($uids[0]);
		if(!$mailUid) return $out;
		return intval($mailUid);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php']);
}

?>