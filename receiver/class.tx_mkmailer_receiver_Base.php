<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Rene Nitzsche (rene@system25.de)
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
 * tx_mkmailer_receiver_Base.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
abstract class tx_mkmailer_receiver_Base implements tx_mkmailer_receiver_IMailReceiver
{
    protected $obj;

    /**
     * @return string
     */
    public function __toString()
    {
        $out = get_class($this)."\n\nObject:\n";
        $out .= is_object($this->obj) ? get_class($this->obj) : '-';
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
        return is_object($this->obj) ? $this->obj->uid : '';
    }
}
