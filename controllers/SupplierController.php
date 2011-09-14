<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SupplierControllerCore extends FrontController
{
	public $php_self = 'supplier';
	
	protected $supplier;
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'product_list.css');
	}
	
	public function canonicalRedirection()
	{
		if (Configuration::get('PS_CANONICAL_REDIRECT'))
		{
		if (Validate::isLoadedObject($this->supplier))
		{
			$canonicalURL = $this->context->link->getSupplierLink($this->supplier);
			if (!preg_match('/^'.Tools::pRegexp($canonicalURL, '/').'([&?].*)?$/', Tools::getProtocol().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
			{
				header('HTTP/1.0 301 Moved');
				if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
					die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$canonicalURL.'">'.$canonicalURL.'</a>');
				Tools::redirectLink($canonicalURL);
			}
		}
	}
	}
	
	public function preProcess()
	{
		if ($id_supplier = (int)Tools::getValue('id_supplier'))
		{
			$this->supplier = new Supplier($id_supplier, $this->context->language->id);

			if (!Validate::isLoadedObject($this->supplier) OR !$this->supplier->active)
			{
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				$this->errors[] = Tools::displayError('Supplier does not exist.');
			}
			else
				$this->canonicalRedirection();
		}

		parent::preProcess();
	}
	
	public function process()
	{
		if (Validate::isLoadedObject($this->supplier) AND $this->supplier->active AND $this->supplier->isAssociatedToGroupShop())
		{
			$nbProducts = $this->supplier->getProducts($this->supplier->id, NULL, NULL, NULL, $this->orderBy, $this->orderWay, true);
				$this->pagination((int)$nbProducts);
				$this->context->smarty->assign(array(
					'nb_products' => $nbProducts,
					'products' => $this->supplier->getProducts($this->supplier->id, $this->context->cookie->id_lang, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay),
					'path' => ($this->supplier->active ? Tools::safeOutput($this->supplier->name) : ''),
					'supplier' => $this->supplier,
				));
		}
		elseif (!Tools::getValue('id_supplier'))
		{
			if (Configuration::get('PS_DISPLAY_SUPPLIERS'))
			{
				$result = Supplier::getSuppliers(true, $this->context->language->id, true);
				$nbProducts = count($result);
				$this->pagination($nbProducts);
		
				$suppliers = Supplier::getSuppliers(true, $this->context->language->id, true, $this->p, $this->n);
				foreach ($suppliers AS &$row)
					$row['image'] = (!file_exists(_PS_SUPP_IMG_DIR_.'/'.$row['id_supplier'].'-medium.jpg')) ? $this->context->language->iso_code.'-default' : $row['id_supplier'];;

				$this->context->smarty->assign(array(
					'pages_nb' => ceil($nbProducts / (int)$this->n),
					'nbSuppliers' => $nbProducts,
					'mediumSize' => Image::getSize('medium'),
					'suppliers' => $suppliers,
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
				));
			}
			else
				$this->context->smarty->assign('nbSuppliers', 0);
		}
	}
	
	public function displayHeader()
	{
		parent::displayHeader();
		$this->productSort();
	}
	
	public function displayContent()
	{
		parent::displayContent();
		if ($this->supplier)
			$this->context->smarty->display(_PS_THEME_DIR_.'supplier.tpl');
		else
			$this->context->smarty->display(_PS_THEME_DIR_.'supplier-list.tpl');
	}
	
}
