<?php

declare(strict_types=1);

return [
    'tx_mkmailer_actions_SendMails' => \DMK\MkMailer\Frontend\Action\SendMails::class,
    'tx_mkmailer_mail_IAddress' => \DMK\MkMailer\Mail\IMailAddress::class,
    'tx_mkmailer_mail_IAttachment' => \DMK\MkMailer\Mail\IAttachment::class,
    'tx_mkmailer_mail_Address' => \DMK\MkMailer\Mail\MailAddress::class,
    'tx_mkmailer_mail_Attachment' => \DMK\MkMailer\Mail\Attachment::class,
    'tx_mkmailer_mail_Factory' => \DMK\MkMailer\Mail\Factory::class,

    'tx_mkmailer_models_Log' => \DMK\MkMailer\Model\Log::class,
    'tx_mkmailer_models_Queue' => \DMK\MkMailer\Model\Queue::class,
    'tx_mkmailer_models_Template' => \DMK\MkMailer\Model\Template::class,
];
