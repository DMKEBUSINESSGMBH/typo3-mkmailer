<?php
/**
 * 	@package TYPO3
 *  @subpackage tx_mkmailer
 *  @author Hannes Bochmann
 *
 *  Copyright notice
 *
 *  (c) 2014 Hannes Bochmann <hannes.bochmann@das-medienkombinat.de>
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
 */
tx_rnbase::load('tx_mkmailer_receiver_Email');

/**
 * generische klasse um ein model zu versenden. es müssen nur die die abstrakten methoden
 * bereitgestellt werden und wenn gewünscht noch getConfId überschrieben werden.
 *
 * diese verlangt nur eine email adresse und die model id im constructor
 *
 * @author Hannes Bochmann <hannes.bochmann@dmk-business.de>
 */
abstract class tx_mkmailer_receiver_Model extends tx_mkmailer_receiver_Email {

	/**
	 * @var int
	 */
	protected $modelUid;

	/**
	 *
	 * @param string $email
	 * @param int $ratingUid
	 */
	public function __construct($email = null, $modelUid = null){
		parent::tx_mkmailer_receiver_Email($email);
		$this->setModelUid($modelUid);
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_mkmailer_receiver_Email::setValueString()
	 */
	public function setValueString($valueString) {
		$valueParts = t3lib_div::trimExplode('_', $valueString);
		$this->setEMail($valueParts[0]);
		$this->setModelUid($valueParts[1]);
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_mkmailer_receiver_Email::getValueString()
	 */
	public function getValueString() {
		return $this->getEMail() . '_' . $this->getModelUid();
	}

	/**
	 * @param int $rating
	 */
	public function setModelUid($modelUid) {
		$this->modelUid = intval($modelUid);
	}

	/**
	 * @return int
	 */
	public function getModelUid() {
		return intval($this->modelUid);
	}

	/**
	 * (non-PHPdoc)
	 * @see tx_mkmailer_receiver_BaseTemplate::addAdditionalData()
	 */
	protected function addAdditionalData(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx) {
		$markerClass = tx_rnbase::makeInstance($this->getMarkerClass());
		$model = $this->getModel();
		$modelMarker = $this->getModelMarker();

		$mailText = $markerClass->parseTemplate(
			$mailText, $model, $formatter, $confId, $modelMarker
		);
		$mailHtml = $markerClass->parseTemplate(
			$mailHtml, $model, $formatter, $confId, $modelMarker
		);
		$mailSubject = $markerClass->parseTemplate(
			$mailSubject, $model, $formatter, $confId, $modelMarker
		);
	}

	/**
	 * @return tx_rnbase_model_base
	 */
	abstract protected function getModel();

	/**
	 * der Marker im Template für das model
	 *
	 * @return string
	 */
	abstract protected function getModelMarker();

	/**
	 * @return tx_rnbase_util_SimpleMarker
	 */
	abstract protected function getMarkerClass();
}