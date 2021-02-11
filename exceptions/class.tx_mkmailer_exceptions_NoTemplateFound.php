<?php

/**
 * tx_mkmailer_exceptions_NoTemplateFound.
 *
 * No template found exception
 *
 * @author          Michael Wagner <dev@dmk-ebusiness.de>
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_exceptions_NoTemplateFound extends Exception
{
    /**
     * @param string $message
     * @param string $code
     * @param string $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (!$message) {
            $message = 'No mail template found!';
        }
        parent::__construct($message, $code, $previous);
    }
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/exceptions/class.tx_mkmailer_exceptions_NoTemplateFound.php']) {
    include_once $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/mkmailer/exceptions/class.tx_mkmailer_exceptions_NoTemplateFound.php'];
}
