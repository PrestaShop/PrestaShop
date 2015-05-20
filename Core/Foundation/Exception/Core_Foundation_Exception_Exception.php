<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Core_Foundation_Exception_Exception extends Exception
{
	const WEB_BREAK_LINE = '<br/>';

	/**
	 * Dump exception thrown and terminate script execution
	 */
	protected function dumpExceptionAndQuit()
	{
		header('HTTP/1.1 500 Internal Server Error');

		$final_output = '['.strtoupper(get_called_class()).']';
		$final_output .= $this->addBreakLines(2);
		$final_output .= 'Stack Trace:'.$this->addBreakLines(1);
		$final_output .= '- Code:'.$this->getCode().$this->addBreakLines(1);
		$final_output .= '- File:'.$this->getFile().$this->addBreakLines(1);
		$final_output .= '- Line:'.$this->getLine().$this->addBreakLines(1);
		$final_output .= $this->addBreakLines(2);
		$final_output .= print_r($this->getTrace(), true);
		$final_output .= $this->addBreakLines(2);
		echo $final_output;
		exit;

	}

	/**
	 * Add breaklines $times times !
	 * @param $times
	 * @return string
	 */
	private function addBreakLines($times)
	{
		$ret_val = '';

		while ($times != 0) {
			$ret_val .= Core_Foundation_Exception_Exception::WEB_BREAK_LINE;
			$times--;
		}

		return $ret_val;
	}
}