.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.




.. _changelog:

ChangeLog
=========

The following is a very high level overview of the changes in this extension.

.. tabularcolumns:: |r|p{13.7cm}|

=========  ===========================================================================
Version    Changes
=========  ===========================================================================
2.0.0      [REFACTORING] tx_mkmailer_util_Mails::sendModelReceiverMail ist nicht mehr statisch. Bitte Aufrufe entsprechend refactoren damit diese nicht statisch auf eine Instanzvariable gehen.
           [BUGFIX] Zeige default Labels beim Wizicon an falls Label nicht in der Sprachdatei
           [BUGFIX] Upload Ordner bei bedarf erstellen
1.0.9      [BUGFIX] den Trenner von E-Mail und Model UID in tx_mkmailer_receiver_Model gefixed(beim Update sicherstellen dass die mkmailer Warteschlange leer ist, um Fehler beim Versand zu verhindern)
=========  ===========================================================================
