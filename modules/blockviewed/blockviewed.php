<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockViewed extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'blockviewed';
		$this->tab = 'front_office_features';
		$this->version = 0.9;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Viewed products block');
		$this->description = $this->l('Adds a block displaying last-viewed products.');
	}

	public function install()
	{
		if (!parent::install()
			OR !$this->registerHook('leftColumn')
			OR !$this->registerHook('header')
			OR !Configuration::updateValue('PRODUCTS_VIEWED_NBR', 2))
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockViewed'))
		{
			if (!($productNbr = Tools::getValue('productNbr')) || empty($productNbr))
				$output .= '<div class="alert error">'.$this->l('You must fill in the \'Products displayed\' field.').'</div>';
			elseif ((int)($productNbr) == 0)
				$output .= '<div class="alert error">'.$this->l('Invalid number.').'</div>';
			else
			{
				Configuration::updateValue('PRODUCTS_VIEWED_NBR', (int)$productNbr);
				$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Products displayed').'</label>
				<div class="margin-form">
					<input type="text" name="productNbr" value="'.Configuration::get('PRODUCTS_VIEWED_NBR').'" />
					<p class="clear">'.$this->l('Define the number of products displayed in this block').'</p>
				</div>
				<center><input type="submit" name="submitBlockViewed" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookRightColumn($params)
	{
		$id_product = (int)Tools::getValue('id_product');
		$productsViewed = (isset($params['cookie']->viewed) && !empty($params['cookie']->viewed)) ? array_slice(explode(',', $params['cookie']->viewed), 0, Configuration::get('PRODUCTS_VIEWED_NBR')) : array();

		if (sizeof($productsViewed))
		{
			$defaultCover = Language::getIsoById($params['cookie']->id_lang).'-default';

			$productIds = implode(',', $productsViewed);
			$productsImages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT image_shop.id_image, p.id_product, il.legend, product_shop.active, pl.name, pl.description_short, pl.link_rewrite, cl.link_rewrite AS category_rewrite
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (il.id_image = i.id_image)
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
			WHERE p.id_product IN ('.$productIds.')
			AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')
			AND pl.id_lang = '.(int)($params['cookie']->id_lang).'
			AND cl.id_lang = '.(int)($params['cookie']->id_lang)
			);

			$productsImagesArray = array();
			foreach ($productsImages AS $pi)
				$productsImagesArray[$pi['id_product']] = $pi;

			$productsViewedObj = array();
			foreach ($productsViewed AS $productViewed)
			{
				$obj = (object)'Product';
				if (!isset($productsImagesArray[$productViewed]) || (!$obj->active = $productsImagesArray[$productViewed]['active']))
					continue;
				else
				{
					$obj->id = (int)($productsImagesArray[$productViewed]['id_product']);
					$obj->id_image = (int)$productsImagesArray[$productViewed]['id_image'];
					$obj->cover = (int)($productsImagesArray[$productViewed]['id_product']).'-'.(int)($productsImagesArray[$productViewed]['id_image']);
					$obj->legend = $productsImagesArray[$productViewed]['legend'];
					$obj->name = $productsImagesArray[$productViewed]['name'];
					$obj->description_short = $productsImagesArray[$productViewed]['description_short'];
					$obj->link_rewrite = $productsImagesArray[$productViewed]['link_rewrite'];
					$obj->category_rewrite = $productsImagesArray[$productViewed]['category_rewrite'];
					// $obj is not a real product so it cannot be used as argument for getProductLink()
					$obj->product_link = $this->context->link->getProductLink($obj->id, $obj->link_rewrite, $obj->category_rewrite);

					if (!isset($obj->cover) || !$productsImagesArray[$productViewed]['id_image'])
					{
						$obj->cover = $defaultCover;
						$obj->legend = '';
					}
					$productsViewedObj[] = $obj;
				}
			}

			if ($id_product AND !in_array($id_product, $productsViewed))
			{
				// Check if the user to the right of access to this product
				$result = Db::getInstance()->getRow('
				SELECT COUNT(cug.`id_customer`) AS total
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = cp.`id_category`)
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cug ON (cug.`id_group` = cg.`id_group`)
				WHERE p.`id_product` = '.(int)($id_product).'
				'.($this->context->customer->id ? 'AND cug.`id_customer` = '.(int)$this->context->customer->id :
				'AND cg.`id_group` = 1')
				);
				if ($result['total'])
					array_unshift($productsViewed, $id_product);
			}
			$viewed = '';
			foreach ($productsViewed AS $id_product_viewed)
				$viewed .= (int)($id_product_viewed).',';
			$params['cookie']->viewed = rtrim($viewed, ',');

			if (!sizeof($productsViewedObj))
				return ;

			$this->smarty->assign(array(
				'productsViewedObj' => $productsViewedObj,
				'mediumSize' => Image::getSize('medium')));

			return $this->display(__FILE__, 'blockviewed.tpl');
		}
		elseif ($id_product)
			$params['cookie']->viewed = (int)($id_product);
		return ;
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockviewed.css', 'all');
	}
}
