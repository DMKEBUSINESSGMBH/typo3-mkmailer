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

abstract class tx_mkmailer_receiver_Base implements tx_mkmailer_receiver_IMailReceiver, Stringable
{
    protected $obj;

    public function __toString(): string
    {
        $out = static::class."\n\nObject:\n";
        $out .= is_object($this->obj) ? $this->obj::class : '-';
        $out .= "\n\nAddresses:\n";
        $addrs = $this->getAddresses();
        for ($i = 0, $cnt = count($addrs); $i < $cnt; ++$i) {
            $out .= "\n".$addrs[$i];
        }

        return $out;
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_mkmailer_receiver_IMailReceiver::getValueString()
     */
    public function getValueString()
    {
        return is_object($this->obj) ? $this->obj->getUid() : '';
    }
}
