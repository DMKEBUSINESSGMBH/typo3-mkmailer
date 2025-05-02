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

/**
 * tx_mkmailer_mail_Attachment.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mail_Attachment implements tx_mkmailer_mail_IAttachment
{
    private int $type = tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT;

    /**
     * @var string
     */
    private $pathOrContent;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $embedId;

    private string $mimeType = 'application/octet-stream';

    private string $encoding = 'base64';

    /**
     * @param int $type @see tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT
     */
    public function __construct(int $type)
    {
        $this->setAttachmentType($type);
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getPathOrContent()
     */
    public function getPathOrContent()
    {
        return $this->pathOrContent;
    }

    /**
     * @param string $pathOrContent
     */
    public function setPathOrContent($pathOrContent): void
    {
        $this->pathOrContent = $pathOrContent;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getEmbedId()
     */
    public function getEmbedId()
    {
        return $this->embedId;
    }

    /**
     * @param string $embedId
     */
    public function setEmbedId($embedId): void
    {
        $this->embedId = $embedId;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getMimeType()
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getEncoding()
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_mail_IAttachment::getAttachmentType()
     */
    public function getAttachmentType(): int
    {
        return $this->type;
    }

    public function setAttachmentType(int $type): void
    {
        $this->type = $type;
    }
}
