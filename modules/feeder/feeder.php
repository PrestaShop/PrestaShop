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

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Feeder extends Module
{
	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'feeder';
		$this->tab = 'front_office_features';
		$this->version = 0.2;
		$this->author = 'PrestaShop';
		
		$this->_directory = dirname(__FILE__).'/../../';
		parent::__construct();
		
		$this->displayName = $this->l('RSS products feed');
		$this->description = $this->l('Generate a RSS products feed');
	}
	
	function install()
	{
		if (!parent::install())
			return false;
		if (!$this->registerHook('header'))
			return false;
		return true;
	}
	
	function hookHeader($params)
	{
		global $smarty, $cookie;
		
		$id_category = (int)(Tools::getValue('id_category'));
		if (!$id_category)
		{
			if (isset($_SERVER['HTTP_REFERER']) AND preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs) AND !strstr($_SERVER['HTTP_REFERER'], '.html'))
			{
				if (isset($regs[2]) AND is_numeric($regs[2]))
					$id_category = (int)($regs[2]);
				elseif (isset($regs[5]) AND is_numeric($regs[5]))
					$id_category = (int)($regs[5]);
			}
			elseif ($id_product = (int)(Tools::getValue('id_product')))
			{
				$product = new Product($id_product);
				$id_category = $product->id_category_default;
			}
		}
		$category = new Category($id_category);
		$orderByValues = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity');
		$orderWayValues = array(0 => 'ASC', 1 => 'DESC');
		$orderBy = Tools::strtolower(Tools::getValue('orderby', $orderByValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_BY'))]));
		$orderWay = Tools::strtoupper(Tools::getValue('orderway', $orderWayValues[(int)(Configuration::get('PS_PRODUCTS_ORDER_WAY'))]));
		if (!in_array($orderBy, $orderByValues))
			$orderBy = $orderByValues[0];
		if (!in_array($orderWay, $orderWayValues))
			$orderWay = $orderWayValues[0];
		$smarty->assign(array(
			'feedUrl' => Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/rss.php?id_category='.$id_category.'&amp;orderby='.$orderBy.'&amp;orderway='.$orderWay,
		));
		return $this->display(__FILE__, 'feederHeader.tpl');
	}
}
