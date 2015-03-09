<?php
/**
 * @package tx_mkmailer
 * @subpackage tx_mkmailer_exceptions
 *
 * Copyright notice
 *
 * (c) 2013 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
require_once t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php');

/**
 *
 * tx_mkmailer_exceptions_NoTemplateFound
 *
 * No template found exception
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @author 			Michael Wagner <dev@dmk-ebusiness.de>
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_exceptions_NoTemplateFound extends Exception {

	/**
	 *
	 * @param string $message
	 * @param string $code
	 * @param string $previous
	 */
	public function __construct($message = null, $code = null, $previous = null) {
		if (!$message) {
			$message = 'No mail template found!';
		}
		parent::__construct($message, $code, $previous);
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/exceptions/class.tx_mkmailer_exceptions_NoTemplateFound.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/exceptions/class.tx_mkmailer_exceptions_NoTemplateFound.php']);
}