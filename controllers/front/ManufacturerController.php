<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ManufacturerControllerCore extends FrontController
{
	public $php_self = 'manufacturer';
	protected $manufacturer;

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'product_list.css');

		if (Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
	}

	public function canonicalRedirection($canonicalURL = '')
	{
		if (Validate::isLoadedObject($this->manufacturer))
			parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
	}

	/**
	 * Initialize manufaturer controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if ($id_manufacturer = Tools::getValue('id_manufacturer'))
		{
			$this->manufacturer = new Manufacturer((int)$id_manufacturer, $this->context->language->id);
			if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop())
			{
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				$this->errors[] = Tools::displayError('Manufacturer does not exist.');
			}
			else
				$this->canonicalRedirection();
		}
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop())
		{
			$this->productSort();
			$this->assignOne();
			$this->setTemplate(_PS_THEME_DIR_.'manufacturer.tpl');
		}
		else
		{
			$this->assignAll();
			$this->setTemplate(_PS_THEME_DIR_.'manufacturer-list.tpl');
		}
	}

	/**
	 * Assign template vars if displaying one manufacturer
	 */
	protected function assignOne()
	{
		$nbProducts = $this->manufacturer->getProducts($this->manufacturer->id, null, null, null, $this->orderBy, $this->orderWay, true);
		$this->pagination((int)$nbProducts);
		$this->context->smarty->assign(array(
			'nb_products' => $nbProducts,
			'products' => $this->manufacturer->getProducts($this->manufacturer->id, $this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay),
			'path' => ($this->manufacturer->active ? Tools::safeOutput($this->manufacturer->name) : ''),
			'manufacturer' => $this->manufacturer,
			'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			);
	}

	/**
	 * Assign template vars if displaying the manufacturer list
	 */
	protected function assignAll()
	{
		if (Configuration::get('PS_DISPLAY_SUPPLIERS'))
		{
			$id_current_shop_group = Shop::getContextShopGroupID();
			$data = Manufacturer::getManufacturers(true, $this->context->language->id, true, false, false, false, $id_current_shop_group);
			$nbProducts = count($data);
			$this->pagination($nbProducts);

			foreach ($data as &$item)
				$item['image'] = (!file_exists(_PS_MANU_IMG_DIR_.'/'.$item['id_manufacturer'].'-medium_default.jpg')) ? $this->context->language->iso_code.'-default' : $item['id_manufacturer'];

			$this->context->smarty->assign(array(
				'pages_nb' => ceil($nbProducts / (int)($this->n)),
				'nbManufacturers' => $nbProducts,
				'mediumSize' => Image::getSize('medium_default'),
				'manufacturers' => $data,
				'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			));
		}
		else
			$this->context->smarty->assign('nbManufacturers', 0);
	}
}
