<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "mkmailer" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use Sys25\RnBase\Utility\Files;
use Sys25\RnBase\Utility\T3General;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Mail Factory.
 *
 * Ein MailJob kann in die MailQueue eingestellt werden
 * und wird zu einem späteren Zeitpunkt verarbeitet.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mail_Factory
{
    /**
     * Creates a mail job.
     *
     * @param array[tx_mkmailer_receiver_IMailReceiver] $receiver
     *
     * @return tx_mkmailer_mail_MailJob
     */
    public static function createMailJob(
        array $receiver = [],
        ?tx_mkmailer_models_Template &$templateObj = null,
    ): object {
        return GeneralUtility::makeInstance(
            'tx_mkmailer_mail_MailJob',
            $receiver,
            $templateObj
        );
    }

    /**
     * Erstellt ein Datei-Attachment. Wenn ein relativer Pfad übergeben wird,
     * dann wird dieser automatisch in einen absoluten TYPO3-Pfad umgewandelt.
     *
     * @param string $path
     * @param string $name
     * @param string $mimeType
     *
     * @return tx_mkmailer_mail_IAttachment
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    public static function createAttachment(
        $path,
        $name = '',
        string $encoding = 'base64',
        $mimeType = false,
    ): object {
        return self::createAttachmentInstance(
            tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
            self::makeAbsPath($path),
            $name,
            '',
            $encoding,
            $mimeType
        );
    }

    /**
     * Find the mimeType of a file ord its content.
     *
     * @param string $absPathOrContent
     *
     * @return string
     */
    protected static function getFileInfoMimeType($absPathOrContent)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = is_file($absPathOrContent) ? $finfo->file($absPathOrContent) : $finfo->buffer($absPathOrContent);

        if (false === $mimeType) {
            return 'application/octet-stream';
        }

        return $mimeType;
    }

    /**
     * Erstellt einen absoluten TYPO3-Pfad.
     *
     * @param string $path
     *
     * @return string
     */
    public static function makeAbsPath($path)
    {
        if (!PathUtility::isAbsolutePath($path)) {
            return Files::getFileAbsFileName(
                T3General::fixWindowsFilePath($path)
            );
        }

        return $path;
    }

    /**
     * Creates an Attachment by the content of the file.
     *
     * @param string $content
     * @param string $name
     * @param string $mimeType
     *
     * @return tx_mkmailer_mail_IAttachment
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    public static function createStringAttachment(
        $content,
        $name = '',
        string $encoding = 'base64',
        $mimeType = false,
    ): object {
        return self::createAttachmentInstance(
            tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
            $content,
            $name,
            '',
            $encoding,
            $mimeType
        );
    }

    /**
     * Creates an embedded attachment.
     *
     * Will be used for images etc, those will be shorn in de mail directly
     *
     * @param string $path
     * @param string $embedId  Content ID of the attachment.  Use this to identify
     * @param string $name
     * @param string $mimeType
     *
     * @return tx_mkmailer_mail_IAttachment
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    public static function createEmbeddedAttachment(
        $path,
        $embedId,
        $name = '',
        string $encoding = 'base64',
        $mimeType = false,
    ): object {
        return self::createAttachmentInstance(
            tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT,
            self::makeAbsPath($path),
            $name,
            $embedId,
            $encoding,
            $mimeType
        );
    }

    /**
     * Creates an instance of the attachment model.
     *
     * @param int    $type             One const tx_mkmailer_mail_IAttachment::TYPE_*
     * @param string $absPathOrContent
     * @param string $name
     * @param string $embedId
     * @param string $mimeType
     *
     * @return tx_mkmailer_mail_Attachment
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    private static function createAttachmentInstance(
        int $type,
        $absPathOrContent,
        $name = '',
        $embedId = '',
        string $encoding = 'base64',
        $mimeType = false,
    ): object {
        /* @var $attachment tx_mkmailer_mail_Attachment */
        $attachment = GeneralUtility::makeInstance(
            'tx_mkmailer_mail_Attachment',
            $type
        );

        $attachment->setPathOrContent($absPathOrContent);
        $attachment->setName($name);
        $attachment->setEmbedId($embedId);
        $attachment->setEncoding($encoding);

        if (false === $mimeType) {
            $mimeType = self::getFileInfoMimeType($absPathOrContent);
        }

        $attachment->setMimeType($mimeType);

        return $attachment;
    }

    /**
     * Creates an instance of an address model.
     *
     * @param string $address
     * @param string $name
     *
     * @return tx_mkmailer_mail_Address
     */
    public static function createAddressInstance(
        $address,
        $name = '',
    ): object {
        return GeneralUtility::makeInstance(
            'tx_mkmailer_mail_Address',
            $address,
            $name
        );
    }
}
