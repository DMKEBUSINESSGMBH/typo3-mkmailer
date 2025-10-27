<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkmailer" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use Sys25\RnBase\Backend\Module\BaseModFunc;
use Sys25\RnBase\Backend\Utility\BEPager;
use Sys25\RnBase\Backend\Utility\Tables;
use Sys25\RnBase\Database\Connection;
use Sys25\RnBase\Frontend\Marker\Templates;
use Sys25\RnBase\Frontend\Request\Parameters;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * tx_mkmailer_mod1_FuncOverview.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mod1_FuncOverview extends BaseModFunc
{
    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Backend\Module\BaseModFunc::getFuncId()
     */
    protected function getFuncId(): string
    {
        return 'overview';
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Backend\Module\BaseModFunc::getContent()
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function getContent($template, &$configurations, &$formatter, $formTool)
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

        return Templates::substituteMarkerArrayCached($template, $markerArray);
    }

    /**
     * Liefert den Content für die MKMailer Übersicht.
     *
     * @return array
     */
    private function getMarkerArrayDataForListView(string $label, string $getEntriesMethodOfMailService, string $showEntriesMethod)
    {
        $pager = GeneralUtility::makeInstance(BEPager::class, 'openQueuePager', $this->getModule(), 0);

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
            $this->getModule()->getLanguageService()->getLL('label_'.$label.'jobs').' ('.$count.')';

        // Pager einblenden
        $pagerData = $pager->render();
        $content['queue'.$label.'_head'] .= '<div class="pager">'.$pagerData['limits'].' - '.
                                                $pagerData['pages'].'</div>';

        return $content;
    }

    /**
     * Creates Entries for Queue List with RemoveButton.
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
     * @param bool $removeButton
     *
     * @return string
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    protected function getTableHtmlForQueueEntries(array $queueEntries, $removeButton = false)
    {
        if ([] === $queueEntries) {
            return '';
        }

        $columns = [];
        $columns[] = [
            $this->getModule()->getLanguageService()->getLL('label_uid'),
            $this->getModule()->getLanguageService()->getLL('label_created'),
            $this->getModule()->getLanguageService()->getLL('label_updated'),
            $this->getModule()->getLanguageService()->getLL('label_send'),
            $this->getModule()->getLanguageService()->getLL('label_prefer'),
            $this->getModule()->getLanguageService()->getLL('label_receivers'),
            $this->getModule()->getLanguageService()->getLL('label_subject'),
        ];

        foreach ($queueEntries as $queueEntry) {
            $column = [];

            $removeBtn = '';
            if ($removeButton) {
                $removeBtn = $this->getModule()->getFormTool()->createSubmit(
                    'removeMail[]['.$queueEntry->getUid().']',
                    $this->getModule()->getLanguageService()->getLL('label_delete'),
                    $this->getModule()->getLanguageService()->getLL('label_text_delete')
                );
            }

            $column[] = $queueEntry->getUid().$removeBtn;
            $column[] = $queueEntry->getCreationDate();
            $column[] = $queueEntry->getLastUpdate();
            $column[] = $queueEntry->getMailCount();
            $column[] = $queueEntry->isPrefer() ?
                $this->getModule()->getLanguageService()->getLL('label_yes') :
                $this->getModule()->getLanguageService()->getLL('label_no');
            $column[] = $this->showReceiver($queueEntry);

            $content = $queueEntry->getSubject();

            $column[] = substr((string) $content, 0, 30);
            $columns[] = $column;
        }

        /* @var $tables \Sys25\RnBase\Backend\Utility\Tables */
        $tables = GeneralUtility::makeInstance(Tables::class);

        return $tables->buildTable($columns);
    }

    /**
     * @return string
     */
    protected function getTableHtmlForLogEntries(array $logEntries)
    {
        if ([] === $logEntries) {
            return '';
        }

        $columns = [];
        $columns[] = [
            $this->getModule()->getLanguageService()->getLL('label_uid'),
            $this->getModule()->getLanguageService()->getLL('label_created'),
            $this->getModule()->getLanguageService()->getLL('label_receiver'),
            '',
        ];
        foreach ($logEntries as $logEntry) {
            $column = [];

            $editButton = $this->getModule()->getFormTool()->createEditButton(
                'tx_mkmailer_receiver',
                $logEntry->getReceiver(),
                ['title' => $this->getModule()->getLanguageService()->getLL('label_edit_complete_receiver')]
            );
            $moveButton = $this->getModule()->getFormTool()->createSubmit(
                'moveLogEntryBackToQueue[]['.$logEntry->getReceiver().']',
                $this->getModule()->getLanguageService()->getLL('label_move'),
                $this->getModule()->getLanguageService()->getLL('label_text_move')
            );

            $column[] = $logEntry->getUid();
            $column[] = $logEntry->getTstamp();
            $column[] = $logEntry->getAddress().' '.$editButton;
            $column[] = $moveButton;
            $columns[] = $column;
        }

        /* @var $tables \Sys25\RnBase\Backend\Utility\Tables */
        $tables = GeneralUtility::makeInstance(Tables::class);

        return $tables->buildTable($columns);
    }

    /**
     * Zeigt die Empfänger der Mail an.
     */
    protected function showReceiver(tx_mkmailer_models_Queue $mail): string
    {
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
        $ret = [];
        $receivers = $mail->getReceivers();
        for ($i = 0, $cnt = count($receivers); $i < $cnt; ++$i) {
            $receiverData = $receivers[$i];
            $receiver = $mailServ->createReceiver($receiverData);

            $addrCnt = $receiver->getAddressCount();
            $addrInfo = $addrCnt.' '.$this->getModule()->getLanguageService()->getLL('label_receivers');
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
     */
    private function handleDeleteMail(): string
    {
        $out = '';
        $uid = $this->getUidFromRequest('removeMail');
        if (0 === $uid || false === $uid) {
            return $out;
        }

        // Die Mail löschen
        $mailServ = tx_mkmailer_util_ServiceRegistry::getMailService();
        $mailServ->deleteMail($uid);

        return $out;
    }

    private function handleMoveLogEntryBackToQueue(): void
    {
        $uid = $this->getUidFromRequest('moveLogEntryBackToQueue');

        if (0 != $uid) {
            Connection::getInstance()->doUpdate(
                'tx_mkmailer_queue',
                'uid='.$uid,
                ['deleted' => '0']
            );
            Connection::getInstance()->doDelete('tx_mkmailer_log', 'receiver = '.$uid);
        }
    }

    /**
     * Liefert die Mail aus dem Request oder false.
     *
     * @return int
     */
    private function getUidFromRequest(string $varName): false|int
    {
        $uids = Parameters::getPostOrGetParameter($varName);
        if (!is_array($uids) || [] === $uids) {
            return false;
        }

        // Es sollte immer nur eine Mail drin liegen
        $mailUid = key($uids[0]);
        if (0 === $mailUid || ('' === $mailUid || '0' === $mailUid) || null === $mailUid) {
            return false;
        }

        return (int) $mailUid;
    }

    public function getModuleIdentifier(): string
    {
        return 'mkmailer';
    }
}
