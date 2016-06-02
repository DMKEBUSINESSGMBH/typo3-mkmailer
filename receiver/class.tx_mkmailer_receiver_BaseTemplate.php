<?php
/**
 *  Copyright notice
 *
 *  (c) 2011 DMK E-BUSINESS <dev@dmk-ebusiness.de>
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
tx_rnbase::load('tx_rnbase_util_Templates');
tx_rnbase::load('tx_mkmailer_receiver_Base');

/**
 *
 * tx_mkmailer_receiver_BaseTemplate
 *
 * Basisklasse für Receiver.
 * Um den jeweiligen Inhalt wird ein Template gemappt, falls konfiguriert.
 *
 * Muss noch um die Methoden des Interfaces tx_mkmailer_receiver_IMailReceiver erweitert werden.
 * getAddressCount, getAddresses, getName, getSingleAddress, setValueString
 *
 * @package 		TYPO3
 * @subpackage	 	mkmailer
 * @author 			Michael Wagner <dev@dmk-ebusiness.de>
 * @license 		http://www.gnu.org/licenses/lgpl.html
 * 					GNU Lesser General Public License, version 3 or later
 */
abstract class tx_mkmailer_receiver_BaseTemplate
	extends tx_mkmailer_receiver_Base {

	/**
	 * Liefert die ConfId für den Reciver.
	 * Sollte überschrieben werden!
	 * Der Quatsch mit der Klasse ist nur Fallback!
	 *
	 * @return 	string
	 */
	protected function getConfId() {
		$confId = explode('_', get_class($this));
		$confId = array_pop($confId);
		return strtolower($confId).'.';
	}


	/**
	 * @TODO: die original confid wird noch gebraucht -> sendmails.
	 *
	 * @param 	tx_rnbase_configurations 	$configurations
	 * @param 	string 						$confId
	 * @param 	string 						$type
	 * @param 	string 						$config
	 * @return 	string
	 */
	protected function getConfig($configurations, $confId, $type, $config){
		// wird benötigt um template und subpart vom default auszulesen
		$confIdNoDot = strpos($confId,'.') !== false ? substr($confId, 0, -1) : '';

		$ret = $configurations->get($confId.$type.$config);
		$ret = $ret ? $ret : $configurations->get($confIdNoDot.$config);
		$ret = $ret ? $ret : $configurations->get('sendmails.basetemplate.'.$type.$config);
		$ret = $ret ? $ret : $configurations->get('sendmails.basetemplate'.$config);
		return $ret;
	}

	/**
	 * Wrapt ein Template um den Inhalt
	 *
	 * @param 	string 						$content
	 * @param 	tx_rnbase_configurations 	$configurations
	 * @param 	string 						$confId
	 * @param 	string 						$type
	 * @param 	int 						$idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return 	string
	 */
	protected function parseTemplate($content, $configurations, $confId, $type, $idx = 0) {
		if(empty($content) || !$configurations->getBool($confId.'wrapTemplate')) {
			return $content;
		}

		/* *** Template auslesen *** */
		$templatePath = $this->getConfig($configurations, $confId, $type, 'Template');

		if(!$templatePath) {
			return '<!-- NO Template defined. -->'.$content;
		}

		tx_rnbase::load('tx_rnbase_util_Files');
		$template = tx_rnbase_util_Files::getFileResource($templatePath);
		if(!$template) {
			return '<!-- TEMPLATE NOT FOUND: '.$templatePath.' -->'.$content;
		}

		/* *** Subpart auslesen *** */
		$subpart = $this->getConfig($configurations, $confId, $type, 'Subpart');
		$subpart = $subpart ? $subpart : '###CONTENT'.strtoupper($type).'###';
		$template = tx_rnbase_util_Templates::getSubpart($template,$subpart);

		if(!$template) {
			return '<!-- SUBPART NOT FOUND: '.$subpart.' -->'.$content;
		}

		tx_rnbase::load('tx_rnbase_util_Templates');
		$out = tx_rnbase_util_Templates::substituteMarkerArrayCached(
			$template, array('###CONTENT###' => $content)
		);

		return trim($out);
	}

	/**
	 * Parst den Receiver ein Template um den Inhalt
	 *
	 * @param 	string 						$content
	 * @param 	tx_rnbase_configurations 	$configurations
	 * @param 	string 						$confId
	 * @param 	string 						$type
	 * @param 	int 						$idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return 	string
	 */
	protected function parseReceiver($content, $configurations, $confId, $type, $idx = 0) {
		tx_rnbase::load('tx_rnbase_util_BaseMarker');

		$out = $content;

		// jetzt noch dcmarker und labels ersetzen.
		$markerArray = $subpartArray = $wrappedSubpartArray = $params = array();
		$formatter = $configurations->getFormatter();

		if (tx_rnbase_util_BaseMarker::containsMarker($out, 'RECEIVER_')) {
			// receiver und dcmarker auslesen
			$markerArray = $formatter->getItemMarkerArrayWrapped(
				$this->getReceiverRecord($idx),
				$confId . 'receiver' . $type . '.',
				0,
				'RECEIVER_'
			);
		}

		$out = $this->substituteMarkerArray(
			$out,
			$markerArray,
			$subpartArray,
			$wrappedSubpartArray,
			$params,
			$formatter,
			$confId
		);

		return trim($out);
	}

	/**
	 * Calls modul subparts, module markers and substitutes the marker arrays.
	 *
	 * @param string $template
	 * @param array $markerArray
	 * @param array $subpartArray
	 * @param array $wrappedSubpartArray
	 * @param array $params
	 * @param tx_rnbase_util_FormatUtil $formatter
	 * @param string $confId
	 *
	 * @return string
	 */
	protected function substituteMarkerArray(
		$template,
		array $markerArray,
		array $subpartArray,
		array $wrappedSubpartArray,
		array $params,
		tx_rnbase_util_FormatUtil $formatter,
		$confId
	) {
		// labels und module parsen
		tx_rnbase_util_BaseMarker::callModules(
			$template,
			$markerArray,
			$subpartArray,
			$wrappedSubpartArray,
			$params,
			$formatter
		);

		// receiver und module rendern
		return tx_rnbase_util_Templates::substituteMarkerArrayCached(
			$template,
			$markerArray,
			$subpartArray,
			$wrappedSubpartArray
		);
	}

	/**
	 * Verändert das entgültige HTML.
	 *
	 * @param 	string 	$content
	 * @return 	string
	 */
	protected function fixContentHtml($content){
		return trim($content);
	}

	/**
	 * Verändert den entgültigen Text.
	 *
	 * @param 	string 	$content
	 * @return 	string
	 */
	protected function fixSubject($content){
		// Wir entfernen die HTML Tags!
		return trim(strip_tags($content));
	}

	/**
	 * Verändert den entgültigen Text.
	 *
	 * @param 	string 	$content
	 * @return 	string
	 */
	protected function fixContentText($content){
		// BR-Tags wandeln wir in Umbrüche um.
		$replaces = array('<br />'=>"\r\n",'<br/>'=>"\r\n");
		$content = str_replace(
			array_keys($replaces),
			array_values($replaces),
			trim($content)
		);
		// Wir wollen nur Text. HTML code entfernen wir!
		$content = strip_tags($content);
		return trim($content);
	}

	/**
	 * Liefert den Record, der im Wrappertemplate als RECEIVER_* gerendert wird.
	 *
	 * @param int $idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return array
	 */
	protected function getReceiverRecord($idx) {
		$record = $this->getSingleAddress($idx);
		return $record;
	}

	/**
	 * Erstellt eine individuelle Email für einen Empfänger der Email.
	 *
	 * @param 	tx_mkmailer_models_Queue 	$queue
	 * @param 	tx_rnbase_util_FormatUtil 	$formatter
	 * @param 	string 						$confId
	 * @param 	int 						$idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return 	tx_mkmailer_mail_IMessage
	 */
	public function getSingleMail($queue, &$formatter, $confId, $idx) {
		$confId .= $this->getConfId();

		$mailText = $queue->getContentText();
		$mailHtml = $queue->getContentHtml();
		$mailSubject = $queue->getSubject();

		// erstmal das Template wrappen, da dort marker sein könnten, welche ersetzt werden müssen.
		$mailText = $this->parseTemplate(
			$mailText, $formatter->getConfigurations(), $confId, 'text', $idx
		);
		$mailHtml = $this->parseTemplate(
			$mailHtml, $formatter->getConfigurations(), $confId, 'html', $idx
		);

		// zusätzliche Marker füllen
		$this->addAdditionalData(
			$mailText, $mailHtml, $mailSubject, $formatter, $confId, $idx
		);

		// Nun den Receiver parsen
		// Dies machen wir absichtlich nach dem Wrap, da dort und beim parsen von AdditionalDatamarker weitere Marker enthalten sein können.
		$mailText = $this->parseReceiver(
			$mailText, $formatter->getConfigurations(), $confId, 'text', $idx
		);
		$mailHtml = $this->parseReceiver(
			$mailHtml, $formatter->getConfigurations(), $confId, 'html', $idx
		);

		$mailSubject = $this->fixSubject($mailSubject);
		$mailText = $this->fixContentText($mailText);
		$mailHtml = $this->fixContentHtml($mailHtml);

		$msg = tx_rnbase::makeInstance('tx_mkmailer_mail_SimpleMessage');
		// @TODO: Was ist eigentlich mit den CCs und BCCs??
		$singleAddress = $this->getSingleAddress($idx);
		$sendTo = $this->email ? $this->email : $singleAddress['address'];
		$msg->addTo($sendTo);
		$msg->setTxtPart($mailText);
		$msg->setHtmlPart($mailHtml);
		$msg->setSubject($mailSubject);

		return $msg;
	}

	/**
	 * Hier können susätzliche Daten in das Template gefügt werden.
	 *
	 * @param 	string 						$mailText
	 * @param 	string 						$mailHtml
	 * @param 	string 						$mailSubject
	 * @param 	tx_rnbase_util_FormatUtil 	$formatter
	 * @param 	string 						$confId
	 * @param 	int 						$idx Index des Empfängers von 0 bis (getAddressCount() - 1)
	 * @return 	tx_mkmailer_mail_IMessage
	 */
	protected function addAdditionalData(&$mailText, &$mailHtml, &$mailSubject, $formatter, $confId, $idx) {

	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_BaseTemplate.php'])	{
  include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_BaseTemplate.php']);
}
