mkmailer
=======

![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-6.2%20%7C%207.6%20%7C%208.7%20%7C%209.5-orange?maxAge=3600&style=flat-square&logo=typo3)
[![Latest Stable Version](https://img.shields.io/packagist/v/dmk/mkmailer.svg?maxAge=3600&style=flat-square&logo=composer)](https://packagist.org/packages/dmk/mkmailer)
[![Total Downloads](https://img.shields.io/packagist/dt/dmk/mkmailer.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkmailer)
[![Build Status](https://img.shields.io/github/workflow/status/DMKEBUSINESSGMBH/typo3-mkmailer/PHP-CI.svg?maxAge=3600&style=flat-square&logo=github-actions)](https://github.com/DMKEBUSINESSGMBH/typo3-mkmailer/actions?query=workflow%3APHP-CI)
[![License](https://img.shields.io/packagist/l/dmk/mkmailer.svg?maxAge=3600&style=flat-square&logo=gnu)](https://packagist.org/packages/dmk/mkmailer)

Was macht die Extension?
------------------------

In einer Plattform müssen häufig E-Mails an verschiedene Nutzer geschickt werden. Bei kleineren Portalen ist das kein Problem. Wenn jedoch in kurzer Zeit sehr viele Mails verschickt werden, kommen die Server schnell an ihre Grenze. Aus diesem Grund werden z.b. die Newsletter von direct\_mail häppchenweise in kleinen Paketen von ca. 50 Mails pro Minute versandt.

Die Extension mkmailer bietet eine API in der man diesen asynchronen Versand auch für eigene Extensions nutzen kann. Man schickt die Mails damit nicht mehr selbst, sondern stellt sie zunächst in eine Queue. Außerdem verwendet die Extension die PHP-Bibliothek PHPMailer. Damit werden mit hoher Sicherheit valide Emails auf Reisen geschickt. Auch Multi-Mime-Mails mit Anhängen funktionieren.

[SendMails](Documentation/SendMails/Index.md)

[Receiver](Documentation/Receiver/Index.md)

[BEModule](Documentation/BEModule/Index.md)

[ChangeLog](Documentation/ChangeLog/Index.md)
