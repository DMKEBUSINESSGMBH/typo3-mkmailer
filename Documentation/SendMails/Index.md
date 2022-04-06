Eine Mail verschicken
=====================

Direkter Versand
----------------

Als einfachsten Weg kann man direkt eine Mail verschicken. Dieser Weg ist aber nicht empfohlen, da die Mail direkt verschickt wird und nicht über die Queue ausgeliefert wird.

~~~~ {.sourceCode .php}
$msg = new tx_mkmailer_mail_SimpleMessage();
$msg->addTo($to, $toName);
$html = "
Das ist der HTML-Teil der Email.

Das ist <a href=\"http://www.google.de/\">Google</a>.
";
$text = "Das ist eine Textmail.\nZeilenumbrüche gehen auch...";
$msg->setTxtPart($text);
$msg->setHtmlPart($html);
$msg->setSubject('Das ist eine Testmail.');
$msg->setFrom('test@dmk-ebusiness.de', 'Servermail');
$srv = tx_mkmailer_util_ServiceRegistry::getMailService();
$srv->sendEmail($msg);
~~~~

**Versand aus dem BE**

Die PID der Mailseite muss in die Extension eingetragen werden, damit das Typoscript geladen wird!

Einstellen eines Jobs in die Queue
----------------------------------

Besser ist es der Platform einen Auftrag für den Versand einer Mail zu geben. Der Mechanismus dafür ist etwas umfangreicher. Zunächst muss man wissen, daß man eine Mail nicht nur an einzelne Personen verschicken kann, sondern auch an Gruppen. Dafür benötigt man einen MailReceiver. Das ist eine Implementierung des Interfaces **tx\_mkmailer\_receiver\_IMailReceiver**. Die Extension mkmailer liefert bereits fertige Implementierungen mit. Die einfachste ist **tx\_mkmailer\_receiver\_Email**. Man kann einem Mailauftrag auch mehrere Receiver mitgeben.

~~~~ {.sourceCode .php}
$mailSrv = tx_mkmailer_util_ServiceRegistry::getMailService();
$templateKey = 'info_mail_123'; // Der Key ist abhängig von der Applikation. Das entsprechende Template muss im BE angelegt sein
$templateObj = $mailSrv->getTemplate($templateKey);

$from = 'test@egal.de';
// Den Empfänger der Mail als Receiver anlegen, Hier ein Standardreceiver, man kann aber auch eigene Receiver schreiben
$receiver = new tx_mkmailer_receiver_FeUser();
$receiver->setFeUser($feuser);

$job = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_mkmailer_mail_MailJob');
$job->addReceiver($receiver);
$job->setFrom($templateObj->getFromAddress());
$job->setCCs($templateObj->getCcAddress());
$job->setBCCs($templateObj->getBccAddress());

$txtPart = $templateObj->getContentText();
$htmlPart = $templateObj->getContentHtml();
$subject = $templateObj->getSubject();
// Die Mailinhalte können jetzt durch verschiedene zusätzliche Marker geschickt werden, um Platzhalter zu ersetzen.
// Der FEUser wird beim Versand schon automatisch ersetzt! Es geht also nur um zusätzliche Daten.

$job->setSubject($tsubject);
$job->setContentText($txtPart);
$job->setContentHtml($htmlPart);

// Anhänge hinzufügen
$attachment = tx_mkmailer_mail_Factory::createAttachment($attachmentPath);
$job->addAttachment($attachment);

// Und nun geht alles in den Versand
$mailSrv->spoolMailJob($job);
~~~~

Wenn die Mail aus dem BE angestossen wird, dann kann es sinnvoll sein, die Zusatzdaten auch erst beim Versand zu ersetzen. Dieser erfolgt im FE und kann voll mit TypoScript konfiguriert werden. Es ist dann aber ein eigener Receiver notwendig.

Verarbeitung der Queue
----------------------

Damit die E-Mails aus der Queue auch versendet werden, muss im Typo3 eine Seite eingerichtet und das Plugin von MKmailer angelegt werden.

Damit die Mails dann automatisch versendet werden, gibt es 2 Wege:

Wenn mklib installiert ist und mind. TYPO3 6.2 genutzt wird, kann einfach der TYPO3 Scheduler von mkmailer verwendet werden. Damit dieser funktioniert muss lediglich die cronpage in den Extension Einstellungen konfiguriert sein.

Ohne mklib muss ein crontab eingerichtet werden.

Unter Linux sieht das ganze dann so aus.

**crontab -e**

**0-59/2 \* \* \* \* wget -O /dev/null -q "<http://www.page.tld/index.php?id=123>"**

Es wird nun aller 2 Minuten die Seite abgerufen. Wichtig ist, das hier index.php?id=[PAGE\_ID\_OR\_PAGE\_ALIAS] genutzt wird!

In jedem Fall muss sichergestellt werden das der Server die URL aufrufen kann.
