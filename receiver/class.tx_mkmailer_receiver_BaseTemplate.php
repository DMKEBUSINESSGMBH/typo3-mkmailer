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
tx_rnbase::load('tx_mkmailer_receiver_Base');

/**
 * Basisklasse für Receiver.
 * Um den jeweiligen Inhalt wird ein Template gemappt, falls konfiguriert.
 * 
 * Muss noch um die Methoden des Interfaces tx_mkmailer_receiver_IMailReceiver erweitert werden.
 * getAddressCount, getAddresses, getName, getSingleAddress, setValueString
 *  
 * @package tx_mkmailer
 * @subpackage tx_mkmailer_receiver
 * @author Michael Wagner <michael.wagner@das-medienkombinat.de>
 */
abstract class tx_mkmailer_receiver_BaseTemplate extends tx_mkmailer_receiver_Base {
	
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
	 * @return 	string
	 */
	private function parseTemplate($content, $configurations, $confId, $type){
		
		if(!$configurations->getBool($confId.'wrapTemplate')) return $content;
		
		/* *** Template auslesen *** */
		$templatePath = $this->getConfig($configurations, $confId, $type, 'Template');
		
		if(!$templatePath) return '<!-- NO Template defined. -->'.$content;

	    tx_rnbase::load('tx_rnbase_util_Files');
	    $template = tx_rnbase_util_Files::getFileResource($templatePath);
	    if(!$template) return '<!-- TEMPLATE NOT FOUND: '.$templatePath.' -->'.$content;
	    
		/* *** Subpart auslesen *** */
		$subpart = $this->getConfig($configurations, $confId, $type, 'Subpart');
		$subpart = $subpart ? $subpart : '###CONTENT'.strtoupper($type).'###';
	    $template = t3lib_parsehtml::getSubpart($template,$subpart);
	    
	    if(!$template) return '<!-- SUBPART NOT FOUND: '.$subpart.' -->'.$content;
	    
	    $markerArray['###CONTENT###'] = $content;

	    tx_rnbase::load('tx_rnbase_util_Templates');
	    $out = tx_rnbase_util_Templates::substituteMarkerArrayCached($template, $markerArray);
	    return trim($out);
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
		$mailText = $this->parseTemplate($mailText, $formatter->getConfigurations(), $confId, 'text');
		$mailHtml = $this->parseTemplate($mailHtml, $formatter->getConfigurations(), $confId, 'html');
		
		// zusätzliche Marker füllen
		$this->addAdditionalData($mailText, $mailHtml, $mailSubject, $formatter, $confId, $idx);
		
		$mailSubject = $this->fixSubject($mailSubject);
		$mailText = $this->fixContentText($mailText);
		$mailHtml = $this->fixContentHtml($mailHtml);
		
		$msg = tx_rnbase::makeInstance('tx_mkmailer_mail_SimpleMessage');
		$msg->addTo($this->email);
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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_BaseTemplate.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mkmailer/receiver/class.tx_mkmailer_receiver_BaseTemplate.php']);
}