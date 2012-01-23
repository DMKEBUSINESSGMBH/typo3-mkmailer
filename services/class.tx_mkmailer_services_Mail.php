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
require_once(PATH_t3lib.'class.t3lib_svbase.php');

tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('tx_rnbase_util_Dates');



/**
 * Mailing service
 *
 * @author Rene Nitzsche
 */
class tx_mkmailer_services_Mail extends t3lib_svbase {

	/**
	 * Abarbeitung der MailQueue
	 * @param $configurations
	 * @param $confId
	 * @return string
	 */
	public function executeQueue(&$configurations, $confId){
		tx_rnbase::load('tx_mkmailer_util_Misc');

		$maxMails = intval($configurations->get($confId.'maxMails'));
		$testMail = $configurations->get($confId.'testMail');
		$mailMode = $configurations->get($confId.'mode');
		$mailMode = $mailMode ? $mailMode : 'PHPMAILER'; // Es ist auch PEAR möglich

		$maxMails = $maxMails > 0 ? ($maxMails-1) : 10;
		$sentQueueCnt = 0; // Die versendeten Mails über alle Queues zählen
		$sentErrors = array(); // Die fehlerhaften Mails über alle Queues sammeln
		$formatter = $configurations->getFormatter();
		// Laden von offenen Aufträge
		$queueArr = $this->getMailQueue();
		foreach ($queueArr As $queue) {
			if($sentQueueCnt > $maxMails) break;
			$receiverArr = $queue->getReceivers();
			if(!count($receiverArr)) {
				// Es sind keine Empfänger der Mail zugeordnet -> schliessen
				$this->closeMailQueue($queue);
				continue; // der nächste bitte
			}
			$sentCnt = 0; // Die versendeten Mails für diese Queue zählen
			$errCnt = 0; // Fehlerhafte Mails zählen
			foreach($receiverArr As $receiverData) {
				if($sentQueueCnt > $maxMails) break;
				// Jetzt den eigentliche Receiver instanziieren, damit er uns die Mails erstellt
				$receiver = $this->createReceiver($receiverData);
				// Wir nähern uns dem Ziel!
				$mailSize = $receiver->getAddressCount();
				for($i=0; $i < $mailSize; $i++) {
					if($sentQueueCnt > $maxMails) break;
					
					$address = $receiver->getSingleAddress($i);
					// Prüfen, ob diese Mail schon verschickt wurde!
					if(is_array($address) && !$this->isMailSent($queue, $address['addressid'])) {
						// Address ist immer ein Array mit den Teilen der Mailadresse
//						$options = array();
//						$options['mailerMode'] = $mailMode;
						$message = $receiver->getSingleMail($queue, $formatter, $confId, $i);

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
						if($queue->getUploads() && !$message->getAttachments()) {
							$attachments = $queue->getUploads();
							foreach ($attachments As $attachment) {
								$message->addAttachment($attachment);
							}
						}

						if($testMail) {
							$message->setOption('testmail', $testMail);
							t3lib_div::debug($message, 'tx_mkmailer_actions_SendMails Diese Info wird nur im Testmodus angezeigt!'); // TODO: Remove me!
						}
						// sendMail erwartet als Empfänger entweder einen String oder ein Array. Das Array ist aber für
						// verschiedene Empfänger gedacht. Daher muss das Datenarray nochmal in ein Array gepackt werden
						try {
							$this->sendEmail($message);
							$sentCnt++;
							// Und jetzt den Versand loggen
							$this->markMailAsSent($queue, $address['addressid']);
						}
						catch(Exception $e) {
							$errCnt++;
							$sentErrors[] = 'QueueID: ' . $queue->uid . ' (' . implode(',',$address) . ') Msg: ' . $e->getMessage();
							tx_rnbase::load('tx_rnbase_util_Logger');
							tx_rnbase_util_Logger::fatal('Error in SendMailQueue', 'mkmailer',
								array('Exception' => $e->getMessage(), 'Queue' => $queue->record, 'Address' => $address));
//							error_log('Error in SendMailQueue: ' . $msg);
						}
						$sentQueueCnt++; // Gesamtzahl trotz Fehler erhöhen, um Endlosschleife zu verhindern
					}
				}
			}
			if($sentCnt == 0 && $errCnt == 0) {
				// Für diese Queue wurden keine Mails mehr verschickt, sie kann also geschlossen werden
				$this->closeMailQueue($queue);
			}
			else {
				// Es wurden Mails verschickt. Diesen Zustand speichern
				$this->updateMailQueue($queue, $sentCnt);
			}
		}
		$out = '<p>Finished with ' . $sentQueueCnt . ' Mails. Errors: ' . count($sentErrors) .'</p>';
		if(count($sentErrors)) {
			$out .= '<h3>Errors</h3><ul>';
			foreach($sentErrors As $errorMsg) {
				$out .= "<li>$errorMsg \n";
			}
		}
		return $out;
	}

	/**
	 * Liefert eine Mail an eine Liste von Empfängern. Es ist nicht garantiert, daß die Emails sofort
	 * ausgeliefert werden. Sollte die Empfängerliste zu gross sein, dann werden die Mails ggf. in
	 * eine Warteschlange eingefügt und per Cron ausgeliefert.
	 * Die Emails werden noch einmal individuell für jeden Empfänger aufbereitet, so daß eine individuelle
	 * Ansprache möglich ist.
	 *
	 * @param tx_mkmailer_mail_IMailJob $job
	 * @throws Exception
	 */
	public function spoolMailJob(tx_mkmailer_mail_IMailJob $job) {
		// $msg, $receivers, $from, $options = array()

		if($job->getCCs()) {
			$ccs = array();
			foreach($job->getCCs() As $addr) $ccs[] = $addr->getAddress();
			$ccs = implode(',' , $ccs);
		}

		if($job->getBCCs()) {
			$bccs = array();
			foreach($job->getBCCs() As $addr) $bccs[] = $addr->getAddress();
			$bccs = implode(',' , $bccs);
		}

		$from = $job->getFrom();

		// Zuerst prüfen, wieviele Mails verschickt werden sollen
		$size = 0;
		foreach($job->getReceiver() As $receiver) {
			$addCnt = $receiver->getAddressCount();
			if(!$addCnt) { // Ein Receiver der keine Mails versenden will, wurde falsch angelegt
				error_log('Error in MailService: MailReceiver has no address! ' . $receiver->__toString());
				error_log('Error in MailService: Msg:' . $msg);
				throw new Exception('Error in MailService: MailReceiver has no address! ' . $receiver->__toString());
			}
			$size += $addCnt;
		}

		// Zuerst die eigentliche Mail speichern
		$data['isstatic'] = 0;
		$data['prefer'] = $size < 10 ? 1 : 0;
		$data['subject'] = $job->getSubject();
		$data['contenttext'] = $job->getContentText();
		$data['contenthtml'] = $job->getContentHtml();
		$data['mail_from'] = is_object($from) ? $from->getAddress() : 'noreply@mkmailer.com';
		$data['mail_fromName'] = is_object($from) ? $from->getName() : '';
		$data['mail_cc'] = $ccs;
		$data['mail_bcc'] = $bccs;
		// Attachments werden serialisiert abgespeichert.
		$attachments = $job->getAttachments();
		$data['attachments'] = $attachments ? serialize($attachments) : '';
		$data['cr_date'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());

		$mailUid = tx_rnbase_util_DB::doInsert('tx_mkmailer_queue',$data,0);
		foreach($job->getReceiver() As $receiver) {
			// Dann jeden Receiver in die DB legen
			$data = array();
			$data['email'] = $mailUid;
			$data['resolver'] = get_class($receiver);
			$data['receivers'] = $receiver->getValueString();
			tx_rnbase_util_DB::doInsert('tx_mkmailer_receiver',$data,0);
		}
	}

	/**
	 * Aktualisiert die Mailqueue in der DB.
	 *
	 * @param tx_mkmailer_models_Queue $mailQueue
	 * @param int $mailCnt
	 */
	public function updateMailQueue(tx_mkmailer_models_Queue $mailQueue, $mailCnt) {
		tx_rnbase::load('tx_rnbase_util_Dates');
		// Zuerst die eigentliche Mail speichern
		$data['mailcount'] = $mailQueue->getMailCount() + intval($mailCnt);
		$data['lastupdate'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
		$where = 'uid = ' . $mailQueue->uid;
		tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', $where,$data,0);
	}

	/**
	 * Die übergegebene Mailqueue wird geschlossen. Es werden keine weiteren Mails verschickt.
	 * Außerdem werden vorhandene Attachments vom Server gelöscht
	 *
	 * @param tx_mkmailer_models_Queue $mailQueue
	 */
	private function closeMailQueue(tx_mkmailer_models_Queue $mailQueue) {
		tx_rnbase::load('tx_rnbase_util_Dates');
		// Zuerst die eigentliche Mail speichern
		$data['deleted'] = 1;
		$data['lastupdate'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
		$where = 'uid = ' . $mailQueue->uid;
		tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', $where,$data,0);
		// Jetzt noch die Uploads löschen
		if($mailQueue->getUploads()) {
			//FIXME: die stehen nicht mehr komasepariert in der DB!!!
			// $mailQueue->getUploads() returns string or array[tx_mkmailer_mail_IAttachment]
			$path = $this->getUploadDir();
			$uploads = t3lib_div::trimExplode(',', $mailQueue->getUploads());
			foreach($uploads As $upload) {
				$upload = $path . $upload;
				unlink($upload);
			}
		}
	}
	/**
	 * Liefert eine Array mit allen anstehenden Mails aus der Queue
	 * @return array[tx_mkmailer_models_Queue]
	 */
	public function getMailQueue($options = array()) {
		$what = array_key_exists('count',$options) ? 'count(uid) As cnt' : '*';
		$from = 'tx_mkmailer_queue';
		$where = 'deleted=0' ;

		$options['where'] = $where;
		$options['orderby'] = 'prefer desc, cr_date asc'; // FIFO - die älteste zuerst, Prefer mit Besserstellung
		$options['enablefieldsoff'] = 1;
		if(!array_key_exists('count',$options))
			$options['wrapperclass'] = 'tx_mkmailer_models_Queue';
		$ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
		return array_key_exists('count',$options) ? $ret[0]['cnt'] : $ret;
	}

	/**
	 * Liefert eine Array mit allen beendeten Mails aus der Queue
	 * @return array[tx_mkmailer_models_Queue]
	 */
	public function getMailQueueFinished($options = array()) {
		$what = array_key_exists('count',$options) ? 'count(uid) As cnt' : '*';
		$from = 'tx_mkmailer_queue';

		$options['where'] = 'deleted=1';
		$options['orderby'] = 'lastupdate desc';
		$options['enablefieldsoff'] = 1;
		if(!array_key_exists('count',$options))
			$options['wrapperclass'] = 'tx_mkmailer_models_Queue';
		$ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
		return array_key_exists('count',$options) ? $ret[0]['cnt'] : $ret;
	}

	/**
	 * Delete a mail job from mail queue
	 * @param int $uid
	 * @return int number of affected mails
	 */
	public function deleteMail($uid) {
		return tx_rnbase_util_DB::doUpdate('tx_mkmailer_queue', 'uid='.$uid, array('deleted'=>'1'));
	}
	/**
	 * Liefert die Empfänger einer gespoolten Mail.
	 *
	 * @param tx_mkmailer_models_Queue $mailQueue
	 * @return array
	 */
	public function getMailReceivers(tx_mkmailer_models_Queue $mailQueue) {
		$what = '*';
    $from = 'tx_mkmailer_receiver';

    $options['where'] = 'email=' . $mailQueue->uid;
    $options['enablefieldsoff'] = 1;
    $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
    return $ret;
	}

	/**
	 * Erstellt einen Receiver mit den gespeicherten Daten aus der DB
	 *
	 * @param array $receiverArr Datensatz aus der Tabelle tx_mkmailer_receiver
	 * @return tx_mkmailer_receiver_IMailReceiver
	 */
	public function createReceiver($receiverArr) {
		$clazzName = $receiverArr['resolver'];
		tx_rnbase::load($clazzName);
		$receiver = new $clazzName;
		$receiver->setValueString($receiverArr['receivers']);
		return $receiver;
	}

	/**
	 * Erstellt einen MailReceiver für den Mailversand an einen FEUser
	 *
	 * @param tx_t3users_models_feuser $feuser
	 * @return tx_mkmailer_receiver_IMailReceiver
	 */
	public function createReceiverFeUser($feuser) {
		tx_rnbase::load('tx_mkmailer_receiver_FeUser');
		$receiver = new tx_mkmailer_receiver_FeUser();
		$receiver->setFeUser($feuser);
		return $receiver;
	}
	
	/**
	 * Returns the target dir of uploaded attachments
	 *
	 * @return string
	 */
	function getUploadDir() {
		require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
		$oFileTool = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		return t3lib_div::getFileAbsFileName(t3lib_div::fixWindowsFilePath(
			$oFileTool->slashPath(
				$oFileTool->rmDoubleSlash('typo3temp/mkmailer')
			)
		));
	}

	/**
	 * Versand einer Email aus der TYPO3 Umgebung
	 * Die Empfänger werden entweder als kommaseparierter String erwartet oder als
	 * verschachteltes Array. Dabei wird für jede Adresse ein assoziatives Array mit
	 * folgenden Key erwartet:
	 * - address : die Mailadresse
	 * - addressName : Der Name des Emfängers (optional)
	 *
	 * @param string $msg Inhalt der Mail. Die erste Zeile ist das Subject
	 * @param mixed $recipients die Empfänger der Mail als Array oder String
	 * @param string $from
	 * @param array $options
	 * @throws tx_mkmailer_exceptions_SendMail
	 */
	public function sendEmail(tx_mkmailer_mail_IMessage $msg) {

		$this->sendEmail_PHPMailer($msg);
	}
	/**
	 * Versand einer Mail über den PHPMailer
	 * http://phpmailer.sourceforge.net
	 *
	 * @param string $msg Inhalt der Mail. Die erste Zeile ist das Subject
	 * @param mixed $$recipients die Empfänger der Mail als Array oder String
	 * @param string $from
	 * @param array $options
	 * @throws tx_mkmailer_exceptions_SendMail
	 */
	private function sendEmail_PHPMailer(tx_mkmailer_mail_IMessage $msg) {
		require_once(t3lib_extMgm::extPath('mkmailer').'phpmailer/class.phpmailer.php');
		$options = $msg->getOptions();
		$mail = new PHPMailer();
		$mail->LE = "\n"; // Bei \r\n gibt es Probleme
		$mail->CharSet = $options['charset'] ? $options['charset'] : 'UTF-8'; // Default: iso-8859-1
		$mail->Encoding = $options['encoding'] ? $options['encoding'] : '8bit'; // Options for this are "8bit", "7bit", "binary", "base64", and "quoted-printable".

		
		$mail->FromName = $msg->getFrom()->getName(); // Default Fromname

		// Absender
		$mail->From = $msg->getFrom()->getAddress();
		// Return-Path
		if($options['returnpath'])
			// wenn 1 den Absender als Returnpath, anstonsten die angegebene Adresse
			$mail->Sender = $options['returnpath'] == 1 ? $mail->From : $options['returnpath'];
		
		$mail->Subject = $msg->getSubject();
		if($msg->getHtmlPart()) {
			$mail->IsHTML(true);
			$mail->Body = $msg->getHtmlPart();
			$mail->AltBody = $msg->getTxtPart();
		}
		else {
			$mail->Body = $msg->getTxtPart();
		}

		// Die Empfänger
		$addresses = $msg->getTo();
		if(isset($options['testmail']) && $options['testmail']) {
			// Die Mail wird an eine Testadresse verschickt
			t3lib_div::debug($addresses, 'Send Testmail to '.$options['testmail'].' FROM: ' . $from .' tx_dsagbase_services_Mail'); // TODO: Remove me!
			$testAddrs = t3lib_div::trimExplode(',', $options['testmail']);
			foreach($testAddrs As $addr)
				$mail->AddAddress($addr);
		}
		else {
			// Der scharfe Versand
			foreach($addresses As $address) {
				$mail->AddAddress($address->getAddress(), $address->getName());
			}
			$addresses = $msg->getCc();
			foreach($addresses As $address) {
				$mail->AddCC($address->getAddress(), $address->getName());
			}
			$addresses = $msg->getBcc();
			foreach($addresses As $address) {
				$mail->AddBCC($address->getAddress(), $address->getName());
			}
		}
		// Integration der Attachments
		$attachments = $msg->getAttachments();
		if(is_array($attachments) && count($attachments))
			foreach($attachments As $attachment) {
				switch ($attachment->getAttachmentType()) {
					case tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT :
						$mail->AddAttachment($attachment->getPathOrContent(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
					break;
					case tx_mkmailer_mail_IAttachment::TYPE_EMBED :
						$mail->AddEmbeddedImage($attachment->getPathOrContent(), $attachment->getEmbedId(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
					break;
					case tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT :
						$mail->AddStringAttachment($attachment->getPathOrContent(), $attachment->getName(), $attachment->getEncoding(), $attachment->getMimeType());
					break;

					default:
						tx_rnbase_util_Logger::warn('Email with unknown attachment type given!','mkmailer', array(
							'AttachmentType'=>$attachment->getAttachmentType(),
							'Content' => $attachment->getPathOrContent(),
							'Subject' => $msg->getSubject(),)
						);
					break;
				}
			}
		
		$ret = $mail->Send();
		if(!$ret) {
			// Versandfehler. Es wird eine Exception geworfen
			throw tx_rnbase::makeInstance('tx_mkmailer_exceptions_SendMail', $mail->ErrorInfo);
		}
	}

	/**
	 * Find a mail template
	 *
	 * @param string $id mail type string
	 * @return tx_mkmailer_models_Template
	 */
	public function getTemplate($id) {
    $what = '*';
    $from = 'tx_mkmailer_templates';
    $where = 'mailtype=\'' . strtolower($id) .'\'' ;

    $options['where'] = $where;
    $options['wrapperclass'] = 'tx_mkmailer_models_Template';
    $ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
    if(!count($ret)) throw new Exception('Mail template with key \'' . $id .'\' not found!');
    return count($ret) ? $ret[0] : null;
	}

	/**
	 * Prüft, ob eine bestimmte Email schon an den Empfänger ausgeliefert wurde.
	 *
	 * @param tx_mkmailer_models_Queue $mailQueue
	 * @param string $mailAddress
	 * return boolean true, wenn die Mail schon verschickt wurde
	 */
	private function isMailSent(tx_mkmailer_models_Queue $mailQueue, $mailAddress) {
		// Entscheidend ist die Tabelle tx_mkmailer_log
		// Wenn dort die Mailadresse schon drin liegt, dann wurde sie schon verschickt.
		$what = '*';
    $from = 'tx_mkmailer_log';

    $options['where'] = 'email=' . $mailQueue->uid . ' AND LOWER(address) = LOWER(\'' . addslashes($mailAddress) . '\')';
    $options['enablefieldsoff'] = 1;
		$ret = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);
		return count($ret) > 0;
	}
	/**
	 * Markiert diese Mailadresse als abgearbeitet in der Mailqueue.
	 *
	 * @param tx_mkmailer_models_Queue $queue
	 * @param string $mailAddress
	 */
	private function markMailAsSent(tx_mkmailer_models_Queue $queue, $mailAddress) {
		$row['tstamp'] = tx_rnbase_util_Dates::datetime_tstamp2mysql(time());
		$row['email'] = $queue->uid;
		$row['address'] = $mailAddress;
	  tx_rnbase_util_DB::doInsert('tx_mkmailer_log', $row, 0);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/services/class.tx_mkmailer_services_Mailer.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/services/class.tx_mkmailer_services_Mailer.php']);
}

?>