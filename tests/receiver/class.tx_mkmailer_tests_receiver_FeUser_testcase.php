<?php
/**
 * @package Tx_Mkappelrath
 * @subpackage Tx_Mkappelrath_tests
 *
 * Copyright notice
 *
 * (c) 2013 das MedienKombinat GmbH <kontakt@das-medienkombinat.de>
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

require_once(t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php'));

// wir laden alles vorher, wegen den memory leaks
tx_rnbase::load('tx_rnbase_configurations');
tx_rnbase::load('tx_mkmailer_models_Queue');
tx_rnbase::load('tx_rnbase_util_Files');

/**
 * Tests zum E-Mail-Versand.
 *
 * @package Tx_Mkappelrath
 * @subpackage Tx_Mkappelrath_test
 * @author Michael Wagner <michael.wagner@das-medienkombinat.de>
 */
class tx_mkmailer_tests_receiver_FeUser_testcase extends Tx_Phpunit_TestCase {

	/**
	 * Constructs a test case with the given name.
	 *
	 * @param  string $name
	 * @param  array  $data
	 * @param  string $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name,$data, $dataName);
		tx_rnbase::load('tx_rnbase_util_Misc');
		tx_rnbase_util_Misc::prepareTSFE(array('force'=>true));
	}

	protected function setUp(){
		if (!t3lib_extMgm::isLoaded('t3users')) {
			$this->markTestSkipped('t3users ist nicht geladen');
		}

		// bei älteren t3 versionen ist der backpath falsch!
		$this->getFileName_backPath = $GLOBALS['TSFE']->tmpl->getFileName_backPath;
		$GLOBALS['TSFE']->tmpl->getFileName_backPath =
			$GLOBALS['TSFE']->tmpl->getFileName_backPath ?
			$GLOBALS['TSFE']->tmpl->getFileName_backPath : PATH_site;
		return parent::setUp();
	}
	protected function tearDown () {
		// backpath zurücksetzen
		$GLOBALS['TSFE']->tmpl->getFileName_backPath = $this->getFileName_backPath;
		return parent::tearDown();
	}


	/**
	 * @return tx_rnbase_configurations
	 */
	private function getConfigurations($confId, array $configArray = array()){
		$configArray = array($confId => $configArray);

		$configurations = tx_rnbase::makeInstance('tx_rnbase_configurations');
		$configurations->init(
				$configArray,
				$configurations->getCObj(1),
				'mkmailer', 'mkmailer' // die mails werden
		);
		return $configurations;
	}

	/**
	 * @return Tx_Mkappelrath_Receiver_Article
	 */
	private function getReceiver(
		tx_rnbase_model_base $feuser
	){
		$receiver = tx_rnbase::makeInstance(
			'tx_mkmailer_receiver_FeUser'
		);
		$receiver->setFeUser($feuser);
		return $receiver;
	}

	/**
	 *
	 * @param string $sFileName
	 * @return array
	 */
	protected function getTemplates($sFileName){
		$data = array();
		$template = @file_get_contents($sFileName);
		$data['contenttext'] = trim(t3lib_parsehtml::getSubpart($template, '###CONTENTTEXT###'));
		$data['contenthtml'] = trim(t3lib_parsehtml::getSubpart($template, '###CONTENTHTML###'));
		$data['subject'] = trim(t3lib_parsehtml::getSubpart($template, '###CONTENTSUBJECT###'));
		$data['resulttext'] = trim(t3lib_parsehtml::getSubpart($template, '###RESULTTEXT###'));
		$data['resulthtml'] = trim(t3lib_parsehtml::getSubpart($template, '###RESULTHTML###'));
		$data['resultsubject'] = trim(t3lib_parsehtml::getSubpart($template, '###RESULTSUBJECT###'));
		return $data;
	}

	/**
	 * @return 	tx_mkmailer_receiver_BaseTemplate
	 */
	private function getQueue(array $data = array()){
		// wir könnten die queue auch aus der datenbank holen, das template müsste allerdings dort vorhanden sein!
		$data['uid'] = $data['uid'] ? $data['uid'] : 0;

		$data['contenttext'] = $data['contenttext'] ? $data['contenttext'] : 'contenttext';
		$data['contenthtml'] = $data['contenthtml'] ? $data['contenthtml'] : 'contenthtml';
		$data['subject'] = $data['subject'] ? $data['subject'] : 'subject';
		return tx_rnbase::makeInstance('tx_mkmailer_models_Queue', $data);
	}

	/* *** ********************** *** *
	 * *** Die eigentlichen Tests *** *
	 * *** ********************** *** */

	public function testGetSingleMailForFeUser() {

		$configurations = $this->getConfigurations(
			$confId = 'sendmails.',
			array(
				'feuserTemplate' => 'EXT:mkmailer/tests/fixtures/mailfeuser.html'
			)
		);
		$feuser = tx_rnbase::makeInstance('tx_t3users_models_feuser', array(
			'uid'	=> '1',
			'email'	=> 'test@localhost.net',
		));
		$templates = $this->getTemplates(t3lib_extMgm::extPath('mkmailer', 'tests/fixtures/mailfeuser.html'));

		$receiver = $this->getReceiver($feuser);
		$queue = $this->getQueue($templates);
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());
		$subject = ($msg->getSubject());

		$this->assertEquals($templates['resulttext'], $contentText, 'Wrong content text.');
		$this->assertEquals($templates['resulthtml'], $contentHtml, 'Wrong content html.');
		$this->assertEquals($templates['resultsubject'], $subject, 'Wrong content html.');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/receiver/class.tx_mkmailer_tests_receiver_FeUser_testcase.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/receiver/class.tx_mkmailer_tests_receiver_FeUser_testcase.php']);
}