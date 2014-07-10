<?php
/**
 *  Copyright notice
 *
 *  (c) 2011 das MedienKombinat <kontakt@das-medienkombinat.de>
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
require_once(t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php'));
tx_rnbase::load('tx_mkmailer_receiver_BaseTemplate');


/**
 *	Test Receiver Object
 */
class tx_mkmailer_tests_receiver_BaseTemplate extends tx_mkmailer_receiver_BaseTemplate {
	public $addAdditionalData = false;
	function getConfId() { return 'basetemplate.'; }
	function getAddressCount() {}
	function getAddresses() {}
	function getName() {}
	function getSingleAddress($idx) { return array('address' => 'ich@da.com', 'addressid' => 'ich@da.com'); }
//	function getSingleMail($queue, &$formatter, $confId, $idx) {}
	function getValueString() {}
	function setValueString($value) {}
	protected function addAdditionalData(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx) {
		if($this->addAdditionalData) {
			$mailText .= 'addAdditionalData';
			$mailHtml .= 'addAdditionalData';
		}
	}
}

/**
 *	Test Receiver Object mit email variable
 */
class tx_mkmailer_tests_receiver_BaseTemplateWithEmailObjectVariable extends tx_mkmailer_tests_receiver_BaseTemplate {
	protected $email = 'john@doe.com';
}

/**
 *
 */
class tx_mkmailer_tests_receiver_BaseTemplate_testcase
	extends tx_phpunit_testcase {

    /**
     * Constructs a test case with the given name.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name,$data, $dataName);
        tx_rnbase::load('tx_mklib_tests_Util');
		tx_mklib_tests_Util::prepareTSFE(array('force'=>true));
    }


	protected $getFileName_backPath = '';
	protected function setUp(){
		// bei älteren t3 versionen ist der backpath falsch!
		$GLOBALS['TSFE']->tmpl->getFileName_backPath =
			$GLOBALS['TSFE']->tmpl->getFileName_backPath ?
			$GLOBALS['TSFE']->tmpl->getFileName_backPath : PATH_site;
	}
	protected function tearDown () {
		// backpath zurücksetzen
		$GLOBALS['TSFE']->tmpl->getFileName_backPath = $this->getFileName_backPath;
	}
	/**
	 * @return 	tx_rnbase_configurations
	 */
	private function getConfigurations(array $configArray = array()){
		$configurations = tx_rnbase::makeInstance('tx_rnbase_configurations');
		$configArray = array('sendmails.' => $configArray);

		$configurations->init(
				$configArray,
				$configurations->getCObj(1),
				'mkmailer', 'mkmailer'
			);
		return $configurations;
	}

	/**
	 * @param string $class
	 *
	 * @return 	tx_mkmailer_tests_receiver_BaseTemplate
	 */
	private function getReceiver($class = 'tx_mkmailer_tests_receiver_BaseTemplate'){
		return tx_rnbase::makeInstance($class);
	}

	/**
	 * @return 	tx_mkmailer_receiver_BaseTemplate
	 */
	private function getQueue(array $data = array()){
		$data['uid'] = $data['uid'] ? $data['uid'] : 0;
		$data['contenttext'] = $data['contenttext'] ? $data['contenttext'] : 'Text für TEXT<br />';
		$data['contenthtml'] = $data['contenthtml'] ? $data['contenthtml'] : 'Text für HTML<br />';
		$data['subject'] = $data['subject'] ? $data['subject'] : 'Subject';
		return tx_rnbase::makeInstance('tx_mkmailer_models_Queue', $data);
	}

	public function testGetSingleMailWithoutWrap() {
		$confId = 'sendmails.';
		$configurations = $this->getConfigurations();
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('Text für HTML<br />', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('Text für TEXT', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrongTemplate() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplate.' => array(
						'wrapTemplate' => '1',
						'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/wrongtext.html',
						'textTemplate' => 'EXT:mkmailer/tests/fixtures/wronghtml.html',
					),
			);
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('<!-- TEMPLATE NOT FOUND: EXT:mkmailer/tests/fixtures/wrongtext.html -->Text für HTML<br />', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('Text für TEXT', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrappedTemplate() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplate.' => array(
						'wrapTemplate' => '1',
						'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
						'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
					),
			);
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('TEXTTEMPLATEText für TEXT', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrappedTemplateAndAdditionalData() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplate.' => array(
						'wrapTemplate' => '1',
						'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
						'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
					),
			);
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$receiver->addAdditionalData = true;
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>addAdditionalData', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".'addAdditionalData', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrappedDefaultTemplateAndAdditionalData() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplateTemplate' => 'EXT:mkmailer/tests/fixtures/mailwrap.html',
				'basetemplate.' => array(
						'wrapTemplate' => '1',
					),
			);
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$receiver->addAdditionalData = true;
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html>addAdditionalData', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".'addAdditionalData', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrappedDefaultTemplateAndCustomSubpart() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplateTemplate' => 'EXT:mkmailer/tests/fixtures/mailwrap.html',
				'basetemplate.' => array(
					'wrapTemplate' => '1',
					'textSubpart' => '###TESTTEXT###',
					'htmlSubpart' => '###TESTHTML###',
				),
			);
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('HTMLTESTTEMPLATE<html>Text für HTML<br /></html>', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('TEXTTESTTEMPLATEText für TEXT', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailWithWrappedTemplateAndDcMarker() {
		$confId = 'sendmails.';
		$configArray = array(
				'basetemplate.' => array(
						'wrapTemplate' => '1',
						'textTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraptext.html',
						'textSubpart' => '###CONTENTTEXT_DCMARKER###',
						'htmlTemplate' => 'EXT:mkmailer/tests/fixtures/mailwraphtml.html',
						'htmlSubpart' => '###CONTENTHTML_DCMARKER###',
						'receivertext.' => array(),
					),
			);
		$configArray['basetemplate.']['receivertext.']['dctest']
			= $configArray['basetemplate.']['receiverhtml.']['dctest']
				= 'TEXT';
		$configArray['basetemplate.']['receivertext.']['dctest.']['value']
			= $configArray['basetemplate.']['receiverhtml.']['dctest.']['value']
				= 'Hallo Welt';
		$configurations = $this->getConfigurations($configArray);
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$contentHtml = ($msg->getHtmlPart());
		$contentText = ($msg->getTxtPart());

		$this->assertEquals('HTMLTEMPLATE<html>Text für HTML<br /></html> ich@da.com ich@da.com Hallo Welt', $contentHtml, 'HTML part wrong.');
		$this->assertEquals('TEXTTEMPLATEText für TEXT'."\r\n".' ich@da.com ich@da.com Hallo Welt', $contentText, 'TEXT part wrong.');
	}

	public function testGetSingleMailUsesGetSingleAdressIfObjectVariableEmailNotSet() {
		$confId = 'sendmails.';
		$configurations = $this->getConfigurations();
		$receiver = $this->getReceiver();
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$expectedTo = tx_rnbase::makeInstance('tx_mkmailer_mail_Address', 'ich@da.com', '');
		$this->assertEquals(array($expectedTo), $msg->getTo(), 'to wrong.');
	}

	public function testGetSingleMailUsesObjectVariableEmailIfSet() {
		$confId = 'sendmails.';
		$configurations = $this->getConfigurations();
		$receiver = $this->getReceiver('tx_mkmailer_tests_receiver_BaseTemplateWithEmailObjectVariable');
		$queue = $this->getQueue();
		$msg = $receiver->getSingleMail($queue, $configurations->getFormatter(), $confId, 0);

		$expectedTo = tx_rnbase::makeInstance('tx_mkmailer_mail_Address', 'john@doe.com', '');
		$this->assertEquals(array($expectedTo), $msg->getTo(), 'to wrong.');
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/receiver/class.tx_mkmailer_tests_receiver_BaseTemplate_testcase.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/tests/receiver/class.tx_mkmailer_tests_receiver_BaseTemplate_testcase.php']);
}