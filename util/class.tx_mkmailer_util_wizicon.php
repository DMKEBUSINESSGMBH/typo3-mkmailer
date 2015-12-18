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
 *
 * tx_mkmailer_util_wizicon
 *
 * @package        TYPO3
 * @subpackage     mkmailer
 * @author         René Nitzsche <dev@dmk-ebusiness.de>
 * @author         Eric Hertwig <dev@dmk-ebusiness.de>
 * @license        http://www.gnu.org/licenses/lgpl.html
 *                 GNU Lesser General Public License, version 3 or later
 */

tx_rnbase::load( 'tx_rnbase_util_Extensions' );
tx_rnbase::load( 'tx_rnbase_util_Wizicon' );

class tx_mkmailer_util_wizicon extends tx_rnbase_util_Wizicon {

	/**
	 * @return array
	 */
	protected function getPluginData() {
		return array(
			'plugins_tx_mksearch' => array(
				'icon'        => tx_rnbase_util_Extensions::extRelPath( 'mkmailer' ) . 'ext_icon.gif',
				'title'       => 'plugin.mkmailer.label',
				'description' => 'plugin.mkmailer.description'
			)
		);
	}

	/**
	 * @return string
	 */
	protected function getLLFile() {
		return tx_rnbase_util_Extensions::extPath( 'mkmailer' ) . 'locallang_db.xml';
	}
}

if ( defined( 'TYPO3_MODE' ) && $GLOBALS['TYPO3_CONF_VARS'][ TYPO3_MODE ]['XCLASS']['ext/mkmailer/util/class.tx_mkmailer_util_wizicon.php'] ) {
	include_once( $GLOBALS['TYPO3_CONF_VARS'][ TYPO3_MODE ]['XCLASS']['ext/mkmailer/util/class.tx_mkmailer_util_wizicon.php'] );
}
