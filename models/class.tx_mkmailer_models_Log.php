<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2017 Dorit Wittig (dorit.wittig@dmk-ebusiness.de)
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
tx_rnbase::load('tx_rnbase_util_Strings');
tx_rnbase::load('tx_rnbase_model_base');

/**
 * tx_mkmailer_models_Queue
 *
 * Model für einen Datensatz der Tabelle tx_mkmailer_queue.
 * Achtung: Für diese Tabelle existiert kein TCA-Eintrag!
 *
 * @package         TYPO3
 * @subpackage      mkmailer
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_models_Log extends tx_rnbase_model_base
{

    /**
     * (non-PHPdoc)
     * @see tx_rnbase_model_base::getTableName()
     */
    public function getTableName()
    {
        return 'tx_mkmailer_log';
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->record['receiver'];
    }

    /**
     * Liefert die Receiver dieser Mail als Array
     *
     * @return array
     */
    public function getReceiverMail()
    {
        $what = '*';
        $from = 'tx_mkmailer_receiver';

        $options['where'] = 'email=' . $this->record['receiver'];
        $options['enablefieldsoff'] = 1;
        $return = tx_rnbase_util_DB::doSelect($what, $from, $options, 0);

        foreach ($return as $r) {
            $info = $r['receivers'];

            $ret[] = $info;
        }

        return implode('<br />', $ret);
    }

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Queue.php']) {
    include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/models/class.tx_mkmailer_models_Queue.php']);
}
