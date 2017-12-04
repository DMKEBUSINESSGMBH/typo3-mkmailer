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

        $data = array_merge($data, $this->getListForView('open', 'getMailQueue', 'showQueueEntriesWithRemoveButton'));
        $data = array_merge($data, $this->getListForView('finished', 'getMailQueueFinished', 'showQueueEntries'));
        $data = array_merge($data, $this->getListForView('failed', 'getMailQueueFailed', 'showLogEntries'));

        $markerArray = $formatter->getItemMarkerArrayWrapped($data, $this->getConfId().'data.');

        $out = $configurations->getCObj()->substituteMarkerArrayCached($template, $markerArray, $subpartArray, $wrappedSubpartArray);

        return $out;
    }

    /**
     * Liefert den Content für die MKMailer Übersicht
     *
     * @param string $label
     * @param string $mailQueueMethod
     * @param string $showEntriesMethod
     * @return array
     */
    private function getListForView($label, $mailQueueMethod, $showEntriesMethod)
    {
        global $LANG;
        $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

        $options = array('count' => 1);
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();

        $cnt = $mailServ->$mailQueueMethod($options);
        unset($options['count']);
        $pager->setListSize($cnt);
        $pager->setOptions($options);
        $queueArray = $mailServ->$mailQueueMethod($options);
        $content['queue'.$label.'_content'] = $this->$showEntriesMethod($queueArray);
        $content['queue'.$label.'_head'] = $LANG->getLL('label_'.$label.'jobs').' ('.$cnt.')';

        // Pager einblenden
        $pagerData = $pager->render();
        $content['queue'.$label.'_head'] .= '<div class="pager">' . $pagerData['limits'] . ' - ' .$pagerData['pages'] .'</div>';

        return $content;
    }

    /**
     * Creates Entries for Queue List with RemoveButton
     *
     * @param array $data
     * @return string
     */
    public function showQueueEntriesWithRemoveButton($data){
        return $this->showQueueEntries($data, true);
    }

    /**
     * Creates Entries for Queue List
     *
     * @param array $data
     * @param bool $removeButton
     * @return string
     */
    public function showQueueEntries($data, $removeButton = false){
        global $LANG;
        if (!count($data)) {
            return '';
        }
        $cols = array();
        ($editButton)? $cols[] = array('UID', 'Erstellung', 'Receiver', ''): $cols[] = array('UID', 'Erstellung', 'Update', 'Verschickt', 'Bevorzugt', $LANG->getLL('label_receivers'), 'Betreff');
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            $d = $data[$i];
            $col = array();

            $removeBtn = '';
            if ($removeButton) {
                $removeBtn = $this->getModule()->getFormTool()->createSubmit('removeMail[]['.$d->getUid().']', $LANG->getLL('label_delete'), 'Soll diese Mail wirklich gelöscht werden. Die Aktion kann nicht rückgängig gemacht werden!');
            }
            $col[] = $d->getUid(). $removeBtn;
            $col[] = $d->getCreationDate();
            $col[] = $d->getLastUpdate();
            $col[] = $d->getMailCount();
            $col[] = $d->isPrefer() ? 'Ja' : 'Nein';
            $col[] = $this->showReceiver($d);

            $content = $d->getSubject();

            $col[] = substr($content, 0, 30);
            $cols[] = $col;
        }

        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

        return $tables->buildTable($cols);
    }

    /**
     * Creates Entries for Log List
     *
     * @param array $data
     * @param bool $removeButton
     * @return string
     */
    public function showLogEntries($data){
        global $LANG;
        if (!count($data)) {
            return '';
        }
        $cols = array();
        $cols[] = array('UID', 'Erstellung', 'Receiver', '');
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            $d = $data[$i];
            $col = array();

            $editButton = $this->getModule()->getFormTool()->createEditButton('tx_mkmailer_receiver', $d->getReceiverUid());
            $moveButton= $this->getModule()->getFormTool()->createSubmit('moveInQueue[]['.$d->getReceiverUid().']', 'Zurück in Queue verschieben', 'Soll diese Mail wirklich wieder in die Queue verschoben werden?');

            $col[] = $d->getUid();
            $col[] = $d->getTstamp();
            $col[] = $d->getReceiver().' '.$editButton;
            $col[] = $moveButton;
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
