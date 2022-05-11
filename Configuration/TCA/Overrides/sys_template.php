<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

\Sys25\RnBase\Utility\Extensions::addStaticFile('mkmailer', 'static/ts/', 'MK Mailer');
