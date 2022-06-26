<?php

namespace DMK\MkMailer\Mail;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 DMK E-BUSINESS GmbH (dev@dmk-ebusiness.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * tx_mkmailer_mail_Attachment.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class Attachment implements IAttachment
{
    /**
     * @var int
     */
    private $type = IAttachment::TYPE_ATTACHMENT;

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

    /**
     * @var string
     */
    private $mimeType = 'application/octet-stream';

    /**
     * @var string
     */
    private $encoding = 'base64';

    /**
     * @param int $type @see tx_mkmailer_mail_IAttachment::TYPE_ATTACHMENT
     */
    public function __construct($type)
    {
        $this->setAttachmentType($type);
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getPathOrContent()
     */
    public function getPathOrContent()
    {
        return $this->pathOrContent;
    }

    /**
     * @param string $pathOrContent
     */
    public function setPathOrContent($pathOrContent)
    {
        $this->pathOrContent = $pathOrContent;
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getEmbedId()
     */
    public function getEmbedId()
    {
        return $this->embedId;
    }

    /**
     * @param string $embedId
     */
    public function setEmbedId($embedId)
    {
        $this->embedId = $embedId;
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getMimeType()
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getEncoding()
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * (non-PHPdoc).
     *
     * @see IAttachment::getAttachmentType()
     */
    public function getAttachmentType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setAttachmentType($type)
    {
        $this->type = $type;
    }
}
