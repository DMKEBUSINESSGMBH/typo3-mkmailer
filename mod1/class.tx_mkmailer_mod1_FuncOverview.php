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
tx_rnbase::load('tx_rnbase_parameters');
tx_rnbase::load('tx_rnbase_mod_BaseModFunc');

/**
 * tx_mkmailer_mod1_FuncOverview
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mod1_FuncOverview extends tx_rnbase_mod_BaseModFunc
{

    /**
     * (non-PHPdoc)
     * @see tx_rnbase_mod_BaseModFunc::getFuncId()
     */
    public function getFuncId()
    {
        return 'overview';
    }


    /**
     * (non-PHPdoc)
     * @see tx_rnbase_mod_BaseModFunc::getContent()
     */
    public function getContent($template, &$configurations, &$formatter, $formTool)
    {
        $data = array();

        $this->handleDeleteMail();
        $this->handleMoveInQueue();

        $data = array_merge($data, $this->getOpenMails());
        $data = array_merge($data, $this->getFinishedMails());
        $data = array_merge($data, $this->getFailedMails());

        $markerArray = $formatter->getItemMarkerArrayWrapped($data, $this->getConfId().'data.');

        $out = $configurations->getCObj()->substituteMarkerArrayCached($template, $markerArray, $subpartArray, $wrappedSubpartArray);

        return $out;
    }

    /**
     * Liefert die offenen Aufträge in der Mailqueue
     *
     * @return array
     */
    private function getOpenMails()
    {
        global $LANG;
        $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

        $options = array('count' => 1);
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
    private function getFinishedMails()
    {
        global $LANG;
        $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

        $options = array('count' => 1);
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
     * Liefert die fehlgeschlagenen Aufträge in der Mailqueue
     *
     * @return array
     */
    private function getFailedMails()
    {
      global $LANG;
      $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

      $options = array('count' => 1);
      $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
      $cnt = $mailServ->getMailQueueFailed($options);
      unset($options['count']);
      $pager->setListSize($cnt);
      // Jetzt die Daten abholen
      $pager->setOptions($options);

      // Jetzt die eigentlichen Daten laut Page holen
      $queueArr = $mailServ->getMailQueueFailed($options);

      $content['queuefailed_head'] = $LANG->getLL('label_failedjobs').' ('.$cnt.')';

      // Pager einblenden
      $pagerData = $pager->render();
      $content['queuefailed_head'] .= '<div class="pager">' . $pagerData['limits'] . ' - ' .$pagerData['pages'] .'</div>';

      $content['queuefailed_content'] = $this->showLogs($queueArr, true);

      return $content;
    }

    /**
     * Creates a table of logs
     *
     * @param array $logs
     * @param bool $changeButton
     * @return string
     */
    private function showLogs(&$logs, $changeButton = false)
    {
        global $LANG;
        if (!count($logs)) {
            return '';
        }
        $cols = array();
        $cols[] = array('UID', 'Erstellung', 'Adresse (Receiver)', '');
        $cnt = count($logs);
        for ($i = 0; $i < $cnt; $i++) {
            $log = $logs[$i];
            $col = array();
            if ($changeButton) {
                $changeBtn = $this->getModule()->getFormTool()->createEditButton('tx_mkmailer_receiver', $log->getReceiverUid());
            }

            $moveBtn = $this->getModule()->getFormTool()->createSubmit('moveInQueue[]['.$log->getReceiverUid().']', 'Zurück in Queue verschieben', 'Soll diese Mail wirklich wieder in die Queue verschoben werden?');

            $col[] = $log->getUid();
            $col[] = $log->getTstamp();
            $col[] = $log->getAddress().' ('.$log->getReceiver().') '.$changeBtn;
            $col[] = $moveBtn;
            $cols[] = $col;
        }

        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

        return $tables->buildTable($cols);
    }

    /**
     * Creates a table of email
     *
     * @param array $mails
     * @param bool $removeButton
     * @return string
     */
    private function showMails(&$mails, $removeButton = false)
    {
        global $LANG;
        if (!count($mails)) {
            return '';
        }
        $cols = array();
        $cols[] = array('UID', 'Erstellung', 'Update', 'Verschickt', 'Bevorzugt', $LANG->getLL('label_receivers'), 'Betreff');
        $cnt = count($mails);
        for ($i = 0; $i < $cnt; $i++) {
            $mail = $mails[$i];
            $col = array();
            $rmBtn = '';
            if ($removeButton) {
                $rmBtn = $this->getModule()->getFormTool()->createSubmit('removeMail[]['.$mail->getUid().']', $LANG->getLL('label_delete'), 'Soll diese Mail wirklich gelöscht werden. Die Aktion kann nicht rückgängig gemacht werden!');
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

        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

        return $tables->buildTable($cols);
    }
    /**
     * Zeigt die Empfänger der Mail an
     * @param tx_mkmailer_models_Queue $mail
     */
    public function showReceiver(&$mail)
    {
        global $LANG;
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
        $ret = array();
        $receivers = $mail->getReceivers();
        for ($i = 0, $cnt = count($receivers); $i < $cnt; $i++) {
            $receiverData = $receivers[$i];
            $receiver = $mailServ->createReceiver($receiverData);

            $addrCnt = $receiver->getAddressCount();
            $addrInfo = $addrCnt. ' '.$LANG->getLL('label_receivers');
            if ($addrCnt == 1) {
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
    private function handleDeleteMail()
    {
        $out = '';
        $uid = $this->getUidFromRequest('removeMail');
        if (!$uid) {
            return $out;
        }
        // Die Mail löschen
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
        $mailServ->deleteMail($uid);

        return $out;
    }

    /**
     * aktiviert fehlgeschlagene Mails wieder in der Queue
     * @return string
     */
    private function handleMoveInQueue()
    {
        $out = '';
        $uid = $this->getUidFromRequest('moveInQueue');

        if($uid != 0){
            tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', 'uid='.$uid, array('deleted' => '0'));
            Tx_Rnbase_Database_Connection::getInstance()->doDelete('tx_mkmailer_log', 'receiver = '.$uid);
        }

        return $out;
    }

    /**
     * Liefert die Mail aus dem Request oder false
     *
     * @param string $varName
     * @return int
     */
    private function getUidFromRequest($varName)
    {
        $uids = tx_rnbase_parameters::getPostOrGetParameter($varName);
        if (!is_array($uids) || !count($uids)) {
            return false;
        }
        // Es sollte immer nur eine Mail drin liegen
        list($mailUid, $label) = each($uids[0]);
        if (!$mailUid) {
            return $out;
        }

        return intval($mailUid);
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php']);
}
