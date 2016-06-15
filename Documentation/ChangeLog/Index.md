ChangeLog
=========

The following is a very high level overview of the changes in this extension.

|Version|Changes|
|-------|-------|
|2.0.5|added softref configuration to TCA|
|2.0.4|source out marker array parsing to own method in base template receiver|
||confid added to substituteMarkerArray in tx_mkmailer_receiver_BaseTemplate|
|2.0.3|converted documentation from reSt to markdown|
|2.0.2|[BUGFIX] added renderType for select fields in flexform.xml|
|2.0.1|[BUGFIX] fixed possible sql injections|
|2.0.0|[FEATURE] added support for TYPO3 7.6|
|1.0.11|[FEATURE] cronpage, user and pwd configuration added to sendmails scheduler|
|1.0.10|[REFACTORING] tx\_mkmailer\_util\_Mails::sendModelReceiverMail ist nicht mehr statisch. Bitte Aufrufe entsprechend refactoren damit diese nicht statisch auf eine Instanzvariable gehen.|
||[BUGFIX] Zeige default Labels beim Wizicon an falls Label nicht in der Sprachdatei|
||[BUGFIX] Upload Ordner bei bedarf erstellen|
|1.0.9|[BUGFIX] den Trenner von E-Mail und Model UID in tx\_mkmailer\_receiver\_Model gefixed(beim Update sicherstellen dass die mkmailer Warteschlange leer ist, um Fehler beim Versand zu verhindern)|


