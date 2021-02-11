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
 * tx_mkmailer_mod1_FuncOverview.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mod1_FuncOverview extends tx_rnbase_mod_BaseModFunc
{
    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_mod_BaseModFunc::getFuncId()
     */
    public function getFuncId()
    {
        return 'overview';
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_mod_BaseModFunc::getContent()
     */
    public function getContent($template, &$configurations, &$formatter, $formTool)
    {
        $data = [];

        $this->handleDeleteMail();
        $this->handleMoveLogEntryBackToQueue();

        $data = array_merge(
            $data,
            $this->getMarkerArrayDataForListView(
                'open',
                'getMailQueueOpen',
                'getTableHtmlForQueueEntriesWithRemoteButton'
            )
        );
        $data = array_merge(
            $data,
            $this->getMarkerArrayDataForListView(
                'finished',
                'getMailQueueFinished',
                'getTableHtmlForQueueEntries'
            )
        );
        $data = array_merge(
            $data,
            $this->getMarkerArrayDataForListView(
                'failed',
                'getLogEntriesForFailedMails',
                'getTableHtmlForLogEntries'
            )
        );

        $markerArray = $formatter->getItemMarkerArrayWrapped($data, $this->getConfId().'data.');

        $out = tx_rnbase_util_Templates::substituteMarkerArrayCached($template, $markerArray);

        return $out;
    }

    /**
     * Liefert den Content für die MKMailer Übersicht.
     *
     * @param string $label
     * @param string $getEntriesMethodOfMailService
     * @param string $showEntriesMethod
     *
     * @return array
     */
    private function getMarkerArrayDataForListView($label, $getEntriesMethodOfMailService, $showEntriesMethod)
    {
        $pager = tx_rnbase::makeInstance('tx_rnbase_util_BEPager', 'openQueuePager', $this->getModule()->getName(), 0);

        $options = ['count' => 1];
        $mailService = tx_mkmailer_util_ServiceRegistry::getMailService();

        $count = $mailService->{$getEntriesMethodOfMailService}($options);
        unset($options['count']);
        $pager->setListSize($count);
        $pager->setOptions($options);
        $content['queue'.$label.'_content'] = $this->{$showEntriesMethod}(
            $mailService->{$getEntriesMethodOfMailService}($options)
        );
        $content['queue'.$label.'_head'] =
            $GLOBALS['LANG']->getLL('label_'.$label.'jobs').' ('.$count.')';

        // Pager einblenden
        $pagerData = $pager->render();
        $content['queue'.$label.'_head'] .= '<div class="pager">'.$pagerData['limits'].' - '.
                                                $pagerData['pages'].'</div>';

        return $content;
    }

    /**
     * Creates Entries for Queue List with RemoveButton.
     *
     * @param array $queueEntries
     *
     * @return string
     */
    protected function getTableHtmlForQueueEntriesWithRemoteButton(array $queueEntries)
    {
        return $this->getTableHtmlForQueueEntries($queueEntries, true);
    }

    /**
     * Creates Entries for Queue List.
     *
     * @param array $queueEntries
     * @param bool $removeButton
     *
     * @return string
     */
    protected function getTableHtmlForQueueEntries(array $queueEntries, $removeButton = false)
    {
        if (!count($queueEntries)) {
            return '';
        }
        $columns = [];
        $columns[] = [
            $GLOBALS['LANG']->getLL('label_uid'),
            $GLOBALS['LANG']->getLL('label_created'),
            $GLOBALS['LANG']->getLL('label_updated'),
            $GLOBALS['LANG']->getLL('label_send'),
            $GLOBALS['LANG']->getLL('label_prefer'),
            $GLOBALS['LANG']->getLL('label_receivers'),
            $GLOBALS['LANG']->getLL('label_subject'),
        ];

        foreach ($queueEntries as $queueEntry) {
            $column = [];

            $removeBtn = '';
            if ($removeButton) {
                $removeBtn = $this->getModule()->getFormTool()->createSubmit(
                    'removeMail[]['.$queueEntry->getUid().']',
                    $GLOBALS['LANG']->getLL('label_delete'),
                    $GLOBALS['LANG']->getLL('label_text_delete')
                );
            }
            $column[] = $queueEntry->getUid().$removeBtn;
            $column[] = $queueEntry->getCreationDate();
            $column[] = $queueEntry->getLastUpdate();
            $column[] = $queueEntry->getMailCount();
            $column[] = $queueEntry->isPrefer() ?
                $GLOBALS['LANG']->getLL('label_yes') :
                $GLOBALS['LANG']->getLL('label_no');
            $column[] = $this->showReceiver($queueEntry);

            $content = $queueEntry->getSubject();

            $column[] = substr($content, 0, 30);
            $columns[] = $column;
        }

        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

        return $tables->buildTable($columns);
    }

    /**
     * @param array $logEntries
     *
     * @return string
     */
    protected function getTableHtmlForLogEntries(array $logEntries)
    {
        if (!count($logEntries)) {
            return '';
        }
        $columns = [];
        $columns[] = [
            $GLOBALS['LANG']->getLL('label_uid'),
            $GLOBALS['LANG']->getLL('label_created'),
            $GLOBALS['LANG']->getLL('label_receiver'),
            '',
        ];
        foreach ($logEntries as $logEntry) {
            $column = [];

            $editButton = $this->getModule()->getFormTool()->createEditButton(
                'tx_mkmailer_receiver',
                $logEntry->getReceiver(),
                ['title' => $GLOBALS['LANG']->getLL('label_edit_complete_receiver')]
            );
            $moveButton = $this->getModule()->getFormTool()->createSubmit(
                'moveLogEntryBackToQueue[]['.$logEntry->getReceiver().']',
                $GLOBALS['LANG']->getLL('label_move'),
                $GLOBALS['LANG']->getLL('label_text_move')
            );

            $column[] = $logEntry->getUid();
            $column[] = $logEntry->getTstamp();
            $column[] = $logEntry->getAddress().' '.$editButton;
            $column[] = $moveButton;
            $columns[] = $column;
        }
        /* @var $tables Tx_Rnbase_Backend_Utility_Tables */
        $tables = tx_rnbase::makeInstance('Tx_Rnbase_Backend_Utility_Tables');

        return $tables->buildTable($columns);
    }

    /**
     * Zeigt die Empfänger der Mail an.
     *
     * @param tx_mkmailer_models_Queue $mail
     *
     * @return string
     */
    protected function showReceiver(tx_mkmailer_models_Queue $mail)
    {
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
        $ret = [];
        $receivers = $mail->getReceivers();
        for ($i = 0, $cnt = count($receivers); $i < $cnt; ++$i) {
            $receiverData = $receivers[$i];
            $receiver = $mailServ->createReceiver($receiverData);

            $addrCnt = $receiver->getAddressCount();
            $addrInfo = $addrCnt.' '.$GLOBALS['LANG']->getLL('label_receivers');
            if (1 == $addrCnt) {
                $addrArr = $receiver->getSingleAddress(0);
                $addrInfo = $addrArr['address'];
            }
            $info = $receiver->getName().' (';
            $info .= $addrInfo.')';

            $ret[] = $info;
        }

        return implode('<br />', $ret);
    }

    /**
     * Löscht die angegebene Email aus der Queue.
     *
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
     * @return void
     */
    private function handleMoveLogEntryBackToQueue()
    {
        $uid = $this->getUidFromRequest('moveLogEntryBackToQueue');

        if (0 != $uid) {
            Tx_Rnbase_Database_Connection::getInstance()->doUpdate(
                'tx_mkmailer_queue',
                'uid='.$uid,
                ['deleted' => '0']
            );
            Tx_Rnbase_Database_Connection::getInstance()->doDelete('tx_mkmailer_log', 'receiver = '.$uid);
        }
    }

    /**
     * Liefert die Mail aus dem Request oder false.
     *
     * @param string $varName
     *
     * @return int
     */
    private function getUidFromRequest($varName)
    {
        $uids = tx_rnbase_parameters::getPostOrGetParameter($varName);
        if (!is_array($uids) || !count($uids)) {
            return false;
        }
        // Es sollte immer nur eine Mail drin liegen
        $mailUid = key($uids[0]);
        if (!$mailUid) {
            return false;
        }

        return (int) $mailUid;
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/mail/class.tx_mkmailer_mod1_BaseModule.php'];
}
