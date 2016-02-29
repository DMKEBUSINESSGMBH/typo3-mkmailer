<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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
 ***************************************************************/

tx_rnbase::load('tx_mklib_scheduler_GenericFieldProvider');

/**
 * Send-Mails scheduler task
 *
 * @package TYPO3
 * @subpackage mkmailer
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_scheduler_SendMailsFieldProvider
	extends tx_mklib_scheduler_GenericFieldProvider
{
	/**
	 * Returns the option fields for the SendMails scheduler
	 *
	 * @return 	array
	 */
	protected function getAdditionalFieldConfig(){
		return array(
			'cronpage' => array(
				'type' => 'input',
				'label' => 'PID of the sendmail path. The ExtConf will be used by default',
				'default' => '0',
				'eval' => 'trim',
			),
			'user' => array(
				'type' => 'input',
				'label' => 'Username for basic authentication',
				'default' => '',
				'eval' => 'trim',
			),
			'passwd' => array(
				'type' => 'input',
				'label' => 'Password for basic authentication',
				'default' => '',
				'eval' => 'trim',
			),
		);
	}
}
