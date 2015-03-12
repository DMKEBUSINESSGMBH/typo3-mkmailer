.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

Einleitung
==========


.. _what-it-does:

Was macht die Extension?
------------------------

In einer Plattform müssen häufig E-Mails an verschiedene Nutzer geschickt werden.
Bei kleineren Portalen ist das kein Problem. Wenn jedoch in kurzer Zeit sehr
viele Mails verschickt werden, kommen die Server schnell an ihre Grenze.
Aus diesem Grund werden z.b. die Newsletter von direct_mail häppchenweise in
kleinen Paketen von ca. 50 Mails pro Minute versandt.

Die Extension mkmailer bietet eine API in der man diesen
asynchronen Versand auch für eigene Extensions nutzen kann.
Man schickt die Mails damit nicht mehr selbst, sondern stellt sie zunächst in eine
Queue. Außerdem verwendet die Extension die PHP-Bibliothek PHPMailer.
Damit werden mit hoher Sicherheit valide Emails auf Reisen geschickt.
Auch Multi-Mime-Mails mit Anhängen funktionieren.