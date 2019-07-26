mkmailer
=======

[![Latest Stable Version](https://img.shields.io/packagist/v/dmk/mkmailer.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkmailer)
[![Total Downloads](https://img.shields.io/packagist/dt/dmk/mkmailer.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkmailer)
[![Build Status](https://img.shields.io/travis/DMKEBUSINESSGMBH/typo3-mkmailer.svg?maxAge=3600&style=flat-square)](https://travis-ci.org/DMKEBUSINESSGMBH/typo3-mkmailer)
[![License](https://img.shields.io/packagist/l/dmk/mkmailer.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkmailer)

Was macht die Extension?
------------------------

In einer Plattform müssen häufig E-Mails an verschiedene Nutzer geschickt werden. Bei kleineren Portalen ist das kein Problem. Wenn jedoch in kurzer Zeit sehr viele Mails verschickt werden, kommen die Server schnell an ihre Grenze. Aus diesem Grund werden z.b. die Newsletter von direct\_mail häppchenweise in kleinen Paketen von ca. 50 Mails pro Minute versandt.

Die Extension mkmailer bietet eine API in der man diesen asynchronen Versand auch für eigene Extensions nutzen kann. Man schickt die Mails damit nicht mehr selbst, sondern stellt sie zunächst in eine Queue. Außerdem verwendet die Extension die PHP-Bibliothek PHPMailer. Damit werden mit hoher Sicherheit valide Emails auf Reisen geschickt. Auch Multi-Mime-Mails mit Anhängen funktionieren.

[SendMails](Documentation/SendMails/Index.md)

[Receiver](Documentation/Receiver/Index.md)

[BEModule](Documentation/BEModule/Index.md)

[ChangeLog](Documentation/ChangeLog/Index.md)
