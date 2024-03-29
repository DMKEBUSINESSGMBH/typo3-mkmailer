<?php

use Sys25\RnBase\Backend\Form\ToolBox;
use Sys25\RnBase\Backend\Module\BaseModFunc;
use Sys25\RnBase\Configuration\Processor;
use Sys25\RnBase\Frontend\Marker\FormatUtil;
use Sys25\RnBase\Frontend\Marker\Templates;

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
 * tx_mkmailer_mod1_FuncTest.
 *
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class tx_mkmailer_mod1_FuncTest extends BaseModFunc
{
    /**
     * (non-PHPdoc).
     *
     * @see \Sys25\RnBase\Backend\Module\BaseModFunc::getFuncId()
     */
    public function getFuncId()
    {
        return 'functest';
    }

    /**
     * Returns the module content.
     *
     * @param string $template
     * @param Processor $configurations
     * @param FormatUtil $formatter
     * @param ToolBox $formTool
     *
     * @return string
     */
    public function getContent($template, &$configurations, &$formatter, $formTool)
    {
        $arr = ['name' => 'alfred'];
        $markerArray = $formatter->getItemMarkerArrayWrapped($arr, $this->getConfId().'queue.', 0, 'MAIL_');
        $out = Templates::substituteMarkerArrayCached($template, $markerArray);

        return $out;
    }
}
