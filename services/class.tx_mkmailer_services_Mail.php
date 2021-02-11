<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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

tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('tx_rnbase_util_Dates');
tx_rnbase::load('tx_mkmailer_mail_IMailJob');
tx_rnbase::load('tx_mkmailer_models_Queue');
tx_rnbase::load('tx_mkmailer_models_Log');
tx_rnbase::load('tx_mkmailer_mail_IMessage');
tx_rnbase::load('tx_rnbase_util_Files');
tx_rnbase::load('Tx_Rnbase_Utility_T3General');
tx_rnbase::load('tx_rnbase_util_Strings');
tx_rnbase::load('Tx_Rnbase_Service_Base');
tx_rnbase::load('tx_rnbase_util_Typo3Classes');

/**
 * E-Mail service.
 *
 * @author Rene Nitzsche
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_services_Mail extends Tx_Rnbase_Service_Base
{
    /**
     * Abarbeitung der MailQueue.
     *
     * @param tx_rnbase_configurations $configurations
     * @param string $confId
     *
     * @return string
     */
    public function executeQueue(
        tx_rnbase_configurations $configurations,
        $confId
    ) {
        tx_rnbase::load('tx_mkmailer_util_Misc');
        tx_rnbase::load('tx_rnbase_util_Lock');

        // wir sperren den prozess für eine bestimmte Zeit oder bis zum ende des durchlaufes
        $lockLifeTime = $configurations->getInt($confId.'lockLifeTime');
        $lock = tx_rnbase_util_Lock::getInstance('mkmailerqueue', $lockLifeTime);
        if ($lock->isLocked()) {
            return '<p>The Mail-Process is locked</p>';
        }
        if (!$lock->lockProcess()) {
            tx_rnbase::load('tx_rnbase_util_Logger');
            tx_rnbase_util_Logger::fatal(
                'Error in SendMailQueue: The Mail-Process couldn\'t be locked',
                'mkmailer',
                [
                    'Lock' => $lock,
                ]
            );

            return '<p>The Mail-Process couldn\'t be locked</p>';
        }

        $maxMails = $configurations->getInt($confId.'maxMails');
        $maxMails = $maxMails > 0 ? ($maxMails - 1) : 10;
        $mailMode = $configurations->get($confId.'mode');
        // Es ist auch PEAR möglich
        $mailMode = $mailMode ? $mailMode : 'PHPMAILER';
        // Die versendeten Mails über alle Queues zählen
        $sentQueueCnt = 0;
        // Die fehlerhaften Mails über alle Queues sammeln
        $sentErrors = [];
        // Laden von offenen Aufträge
        $queueArr = $this->getMailQueueOpen();
        foreach ($queueArr as $queue) {
            if ($sentQueueCnt > $maxMails) {
                break;
            }
            $receiverArr = $queue->getReceivers();
            if (!count($receiverArr)) {
                // Es sind keine Empfänger der Mail zugeordnet -> schliessen
                $this->closeMailQueue($queue);
                // der nächste bitte
                continue;
            }
            // Die versendeten Mails für diese Queue zählen
            $sentCnt = 0;
            // Fehlerhafte Mails zählen
            $errCnt = 0;
            foreach ($receiverArr as $receiverData) {
                if ($sentQueueCnt > $maxMails) {
                    break;
                }
                // Jetzt den eigentliche Receiver instanziieren, damit er uns die Mails erstellt
                $receiver = $this->createReceiver($receiverData);
                // Wir nähern uns dem Ziel!
                $mailSize = $receiver->getAddressCount();
                for ($i = 0; $i < $mailSize; ++$i) {
                    if ($sentQueueCnt > $maxMails) {
                        break;
                    }

                    $address = $receiver->getSingleAddress($i);
                    // Prüfen, ob diese Mail schon verschickt wurde!
                    if (is_array($address) && !$this->isMailSent($queue, $address['addressid'])) {
                        $message = $this->builtMessage(
                            $queue,
                            $receiver,
                            $i,
                            $configurations,
                            $confId
                        );

                        // sendMail erwartet als Empfänger entweder einen String oder ein Array. Das Array ist aber für
                        // verschiedene Empfänger gedacht. Daher muss das Datenarray nochmal in ein Array gepackt werden
                        try {
                            $this->sendEmail($message);
                            ++$sentCnt;
                            // Und jetzt den Versand loggen
                            $this->markMailAsSent($queue, $address['addressid'], $receiverData['uid']);
                        } catch (Exception $e) {
                            ++$errCnt;
                            $this->markMailAsSent($queue, $address['addressid'], $receiverData['uid'], true);
                            $sentErrors[] = 'QueueID: '.$queue->getUid().
                                ' ('.implode(',', $address).')'.
                                ' Msg: E-Mail konnte nicht gesendet werden. Sie können fehlerhafte Nachrichten im Backend bearbeiten. ('.$e->getMessage().')';
                            tx_rnbase::load('tx_rnbase_util_Logger');
                            tx_rnbase_util_Logger::fatal(
                                'Error in SendMailQueue: Eine Nachricht konnte aufgrund einer fehlerhaften E-Mail nicht versendet werden. Sie können diese Nachricht im Backend von MkMailer bearbeiten.',
                                'mkmailer',
                                [
                                    'Exception' => $e->getMessage(),
                                    'Queue' => $queue->record,
                                    'Address' => $address,
                                ]
                            );
                        }

                        // Gesamtzahl trotz Fehler erhöhen, um Endlosschleife zu verhindern
                        ++$sentQueueCnt;
                    }
                }
            }
            if (0 == $sentCnt && 0 == $errCnt) {
                // Für diese Queue wurden keine Mails mehr verschickt, sie kann also geschlossen werden
                $this->closeMailQueue($queue);
            } else {
                // Es wurden Mails verschickt. Diesen Zustand speichern
                $this->updateMailQueue($queue, $sentCnt);
            }
        }
        $lock->unlockProcess();

        $out = '&nbsp;';
        if (tx_rnbase_util_Network::isDevelopmentIp()) {
            $out = '<p>Finished with '.$sentQueueCnt.' Mails. Errors: '.count($sentErrors).'</p>';
            if (count($sentErrors)) {
                $out .= '<h3>Errors</h3><ul>';
                foreach ($sentErrors as $errorMsg) {
                    $out .= '<li>'.$errorMsg.LF;
                }
            }
        }

        return $out;
    }

    /**
     * Creates the message object to send.
     *
     * @param tx_mkmailer_models_Queue $queue
     * @param tx_mkmailer_receiver_IMailReceiver $receiver
     * @param int $idx
     * @param tx_rnbase_configurations $configurations
     * @param string $confId
     *
     * @return tx_mkmailer_mail_IMessage
     */
    protected function builtMessage(
        tx_mkmailer_models_Queue $queue,
        tx_mkmailer_receiver_IMailReceiver $receiver,
        $idx,
        tx_rnbase_configurations $configurations,
        $confId
    ) {
        // Address ist immer ein Array mit den Teilen der Mailadresse
        $message = $receiver->getSingleMail(
            $queue,
            $configurations->getFormatter(),
            $confId,
            $idx
        );

        $message->setFrom($queue->getFrom(), $queue->getFromName());
        $message->setCc(tx_mkmailer_util_Misc::parseAddressString($queue->getCc()));
        $message->setBcc(tx_mkmailer_util_Misc::parseAddressString($queue->getBcc()));

        // Zwei Optionen:
        // 1. Der Receiver kümmert sich um die Anhänge. Dann fügt er die gewünschten Attachments
        //    in die Message ein. Damit wird hier nicht weiter gemacht.
        // 2. Die Queue hat Anhänge und die Message nicht. Dann werden alle Anhänge der Queue an
        //    die Message angehängt.
        // Damit kann der Receiver auch dem Queue-Objekt weitere Anhänge hinzufügen, wenn er die
        // bestehenden Attachments nur erweitern will.
        if ($queue->getUploads() && !$message->getAttachments()) {
            $attachments = $queue->getUploads();
            foreach ($attachments as $attachment) {
                $message->addAttachment($attachment);
            }
        }

        if (($testMail = $configurations->get($confId.'testMail'))) {
            $message->setOption('testmail', $testMail);
            tx_rnbase_util_Debug::debug(
                $message,
                'tx_mkmailer_actions_SendMails - Diese Info wird nur im Testmodus angezeigt!'
            );
        }

        return $message;
    }

    /**
     * Liefert eine Mail an eine Liste von Empfängern. Es ist nicht garantiert, daß die Emails sofort
     * ausgeliefert werden. Sollte die Empfängerliste zu gross sein, dann werden die Mails ggf. in
     * eine Warteschlange eingefügt und per Cron ausgeliefert.
     * Die Emails werden noch einmal individuell für jeden Empfänger aufbereitet,
     * so daß eine individuelle Ansprache möglich ist.
     *
     * @param tx_mkmailer_mail_IMailJob $job
     *
     * @throws Exception
     *
     * @return void
     */
    public function spoolMailJob(
        tx_mkmailer_mail_IMailJob $job
    ) {
        $queue = $this->createQueueByJob($job);

        $mailUid = Tx_Rnbase_Database_Connection::getInstance()->doInsert(
            'tx_mkmailer_queue',
            $queue->getProperty(),
            0
        );

        foreach ($job->getReceiver() as $receiver) {
            // Dann jeden Receiver in die DB legen
            $data = [];
            $data['email'] = $mailUid;
            $data['resolver'] = get_class($receiver);
            $data['receivers'] = $receiver->getValueString();
            tx_rnbase_util_DB::doInsert('tx_mkmailer_receiver', $data, 0);
        }
    }

    /**
     * Creates an queue object.
     *
     * @param tx_mkmailer_mail_IMailJob $job
     *
     * @throws Exception
     *
     * @return tx_mkmailer_models_Queue
     */
    protected function createQueueByJob(
        tx_mkmailer_mail_IMailJob $job
    ) {
        if ($job->getCCs()) {
            $ccs = [];
            foreach ($job->getCCs() as $addr) {
                $ccs[] = $addr->getAddress();
            }
            $ccs = implode(',', $ccs);
        }

        if ($job->getBCCs()) {
            $bccs = [];
            foreach ($job->getBCCs() as $addr) {
                $bccs[] = $addr->getAddress();
            }
            $bccs = implode(',', $bccs);
        }

        $from = $job->getFrom();

        // Zuerst prüfen, wieviele Mails verschickt werden sollen
        $size = 0;
        foreach ($job->getReceiver() as $receiver) {
            $addCnt = $receiver->getAddressCount();
            // Ein Receiver der keine Mails versenden will, wurde falsch angelegt
            if (!$addCnt) {
                throw new Exception('Error in MailService: MailReceiver has no address! '.$receiver->__toString());
            }
            $size += $addCnt;
        }

        $data = [];
        $data['isstatic'] = 0;
        $data['prefer'] = $size < 10 ? 1 : 0;
        $data['subject'] = $job->getSubject();
        $data['contenttext'] = $job->getContentText();
        $data['contenthtml'] = $job->getContentHtml();
        $data['mail_from'] = is_object($from) ? $from->getAddress() : 'noreply@mkmailer.com';
        $data['mail_fromName'] = is_object($from) ? $from->getName() : '';
        $data['mail_cc'] = empty($ccs) ? '' : $ccs;
        $data['mail_bcc'] = empty($bccs) ? '' : $bccs;
        // Attachments werden serialisiert abgespeichert.
        $attachments = $job->getAttachments();
        $data['attachments'] = $attachments ? serialize($attachments) : '';
        $data['cr_date'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());

        return tx_rnbase::makeInstance(
            'tx_mkmailer_models_Queue',
            $data
        );
    }

    /**
     * Liefert eine Mail an eine Liste von Empfängern.
     *
     * @param tx_mkmailer_mail_IMailJob $job
     * @param tx_rnbase_configurations $configurations
     * @param string $confId
     *
     * @throws Exception
     *
     * @return void
     */
    public function executeMailJob(
        tx_mkmailer_mail_IMailJob $job,
        tx_rnbase_configurations $configurations,
        $confId
    ) {
        $queue = $this->createQueueByJob($job);
        // to many receivers (10 ore more), spool the job!
        if ($queue->getPrefer() <= 0) {
            $this->spoolMailJob($job);
        }

        tx_rnbase::load('tx_mkmailer_util_Misc');

        // send mail for each receiver
        foreach ($job->getReceiver() as $receiver) {
            /* @var $receiver tx_mkmailer_receiver_IMailReceiver */
            $mailSize = $receiver->getAddressCount();
            for ($i = 0; $i < $mailSize; ++$i) {
                $address = $receiver->getSingleAddress($i);
                if (is_array($address)) {
                    $message = $this->builtMessage(
                        $queue,
                        $receiver,
                        $i,
                        $configurations,
                        $confId
                    );

                    try {
                        $this->sendEmail($message);
                    } catch (Exception $e) {
                        tx_rnbase::load('tx_rnbase_util_Logger');
                        tx_rnbase_util_Logger::fatal(
                            'Error in SendMailQueue',
                            'mkmailer',
                            [
                                'Exception' => $e->getMessage(),
                                'Queue' => $queue->getProperty(),
                                'Address' => $address,
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Aktualisiert die Mailqueue in der DB.
     *
     * @param tx_mkmailer_models_Queue $mailQueue
     * @param int $mailCnt
     */
    public function updateMailQueue(tx_mkmailer_models_Queue $mailQueue, $mailCnt)
    {
        tx_rnbase::load('tx_rnbase_util_Dates');
        // Zuerst die eigentliche Mail speichern
        $data['mailcount'] = $mailQueue->getMailCount() + intval($mailCnt);
        $data['lastupdate'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
        $where = 'uid = '.$mailQueue->uid;
        tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', $where, $data, 0);
    }

    /**
     * Die übergegebene Mailqueue wird geschlossen. Es werden keine weiteren Mails verschickt.
     * Außerdem werden vorhandene Attachments vom Server gelöscht.
     *
     * @param tx_mkmailer_models_Queue $mailQueue
     */
    private function closeMailQueue(tx_mkmailer_models_Queue $mailQueue)
    {
        tx_rnbase::load('tx_rnbase_util_Dates');
        // Zuerst die eigentliche Mail speichern
        $data['deleted'] = 1;
        $data['lastupdate'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
        $where = 'uid = '.$mailQueue->uid;
        tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', $where, $data, 0);

        // FIXME: Löschen geht nicht und muss nochmal überarbeitet werden. Beim spoolen einer Mail
            // muss es folgende Optionen geben:
            // - Es kann eine Kopie des Anhangs angelegt oder das Original verwendet werden
            // (ich denke default sollte sein eine Kopie anzulegen)
            // - Nach dem abschicken wird die Kopie und optional auch das Original gelöscht
            // (default sollte nicht Original löschen sein)
//         if ($mailQueue->getUploads()) {
//             // FIXME: die stehen nicht mehr komasepariert in der DB!!!
//             // $mailQueue->getUploads() returns string or array[tx_mkmailer_mail_IAttachment]
//             $path = $this->getUploadDir();
//             $uploads = tx_rnbase_util_Strings::trimExplode(',', $mailQueue->getUploads());
//             if (is_array($uploads)) {
//                 foreach ($uploads as $upload) {
//                     $upload = $path . $upload;
//                     unlink($upload);
//                 }
//             }
//         }
    }

    /**
     * Liefert eine Array mit allen anstehenden Mails aus der Queue.
     *
     * @return array[tx_mkmailer_models_Queue]
     */
    public function getMailQueueOpen($options = [])
    {
        $what = array_key_exists('count', $options) ? 'count(uid) As cnt' : '*';
        $from = 'tx_mkmailer_queue';
        $where = 'deleted=0';

        $options['where'] = $where;
        $options['orderby'] = 'prefer desc, cr_date asc'; // FIFO - die älteste zuerst, Prefer mit Besserstellung
        $options['enablefieldsoff'] = 1;
        if (!array_key_exists('count', $options)) {
            $options['wrapperclass'] = 'tx_mkmailer_models_Queue';
        }
        $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);

        return array_key_exists('count', $options) ? $ret[0]['cnt'] : $ret;
    }

    /**
     * Liefert eine Array mit allen beendeten Mails aus der Queue.
     *
     * @return array[tx_mkmailer_models_Queue]
     */
    public function getMailQueueFinished($options = [])
    {
        $what = array_key_exists('count', $options) ? 'count(uid) As cnt' : '*';
        $from = 'tx_mkmailer_queue';

        $options['where'] = 'deleted=1';
        $options['orderby'] = 'lastupdate desc';
        $options['enablefieldsoff'] = 1;
        if (!array_key_exists('count', $options)) {
            $options['wrapperclass'] = 'tx_mkmailer_models_Queue';
        }
        $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);

        return array_key_exists('count', $options) ? $ret[0]['cnt'] : $ret;
    }

    /**
     * @param array $options
     *
     * @return array[tx_mkmailer_models_Log]
     */
    public function getLogEntriesForFailedMails(array $options = [])
    {
        $what = array_key_exists('count', $options) ? 'count(uid) As cnt' : '*';
        $from = 'tx_mkmailer_log';

        $options['where'] = 'failed=1';
        $options['orderby'] = 'tstamp desc';
        $options['enablefieldsoff'] = 1;
        if (!array_key_exists('count', $options)) {
            $options['wrapperclass'] = 'tx_mkmailer_models_Log';
        }

        $ret = Tx_Rnbase_Database_Connection::getInstance()->doSelect($what, $from, $options, 0);

        return array_key_exists('count', $options) ? $ret[0]['cnt'] : $ret;
    }

    /**
     * Delete a mail job from mail queue.
     *
     * @param int $uid
     *
     * @return int number of affected mails
     */
    public function deleteMail($uid)
    {
        return tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', 'uid='.$uid, ['deleted' => '1']);
    }

    /**
     * Liefert die Empfänger einer gespoolten Mail.
     *
     * @param tx_mkmailer_models_Queue $mailQueue
     *
     * @return array
     */
    public function getMailReceivers(tx_mkmailer_models_Queue $mailQueue)
    {
        $what = '*';
        $from = 'tx_mkmailer_receiver';

        $options['where'] = 'email='.$mailQueue->uid;
        $options['enablefieldsoff'] = 1;
        $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);

        return $ret;
    }

    /**
     * Erstellt einen Receiver mit den gespeicherten Daten aus der DB.
     *
     * @param array $receiverArr Datensatz aus der Tabelle tx_mkmailer_receiver
     *
     * @return tx_mkmailer_receiver_IMailReceiver
     */
    public function createReceiver($receiverArr)
    {
        $clazzName = $receiverArr['resolver'];
        tx_rnbase::load($clazzName);
        $receiver = new $clazzName();
        $receiver->setValueString($receiverArr['receivers']);

        return $receiver;
    }

    /**
     * Erstellt einen MailReceiver für den Mailversand an einen FEUser.
     *
     * @param tx_t3users_models_feuser $feuser
     *
     * @return tx_mkmailer_receiver_IMailReceiver
     */
    public function createReceiverFeUser($feuser)
    {
        tx_rnbase::load('tx_mkmailer_receiver_FeUser');
        $receiver = new tx_mkmailer_receiver_FeUser();
        $receiver->setFeUser($feuser);

        return $receiver;
    }

    /**
     * Returns the target dir of uploaded attachments.
     *
     * @return string
     */
    public function getUploadDir()
    {
        $uploadDir = tx_rnbase_util_Files::getFileAbsFileName(
            Tx_Rnbase_Utility_T3General::fixWindowsFilePath(
                'typo3temp/mkmailer/'
            )
        );

        if (!file_exists($uploadDir)) {
            tx_rnbase_util_Files::mkdir_deep($uploadDir);
        }

        return $uploadDir;
    }

    /**
     * Versand einer Email aus der TYPO3 Umgebung
     * Die Empfänger werden entweder als kommaseparierter String erwartet oder als
     * verschachteltes Array. Dabei wird für jede Adresse ein assoziatives Array mit
     * folgenden Key erwartet:
     * - address : die Mailadresse
     * - addressName : Der Name des Emfängers (optional).
     *
     * @param string $msg Inhalt der Mail. Die erste Zeile ist das Subject
     * @param mixed $recipients die Empfänger der Mail als Array oder String
     * @param string $from
     * @param array $options
     *
     * @throws tx_mkmailer_exceptions_SendMail
     *
     * @todo SwiftMailer unterstützen
     */
    public function sendEmail(tx_mkmailer_mail_IMessage $msg)
    {
        $this->sendEmail_PHPMailer($msg);
    }

    /**
     * Versand einer Mail über den PHPMailer
     * http://phpmailer.sourceforge.net.
     *
     * @param string $msg Inhalt der Mail. Die erste Zeile ist das Subject
     * @param mixed $$recipients die Empfänger der Mail als Array oder String
     * @param string $from
     * @param array $options
     *
     * @throws tx_mkmailer_exceptions_SendMail
     */
    private function sendEmail_PHPMailer(tx_mkmailer_mail_IMessage $msg)
    {
        require_once tx_rnbase_util_Extensions::extPath('mkmailer').'phpmailer/class.phpmailer.php';
        $options = $msg->getOptions();
        $mail = new PHPMailer();
        $mail->LE = "\n"; // Bei \r\n gibt es Probleme
        $mail->CharSet = $options['charset'] ? $options['charset'] : 'UTF-8'; // Default: iso-8859-1
        $mail->Encoding = $options['encoding'] ? $options['encoding'] : '8bit'; // Options for this are "8bit", "7bit", "binary", "base64", and "quoted-printable".

        $mail->FromName = $msg->getFrom()->getName(); // Default Fromname

        // Absender
        $mail->From = $msg->getFrom()->getAddress();
        // Return-Path
        if ($options['returnpath']) {
            // wenn 1 den Absender als Returnpath, anstonsten die angegebene Adresse
            $mail->Sender = 1 == $options['returnpath'] ? $mail->From : $options['returnpath'];
        }

        $mail->Subject = $msg->getSubject();
        if ($msg->getHtmlPart()) {
            $mail->isHTML(true);
            $mail->Body = $msg->getHtmlPart();
            $mail->AltBody = $msg->getTxtPart();
        } else {
            $mail->Body = $msg->getTxtPart();
        }

        // Die Empfänger
        $addresses = $msg->getTo();
        if (isset($options['testmail']) && $options['testmail']) {
            // Die Mail wird an eine Testadresse verschickt
            tx_rnbase_util_Debug::debug($addresses, 'tx_mkmailer_actions_SendMails - Diese Info wird nur im Testmodus angezeigt! Send Testmail to '.$options['testmail'].' FROM: '.$from.''); // TODO: Remove me!
            $testAddrs = tx_rnbase_util_Strings::trimExplode(',', $options['testmail']);
            tx_rnbase::load('tx_mkmailer_mail_Factory');
            foreach ($testAddrs as $addr) {
                $mail = $this->addAddress(
                    $mail,
                    tx_mkmailer_mail_Factory::createAddressInstance($addr)
                );
            }
        } else {
            // Der scharfe Versand
            foreach ($addresses as $address) {
                $mail = $this->addAddress($mail, $address);
            }
            $addresses = $msg->getCc();
            foreach ($addresses as $address) {
                $mail = $this->addCCAddress($mail, $address);
            }
            $addresses = $msg->getBcc();
            foreach ($addresses as $address) {
                $mail = $this->addBCCAddress($mail, $address);
            }
        }
        // Integration der Attachments
        $attachments = $msg->getAttachments();
        if (is_array($attachments) && count($attachments)) {
            foreach ($attachments as $attachment) {
                switch ($attachment->getAttachmentType()) {
                    case tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT:
                        $mail->addAttachment($attachment->getPathOrContent(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
                        break;
                    case tx_mkmailer_mail_IAttachment::TYPE_EMBED:
                        $mail->addEmbeddedImage($attachment->getPathOrContent(), $attachment->getEmbedId(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
                        break;
                    case tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT:
                        $mail->addStringAttachment($attachment->getPathOrContent(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
                        break;

                    default:
                        tx_rnbase_util_Logger::warn('Email with unknown attachment type given!', 'mkmailer', [
                            'AttachmentType' => $attachment->getAttachmentType(),
                            'Content' => $attachment->getPathOrContent(),
                            'Subject' => $msg->getSubject(), ]);
                        break;
                }
            }
        }

        $ret = $mail->send();
        if (!$ret) {
            // Versandfehler. Es wird eine Exception geworfen
            throw tx_rnbase::makeInstance('tx_mkmailer_exceptions_SendMail', $mail->ErrorInfo);
        }
    }

    /**
     * @param PHPMailer $mail
     * @param tx_mkmailer_mail_IAddress $address
     * @param string $method
     *
     * @return PHPMailer $mail
     */
    private function addAddress($mail, tx_mkmailer_mail_IAddress $address, $method = 'addAddress')
    {
        $mailAdr = $address->getAddress();

        if (Tx_Rnbase_Utility_Strings::validEmail($mailAdr)) {
            $mail->{$method}($mailAdr, $address->getName());
        } else {
            throw new Exception('[Method: '.$method.'] Invalid Email address ('.$mailAdr.') given. Mail not sent!');
        }

        return $mail;
    }

    /**
     * @param PHPMailer $mail
     * @param tx_mkmailer_mail_IAddress $address
     *
     * @return PHPMailer $mail
     */
    private function addCCAddress($mail, tx_mkmailer_mail_IAddress $address)
    {
        return $this->addAddress($mail, $address, 'addCC');
    }

    /**
     * @param PHPMailer $mail
     * @param tx_mkmailer_mail_IAddress $address
     *
     * @return PHPMailer $mail
     */
    private function addBCCAddress($mail, tx_mkmailer_mail_IAddress $address)
    {
        return $this->addAddress($mail, $address, 'addBCC');
    }

    /**
     * Find a mail template.
     *
     * @param string $id mail type string
     *
     * @return tx_mkmailer_models_Template
     */
    public function getTemplate($id)
    {
        $what = '*';
        $from = 'tx_mkmailer_templates';
        $where = 'mailtype='.Tx_Rnbase_Database_Connection::getInstance()->fullQuoteStr(strtolower($id), $from);

        $options['where'] = $where;
        $options['wrapperclass'] = 'tx_mkmailer_models_Template';
        $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
        if (!count($ret)) {
            throw tx_rnbase::makeInstance('tx_mkmailer_exceptions_NoTemplateFound', 'Mail template with key \''.$id.'\' not found!');
        }

        return count($ret) ? $ret[0] : null;
    }

    /**
     * Prüft, ob eine bestimmte Email schon an den Empfänger ausgeliefert wurde.
     *
     * @param tx_mkmailer_models_Queue $mailQueue
     * @param string $mailAddress
     * return boolean true, wenn die Mail schon verschickt wurde
     */
    private function isMailSent(tx_mkmailer_models_Queue $mailQueue, $mailAddress)
    {
        // Entscheidend ist die Tabelle tx_mkmailer_log
        // Wenn dort die Mailadresse schon drin liegt, dann wurde sie schon verschickt.
        $what = '*';
        $from = 'tx_mkmailer_log';

        $options['where'] = 'email='.$mailQueue->uid.' AND LOWER(address) = LOWER(\''.addslashes($mailAddress).'\')';
        $options['enablefieldsoff'] = 1;
        $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);

        return count($ret) > 0;
    }

    /**
     * Markiert diese Mailadresse als abgearbeitet in der Mailqueue.
     *
     * @param tx_mkmailer_models_Queue $queue
     * @param string $mailAddress
     * @param string $receiver
     * @param bool $failed
     *
     * @return void
     */
    private function markMailAsSent(
        tx_mkmailer_models_Queue $queue,
        $mailAddress,
        $receiver,
        $failed = false
    ) {
        if (!$queue->isPersisted()) {
            return;
        }

        $row = [];
        $row['tstamp'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
        $row['email'] = $queue->getUid();
        $row['address'] = $mailAddress;

        $row['receiver'] = $receiver;
        $row['failed'] = $failed;

        Tx_Rnbase_Database_Connection::getInstance()->doInsert('tx_mkmailer_log', $row, 0);
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/services/class.tx_mkmailer_services_Mailer.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/services/class.tx_mkmailer_services_Mailer.php'];
}
