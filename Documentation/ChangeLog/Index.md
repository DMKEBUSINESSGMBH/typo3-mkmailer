ChangeLog
=========

The following is a very high level overview of the changes in this extension.

| Version | Changes                                                                                                                                                                                          |
|---------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 11.0.1  | bugfix for BE module since rn_base 1.16.7                                                                                                                                                        |
| 11.0.0  | added support for TYPO3 10.4 and 11.5 and PHP 7.4 and 8.0 only                                                                                                                                   |
| 9.0.0   | add support for TYPO3 9.5                                                                                                                                                                        |
| 3.0.10  | [SECURITY] Security fix. updated PHPMailer to 5.2.27                                                                                                                                             |
| 3.0.9   | stop mailing process when no lock could be obtained                                                                                                                                              |
| 3.0.8   | add autoload for older TYPO3 versions                                                                                                                                                            |
| 3.0.7   | bugfix for pager in backend module                                                                                                                                                               |
| 3.0.6   | minor bugfix                                                                                                                                                                                     |
| 3.0.5   | add php 7 compatibility                                                                                                                                                                          |
| 3.0.4   | management of failed mails is now possible                                                                                                                                                       |
| 3.0.3   | bugfix when showing queue status                                                                                                                                                                 |
| 3.0.2   | added view for failed mails in backend module                                                                                                                                                    |
|         | backend module is moved to the web module                                                                                                                                                        |
|         | the configured cronpage can be a string now to use aliases                                                                                                                                       |
|         | any information about queue status only dispalyed for devIp                                                                                                                                      |
| 3.0.1   | bugfix for softref of images                                                                                                                                                                     |
| 3.0.0   | Initial TYPO3 8.7 LTS Support                                                                                                                                                                    |
| 2.0.5   | added softref configuration to TCA                                                                                                                                                               |
| 2.0.4   | source out marker array parsing to own method in base template receiver                                                                                                                          |
|         | confid added to substituteMarkerArray in tx_mkmailer_receiver_BaseTemplate                                                                                                                       |
| 2.0.3   | converted documentation from reSt to markdown                                                                                                                                                    |
| 2.0.2   | [BUGFIX] added renderType for select fields in flexform.xml                                                                                                                                      |
| 2.0.1   | [BUGFIX] fixed possible sql injections                                                                                                                                                           |
| 2.0.0   | [FEATURE] added support for TYPO3 7.6                                                                                                                                                            |
| 1.0.11  | [FEATURE] cronpage, user and pwd configuration added to sendmails scheduler                                                                                                                      |
| 1.0.10  | [REFACTORING] tx\_mkmailer\_util\_Mails::sendModelReceiverMail ist nicht mehr statisch. Bitte Aufrufe entsprechend refactoren damit diese nicht statisch auf eine Instanzvariable gehen.         |
|         | [BUGFIX] Zeige default Labels beim Wizicon an falls Label nicht in der Sprachdatei                                                                                                               |
|         | [BUGFIX] Upload Ordner bei bedarf erstellen                                                                                                                                                      |
| 1.0.9   | [BUGFIX] den Trenner von E-Mail und Model UID in tx\_mkmailer\_receiver\_Model gefixed(beim Update sicherstellen dass die mkmailer Warteschlange leer ist, um Fehler beim Versand zu verhindern) |


