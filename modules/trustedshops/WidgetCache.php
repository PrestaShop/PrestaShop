<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WidgetCache
{
	private $_fileName;
	private $__ts_id;

	public function __construct($_fileName, $_ts_id)
	{
		$this->_fileName = $_fileName;
		$this->_ts_id = $_ts_id;
	}

	public function isFresh($timeout = 10800)
	{
		if (file_exists($this->_fileName))
			return ((time() - filemtime($this->_fileName)) < $timeout);
		else
			return false;
	}

	public function refresh()
	{
		return file_put_contents($this->_fileName, file_get_contents('https://www.trustedshops.com/bewertung/widget/widgets/'.$this->_ts_id.'.gif'));
	}
}

