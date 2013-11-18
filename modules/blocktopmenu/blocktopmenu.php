<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require (dirname(__FILE__).'/menutoplinks.class.php');

class Blocktopmenu extends Module
{
	private $_menu = '';
	private $_html = '';
	private $user_groups;

	/*
	 * Pattern for matching config values
	 */
	private $pattern = '/^([A-Z_]*)[0-9]+/';

	/*
	 * Name of the controller
	 * Used to set item selected or not in top menu
	 */
	private $page_name = '';

	/*
	 * Spaces per depth in BO
	 */
	private $spacer_size = '5';

	public function __construct()
	{
		$this->name = 'blocktopmenu';
		$this->tab = 'front_office_features';
		$this->version = 1.6;
		$this->author = 'PrestaShop';

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Top horizontal menu');
		$this->description = $this->l('Add a new horizontal menu to the top of your e-commerce website.');
	}

	public function install()
	{
		if (!parent::install() ||
			!$this->registerHook('displayTop') ||
			!Configuration::updateGlobalValue('MOD_BLOCKTOPMENU_ITEMS', 'CAT1,CMS1,CMS2,PRD1') ||
			!Configuration::updateGlobalValue('MOD_BLOCKTOPMENU_SEARCH', '1') ||
			!$this->registerHook('actionObjectCategoryUpdateAfter') ||
			!$this->registerHook('actionObjectCategoryDeleteAfter') ||
			!$this->registerHook('actionObjectCategoryAddAfter') ||
			!$this->registerHook('actionObjectCmsUpdateAfter') ||
			!$this->registerHook('actionObjectCmsDeleteAfter') ||
			!$this->registerHook('actionObjectCmsAddAfter') ||
			!$this->registerHook('actionObjectSupplierUpdateAfter') ||
			!$this->registerHook('actionObjectSupplierDeleteAfter') ||
			!$this->registerHook('actionObjectSupplierAddAfter') ||
			!$this->registerHook('actionObjectManufacturerUpdateAfter') ||
			!$this->registerHook('actionObjectManufacturerDeleteAfter') ||
			!$this->registerHook('actionObjectManufacturerAddAfter') ||
			!$this->registerHook('actionObjectProductUpdateAfter') ||
			!$this->registerHook('actionObjectProductDeleteAfter') ||
			!$this->registerHook('actionObjectProductAddAfter') ||
			!$this->registerHook('categoryUpdate') ||
			!$this->registerHook('actionShopDataDuplication') ||
			!$this->installDB())
			return false;
		return true;
	}

	public function installDb()
	{
		return (Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'linksmenutop` (
			`id_linksmenutop` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`new_window` TINYINT( 1 ) NOT NULL,
			INDEX (`id_shop`)
		) ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;') &&
			Db::getInstance()->execute('
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'linksmenutop_lang` (
			`id_linksmenutop` INT(11) UNSIGNED NOT NULL,
			`id_lang` INT(11) UNSIGNED NOT NULL,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`label` VARCHAR( 128 ) NOT NULL ,
			`link` VARCHAR( 128 ) NOT NULL ,
			INDEX ( `id_linksmenutop` , `id_lang`, `id_shop`)
		) ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;'));
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
			!Configuration::deleteByName('MOD_BLOCKTOPMENU_ITEMS') ||
			!Configuration::deleteByName('MOD_BLOCKTOPMENU_SEARCH') ||
			!$this->uninstallDB())
			return false;
		return true;
	}

	private function uninstallDb()
	{
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'linksmenutop`');
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'linksmenutop_lang`');
		return true;
	}

	public function getContent()
	{
		$id_lang = (int)Context::getContext()->language->id;
		$languages = $this->context->controller->getLanguages();
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');

		$labels = Tools::getValue('label') ? array_filter(Tools::getValue('label'), 'strlen') : array();
		$links_label = Tools::getValue('link') ? array_filter(Tools::getValue('link'), 'strlen') : array();
		$spacer = str_repeat('&nbsp;', $this->spacer_size);
		$divLangName = 'link_label';
		
		$update_cache = false;

		if (Tools::isSubmit('submitBlocktopmenu'))
		{
			$items = Tools::getValue('items');
			if (is_array($items) && count($items) && Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', implode(',', $items)))
				$this->_html .= $this->displayConfirmation($this->l('The settings have been updated.'));
			else
				$this->_html .= $this->displayError($this->l('Unable to update settings.'));
			Configuration::updateValue('MOD_BLOCKTOPMENU_SEARCH', (bool)Tools::getValue('search'));
			$update_cache = true;
		}
		else if (Tools::isSubmit('submitBlocktopmenuLinks'))
		{
			foreach ($languages as $key => $val)
			{
				$links_label[$val['id_lang']] = Tools::getValue('link_'.(int)$val['id_lang']);
				$labels[$val['id_lang']] = Tools::getValue('label_'.(int)$val['id_lang']);
			}
			
			if ((!count($links_label)) && (!count($labels)))
				;
			else if (!count($links_label))
				$this->_html .= $this->displayError($this->l('Please complete the "link" field.'));
			else if (!count($labels))
				$this->_html .= $this->displayError($this->l('Please add a label'));
			else if (!isset($labels[$default_language]))
				$this->_html .= $this->displayError($this->l('Please add a label for your default language.'));
			else
			{
				MenuTopLinks::add($links_label, $labels,  Tools::getValue('new_window', 0), (int)Shop::getContextShopID());
				$this->_html .= $this->displayConfirmation($this->l('The link has been added.'));
			}
			$update_cache = true;
		}
		else if (Tools::isSubmit('deletelinksmenutop'))
		{
			$id_linksmenutop = Tools::getValue('id_linksmenutop', 0);
			MenuTopLinks::remove($id_linksmenutop, (int)Shop::getContextShopID());
			Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', str_replace(array('LNK'.$id_linksmenutop.',', 'LNK'.$id_linksmenutop), '', Configuration::get('MOD_BLOCKTOPMENU_ITEMS')));
			$this->_html .= $this->displayConfirmation($this->l('The link has been removed'));
			$update_cache = true;
		}
		else if (Tools::isSubmit('updatelinksmenutop'))
		{
			$id_linksmenutop = (int)Tools::getValue('id_linksmenutop', 0);
			$id_shop = (int)Shop::getContextShopID();

			if (Tools::isSubmit('updatelink'))
			{
				$link = array();
				$label = array();
				$new_window = (int)Tools::getValue('new_window', 0);
				
				foreach (Language::getLanguages(false) as $lang)
				{
					$link[$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang']);
					$label[$lang['id_lang']] = Tools::getValue('label_'.(int)$lang['id_lang']);
				}

				MenuTopLinks::update($link, $label, $new_window, (int)$id_shop, (int)$id_linksmenutop, (int)$id_linksmenutop);
				$this->_html .= $this->displayConfirmation($this->l('The link has been edited'));
			}
			$update_cache = true;
		}
		
		if ($update_cache)
			$this->clearMenuCache();
		
		$this->_html .= $this->renderForm();
		$this->_html .= $this->renderAddForm();

		$links = MenuTopLinks::gets((int)$id_lang, null, (int)Shop::getContextShopID());

		if (!count($links))
			return $this->_html;

		$this->_html .= $this->renderList();
		
		return $this->_html;
	}

	private function getMenuItems()
	{
		$items = Tools::getValue('items');
		if (is_array($items) && count($items))
			return $items;
		else
		{
			$conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
			if (strlen($conf) && strpos($conf, ','))
				return explode(',', Configuration::get('MOD_BLOCKTOPMENU_ITEMS'));
			else
				return array();
		}
	}

	private function makeMenuOption()
	{
		$menu_item = $this->getMenuItems();

		$id_lang = (int)$this->context->language->id;
		$id_shop = (int)Shop::getContextShopID();
		$html = '<select multiple="multiple" name="items[]" id="items" style="width: 300px; height: 160px;">';
		foreach ($menu_item as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $values);
			$id = (int)substr($item, strlen($values[1]), strlen($item));

			switch (substr($item, 0, strlen($values[1])))
			{
				case 'CAT':
					$category = new Category((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($category))
						$html .= '<option selected="selected" value="CAT'.$id.'">'.$category->name.'</option>'.PHP_EOL;
					break;

				case 'PRD':
					$product = new Product((int)$id, true, (int)$id_lang);
					if (Validate::isLoadedObject($product))
						$html .= '<option selected="selected" value="PRD'.$id.'">'.$product->name.'</option>'.PHP_EOL;
					break;

				case 'CMS':
					$cms = new CMS((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($cms))
						$html .= '<option selected="selected" value="CMS'.$id.'">'.$cms->meta_title.'</option>'.PHP_EOL;
					break;

				case 'CMS_CAT':
					$category = new CMSCategory((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($category))
						$html .= '<option selected="selected" value="CMS_CAT'.$id.'">'.$category->name.'</option>'.PHP_EOL;
					break;

				// Case to handle the option to show all Manufacturers
				case 'ALLMAN':
					$html .= '<option selected="selected" value="ALLMAN0">'.$this->l('All manufacturers').'</option>'.PHP_EOL;
					break;

				case 'MAN':
					$manufacturer = new Manufacturer((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($manufacturer))
						$html .= '<option selected="selected" value="MAN'.$id.'">'.$manufacturer->name.'</option>'.PHP_EOL;
					break;

				// Case to handle the option to show all Suppliers
				case 'ALLSUP':
					$html .= '<option selected="selected" value="ALLSUP0">'.$this->l('All suppliers').'</option>'.PHP_EOL;
					break;
					
				case 'SUP':
					$supplier = new Supplier((int)$id, (int)$id_lang);
					if (Validate::isLoadedObject($supplier))
						$html .= '<option selected="selected" value="SUP'.$id.'">'.$supplier->name.'</option>'.PHP_EOL;
					break;

				case 'LNK':
					$link = MenuTopLinks::get((int)$id, (int)$id_lang, (int)$id_shop);
					if (count($link))
					{
						if (!isset($link[0]['label']) || ($link[0]['label'] == ''))
						{
							$default_language = Configuration::get('PS_LANG_DEFAULT');
							$link = MenuTopLinks::get($link[0]['id_linksmenutop'], (int)$default_language, (int)Shop::getContextShopID());
						}
						$html .= '<option selected="selected" value="LNK'.$link[0]['id_linksmenutop'].'">'.$link[0]['label'].'</option>';
					}
					break;
				case 'SHOP':
					$shop = new Shop((int)$id);
					if (Validate::isLoadedObject($shop))
						$html .= '<option selected="selected" value="SHOP'.(int)$id.'">'.$shop->name.'</option>'.PHP_EOL;
					break;
			}
		}
		return $html.'</select>';
	}

	private function makeMenu()
	{
		$menu_items = $this->getMenuItems();
		$id_lang = (int)$this->context->language->id;
		$id_shop = (int)Shop::getContextShopID();

		foreach ($menu_items as $item)
		{
			if (!$item)
				continue;

			preg_match($this->pattern, $item, $value);
			$id = (int)substr($item, strlen($value[1]), strlen($item));

			switch (substr($item, 0, strlen($value[1])))
			{
				case 'CAT':
					$this->getCategory((int)$id);
					break;

				case 'PRD':
					$selected = ($this->page_name == 'product' && (Tools::getValue('id_product') == $id)) ? ' class="sfHover"' : '';
					$product = new Product((int)$id, true, (int)$id_lang);
					if (!is_null($product->id))
						$this->_menu .= '<li'.$selected.'><a href="'.Tools::HtmlEntitiesUTF8($product->getLink()).'">'.$product->name.'</a></li>'.PHP_EOL;
					break;

				case 'CMS':
					$selected = ($this->page_name == 'cms' && (Tools::getValue('id_cms') == $id)) ? ' class="sfHover"' : '';
					$cms = CMS::getLinks((int)$id_lang, array($id));
					if (count($cms))
						$this->_menu .= '<li'.$selected.'><a href="'.Tools::HtmlEntitiesUTF8($cms[0]['link']).'">'.$cms[0]['meta_title'].'</a></li>'.PHP_EOL;
					break;

				case 'CMS_CAT':
					$category = new CMSCategory((int)$id, (int)$id_lang);
					if (count($category))
					{
						$this->_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($category->getLink()).'">'.$category->name.'</a>';
						$this->getCMSMenuItems($category->id);
						$this->_menu .= '</li>'.PHP_EOL;
					}
					break;

				// Case to handle the option to show all Manufacturers
				case 'ALLMAN':
					$link = new Link;
					$this->_menu .= '<li><a href="'.$link->getPageLink('manufacturer').'">'.$this->l('All manufacturers').'</a><ul>'.PHP_EOL;
					$manufacturers = Manufacturer::getManufacturers();
					foreach ($manufacturers as $key => $manufacturer)
						$this->_menu .= '<li><a href="'.$link->getManufacturerLink((int)$manufacturer['id_manufacturer'], $manufacturer['link_rewrite']).'">'.$manufacturer['name'].'</a></li>'.PHP_EOL;
					$this->_menu .= '</ul>';
					break;

				case 'MAN':
					$selected = ($this->page_name == 'manufacturer' && (Tools::getValue('id_manufacturer') == $id)) ? ' class="sfHover"' : '';
					$manufacturer = new Manufacturer((int)$id, (int)$id_lang);
					if (!is_null($manufacturer->id))
					{
						if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
							$manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
						else
							$manufacturer->link_rewrite = 0;
						$link = new Link;
						$this->_menu .= '<li'.$selected.'><a href="'.Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int)$id, $manufacturer->link_rewrite)).'">'.$manufacturer->name.'</a></li>'.PHP_EOL;
					}
					break;

				// Case to handle the option to show all Suppliers
				case 'ALLSUP':
					$link = new Link;
					$this->_menu .= '<li><a href="'.$link->getPageLink('supplier').'">'.$this->l('All suppliers').'</a><ul>'.PHP_EOL;
					$suppliers = Supplier::getSuppliers();
					foreach ($suppliers as $key => $supplier)
						$this->_menu .= '<li><a href="'.$link->getSupplierLink((int)$supplier['id_supplier'], $supplier['link_rewrite']).'">'.$supplier['name'].'</a></li>'.PHP_EOL;
					$this->_menu .= '</ul>';
					break;

				case 'SUP':
					$selected = ($this->page_name == 'supplier' && (Tools::getValue('id_supplier') == $id)) ? ' class="sfHover"' : '';
					$supplier = new Supplier((int)$id, (int)$id_lang);
					if (!is_null($supplier->id))
					{
						$link = new Link;
						$this->_menu .= '<li'.$selected.'><a href="'.Tools::HtmlEntitiesUTF8($link->getSupplierLink((int)$id, $supplier->link_rewrite)).'">'.$supplier->name.'</a></li>'.PHP_EOL;
					}
					break;

				case 'SHOP':
					$selected = ($this->page_name == 'index' && ($this->context->shop->id == $id)) ? ' class="sfHover"' : '';
					$shop = new Shop((int)$id);
					if (Validate::isLoadedObject($shop))
					{
						$link = new Link;
						$this->_menu .= '<li'.$selected.'><a href="'.Tools::HtmlEntitiesUTF8($shop->getBaseURL()).'">'.$shop->name.'</a></li>'.PHP_EOL;
					}
					break;
				case 'LNK':
					$link = MenuTopLinks::get((int)$id, (int)$id_lang, (int)$id_shop);
					if (count($link))
					{
						if (!isset($link[0]['label']) || ($link[0]['label'] == ''))
						{
							$default_language = Configuration::get('PS_LANG_DEFAULT');
							$link = MenuTopLinks::get($link[0]['id_linksmenutop'], $default_language, (int)Shop::getContextShopID());
						}
						$this->_menu .= '<li><a href="'.Tools::HtmlEntitiesUTF8($link[0]['link']).'"'.(($link[0]['new_window']) ? ' target="_blank"': '').'>'.$link[0]['label'].'</a></li>'.PHP_EOL;
					}
					break;
			}
		}
	}

	private function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);

		if (is_null($category->id))
			return;

		if ($recursive)
		{
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$category->level_depth);
		}

		$shop = (object) Shop::getShop((int)$category->getShopID());
		$this->_html .= '<option value="CAT'.(int)$category->id.'">'.(isset($spacer) ? $spacer : '').$category->name.' ('.$shop->name.')</option>';

		if (isset($children) && count($children))
			foreach ($children as $child)
			{
				$this->getCategoryOption((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
			}
	}

	private function getCategory($id_category, $id_lang = false, $id_shop = false)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang);

		if ($category->level_depth > 1)
			$category_link = $category->getLink();
		else
			$category_link = $this->context->link->getPageLink('index');

		if (is_null($category->id))
			return;

		$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
		$selected = ($this->page_name == 'category' && ((int)Tools::getValue('id_category') == $id_category)) ? ' class="sfHoverForce"' : '';

		$is_intersected = array_intersect($category->getGroups(), $this->user_groups);
		// filter the categories that the user is allowed to see and browse
		if (!empty($is_intersected))
		{
			$this->_menu .= '<li '.$selected.'>';
			$this->_menu .= '<a href="'.Tools::HtmlEntitiesUTF8($category_link).'">'.$category->name.'</a>';

			if (count($children))
			{
				$this->_menu .= '<ul>';

				foreach ($children as $child)
					$this->getCategory((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);

				if ($category->level_depth == 2)
				{
					$files = scandir(_PS_CAT_IMG_DIR_);
					$this->_menu .= '<li id="category-thumbnail">';

					foreach ($files as $file)
						if (preg_match('/'.$category->id.'-([0-9])?_thumb.jpg/i', $file) === 1)
							$this->_menu .= '<div>'.ImageManager::thumbnail(_PS_CAT_IMG_DIR_.$file, 'category_'.$file, 100, 'jpg', true, true).'</div>';

					$this->_menu .= '</li>';
				}

				$this->_menu .= '</ul>';
			}

			$this->_menu .= '</li>';
		}
	}

	private function getCMSMenuItems($parent, $depth = 1, $id_lang = false)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

		if ($depth > 3)
			return;

		$categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang);
		$pages = $this->getCMSPages((int)$parent);

		if (count($categories) || count($pages))
		{
			$this->_menu .= '<ul>';

			foreach ($categories as $category)
			{
				$this->_menu .= '<li>';
				$this->_menu .= '<a href="#">'.$category['name'].'</a>';
				$this->getCMSMenuItems($category['id_cms_category'], (int)$depth + 1);
				$this->_menu .= '</li>';
			}

			foreach ($pages as $page)
			{
				$cms = new CMS($page['id_cms'], (int)$id_lang);
				$links = $cms->getLinks((int)$id_lang, array((int)$cms->id));

				$selected = ($this->page_name == 'cms' && ((int)Tools::getValue('id_cms') == $page['id_cms'])) ? ' class="sfHoverForce"' : '';
				$this->_menu .= '<li '.$selected.'>';
				$this->_menu .= '<a href="'.$links[0]['link'].'">'.$cms->meta_title.'</a>';
				$this->_menu .= '</li>';
			}

			$this->_menu .= '</ul>';
		}
	}

	private function getCMSOptions($parent = 0, $depth = 1, $id_lang = false)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

		$categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang);
		$pages = $this->getCMSPages((int)$parent, false, (int)$id_lang);

		$spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$depth);

		foreach ($categories as $category)
		{
			$this->_html .= '<option value="CMS_CAT'.$category['id_cms_category'].'" style="font-weight: bold;">'.$spacer.$category['name'].'</option>';
			$this->getCMSOptions($category['id_cms_category'], (int)$depth + 1, (int)$id_lang);
		}

		foreach ($pages as $page)
			$this->_html .= '<option value="CMS'.$page['id_cms'].'">'.$spacer.$page['meta_title'].'</option>';
	}
	
	protected function getCacheId($name = null)
	{
		parent::getCacheId($name);
		$page_name = in_array($this->page_name, array('category', 'supplier', 'manufacturer', 'cms', 'product')) ? $this->page_name : 'index';
		return 'blocktopmenu|'.(int)Tools::usingSecureMode().'|'.$page_name.'|'.(int)$this->context->shop->id.'|'.implode(', ',$this->user_groups).'|'.(int)$this->context->language->id.'|'.(int)Tools::getValue('id_category').'|'.(int)Tools::getValue('id_manufacturer').'|'.(int)Tools::getValue('id_supplier').'|'.(int)Tools::getValue('id_cms').'|'.(int)Tools::getValue('id_product');
	}

	public function hookDisplayTop($param)
	{
		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->page_name = Dispatcher::getInstance()->getController();
		if (!$this->isCached('blocktopmenu.tpl', $this->getCacheId()))
		{
			$this->makeMenu();
			$this->smarty->assign('MENU_SEARCH', Configuration::get('MOD_BLOCKTOPMENU_SEARCH'));
			$this->smarty->assign('MENU', $this->_menu);
			$this->smarty->assign('this_path', $this->_path);
		}

		$this->context->controller->addJS($this->_path.'js/hoverIntent.js');
		$this->context->controller->addJS($this->_path.'js/superfish-modified.js');
		$this->context->controller->addCSS($this->_path.'css/superfish-modified.css');

		$html = $this->display(__FILE__, 'blocktopmenu.tpl', $this->getCacheId());
		return $html;
	}

	private function getCMSCategories($recursive = false, $parent = 1, $id_lang = false)
	{
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

		if ($recursive === false)
		{
			$sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent;

			return Db::getInstance()->executeS($sql);
		}
		else
		{
			$sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent;

			$results = Db::getInstance()->executeS($sql);
			foreach ($results as $result)
			{
				$sub_categories = $this->getCMSCategories(true, $result['id_cms_category'], (int)$id_lang);
				if ($sub_categories && count($sub_categories) > 0)
					$result['sub_categories'] = $sub_categories;
				$categories[] = $result;
			}

			return isset($categories) ? $categories : false;
		}

	}

	private function getCMSPages($id_cms_category, $id_shop = false, $id_lang = false)
	{
		$id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

		$sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'cms` c
			INNER JOIN `'._DB_PREFIX_.'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `'._DB_PREFIX_.'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = '.(int)$id_cms_category.'
			AND cs.`id_shop` = '.(int)$id_shop.'
			AND cl.`id_lang` = '.(int)$id_lang.'
			AND c.`active` = 1
			ORDER BY `position`';

		return Db::getInstance()->executeS($sql);
	}

	public function hookActionObjectCategoryAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectCategoryUpdateAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectCategoryDeleteAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectCmsUpdateAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectCmsDeleteAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectCmsAddAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectSupplierUpdateAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectSupplierDeleteAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectSupplierAddAfter($params)
	{
		$this->clearMenuCache();
	}

	public function hookActionObjectManufacturerUpdateAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectManufacturerDeleteAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectManufacturerAddAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectProductUpdateAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectProductDeleteAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookActionObjectProductAddAfter($params)
	{
		$this->clearMenuCache();
	}
	
	public function hookCategoryUpdate($params)
	{
		$this->clearMenuCache();
	}
	
	private function clearMenuCache()
	{
		$this->_clearCache('blocktopmenu.tpl');
	}
	
	public function hookActionShopDataDuplication($params)
	{
		$linksmenutop = Db::getInstance()->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'linksmenutop 
			WHERE id_shop = '.(int)$params['old_id_shop']
			);

		foreach($linksmenutop as $id => $link)
		{
			Db::getInstance()->execute('
				INSERT IGNORE INTO '._DB_PREFIX_.'linksmenutop (id_linksmenutop, id_shop, new_window) 
				VALUES (null, '.(int)$params['new_id_shop'].', '.(int)$link['new_window'].')');
			
			$linksmenutop[$id]['new_id_linksmenutop'] = Db::getInstance()->Insert_ID();
		}
		
		foreach($linksmenutop as $id => $link)
		{
			$lang = Db::getInstance()->executeS('
					SELECT id_lang, '.(int)$params['new_id_shop'].', label, link 
					FROM '._DB_PREFIX_.'linksmenutop_lang 
					WHERE id_linksmenutop = '.(int)$link['id_linksmenutop'].' AND id_shop = '.(int)$params['old_id_shop']);
			
			foreach($lang as $l)
				Db::getInstance()->execute('
					INSERT IGNORE INTO '._DB_PREFIX_.'linksmenutop_lang (id_linksmenutop, id_lang, id_shop, label, link) 
					VALUES ('.(int)$link['new_id_linksmenutop'].', '.(int)$l['id_lang'].', '.(int)$params['new_id_shop'].', '.(int)$l['label'].', '.(int)$l['link'].' )');
		}
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Menu Top Link'),
					'icon' => 'icon-link'
				),
				'input' => array(
					array(
						'type' => 'link_choice',
						'label' => '',
						'name' => 'link',
						'lang' => true,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Search Bar'),
						'name' => 'search',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)		
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default',
					)
			),
		);
		
		if (Shop::isFeatureActive())
			$fields_form['form']['description'] = $this->l('The modifications will be applied to').' '.(Shop::getContext() == Shop::CONTEXT_SHOP ? $this->l('shop').' '.$this->context->shop->name : $this->l('all shops'));
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;		
		$helper->submit_action = 'submitBlocktopmenu';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'choices' => $this->renderChoicesSelect(),
			'selected_links' => $this->makeMenuOption(),
		);
		return $helper->generateForm(array($fields_form));
	}
	
	public function renderAddForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Menu Top Link'),
					'icon' => 'icon-link'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Label:'),
						'name' => 'label',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Link:'),
						'name' => 'link',
						'lang' => true,
					),
					array(
						'type' => 'switch',
						'label' => $this->l('New window'),
						'name' => 'new_window',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)		
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-default',
					)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		
		
		if (Tools::isSubmit('updatelinksmenutop'))
		{
			$helper->submit_action = 'updatelinksmenutop';
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'updatelink');
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_linksmenutop');
		}
		else
			$helper->submit_action = 'submitBlocktopmenuLinks';
			
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getAddLinkFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}

	public function renderChoicesSelect()
	{
		$spacer = str_repeat('&nbsp;', $this->spacer_size);
		
		$html = '<select multiple="multiple" id="availableItems" style="width: 300px; height: 160px;">';
		$html .= '<optgroup label="'.$this->l('CMS').'">';
		$this->getCMSOptions(0, 1, $this->context->language->id);
		$html .= '</optgroup>';

		// BEGIN SUPPLIER
		$html .= '<optgroup label="'.$this->l('Supplier').'">';
		// Option to show all Suppliers
		$html .= '<option value="ALLSUP0">'.$this->l('All suppliers').'</option>';
		$suppliers = Supplier::getSuppliers(false, $this->context->language->id);
		foreach ($suppliers as $supplier)
			$html .= '<option value="SUP'.$supplier['id_supplier'].'">'.$spacer.$supplier['name'].'</option>';
		$html .= '</optgroup>';

		// BEGIN Manufacturer
		$html .= '<optgroup label="'.$this->l('Manufacturer').'">';
		// Option to show all Manufacturers
		$html .= '<option value="ALLMAN0">'.$this->l('All manufacturers').'</option>';
		$manufacturers = Manufacturer::getManufacturers(false, $this->context->language->id);
		foreach ($manufacturers as $manufacturer)
			$html .= '<option value="MAN'.$manufacturer['id_manufacturer'].'">'.$spacer.$manufacturer['name'].'</option>';
		$html .= '</optgroup>';

		// BEGIN Categories
		$html .= '<optgroup label="'.$this->l('Categories').'">';
		$this->getCategoryOption(1, (int)$this->context->language->id, (int)Shop::getContextShopID());
		$html .= '</optgroup>';
		
		// BEGIN Shops
		if (Shop::isFeatureActive())
		{
			$html .= '<optgroup label="'.$this->l('Shops').'">';
			$shops = Shop::getShopsCollection();
			foreach ($shops as $shop)
			{
				if (!$shop->setUrl() && !$shop->getBaseURL())
					continue;
				$html .= '<option value="SHOP'.(int)$shop->id.'">'.$spacer.$shop->name.'</option>';
			}	
			$html .= '</optgroup>';
		}
		
		// BEGIN Products
		$html .= '<optgroup label="'.$this->l('Products').'">';
		$html .= '<option value="PRODUCT" style="font-style:italic">'.$spacer.$this->l('Choose product ID').'</option>';
		$html .= '</optgroup>';

		// BEGIN Menu Top Links
		$html .= '<optgroup label="'.$this->l('Menu Top Links').'">';
		$links = MenuTopLinks::gets($this->context->language->id, null, (int)Shop::getContextShopID());
		foreach ($links as $link)
		{
			if ($link['label'] == '')
			{
				$link = MenuTopLinks::get($link['id_linksmenutop'], $default_language, (int)Shop::getContextShopID());
				$html .= '<option value="LNK'.(int)$link[0]['id_linksmenutop'].'">'.$spacer.$link[0]['label'].'</option>';
			}
			else
				$html .= '<option value="LNK'.(int)$link['id_linksmenutop'].'">'.$spacer.$link['label'].'</option>';
		}
		$html .= '</optgroup>';
		$html .= '</select>';
		return $html;
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'search' => Tools::getValue('search', Configuration::get('MOD_BLOCKTOPMENU_SEARCH'))
			);
	}
	
	public function getAddLinkFieldsValues()
	{
		$links_label_edit = '';
		$labels_edit = '';
		$new_window_edit = '';
		if (Tools::isSubmit('updatelinksmenutop'))
		{
			$link = MenuTopLinks::getLinkLang(Tools::getValue('id_linksmenutop'), (int)Shop::getContextShopID());
			$links_label_edit = $link['link'];
			$labels_edit = $link['label'];
			$new_window_edit = $link['new_window'];
		}			
		
		$fields_values = array(
			'new_window' => Tools::getValue('new_window', $new_window_edit),
			'id_linksmenutop' => Tools::getValue('id_linksmenutop'),
		);
		
		foreach (Language::getLanguages(false) as $lang)
		{
			$fields_values['label'][$lang['id_lang']] = Tools::getValue('label_'.(int)$lang['id_lang'], isset($labels_edit[$lang['id_lang']]) ? $labels_edit[$lang['id_lang']] : '');
			$fields_values['link'][$lang['id_lang']] = Tools::getValue('link_'.(int)$lang['id_lang'], isset($links_label_edit[$lang['id_lang']]) ? $links_label_edit[$lang['id_lang']] : '');
		}
		
		return $fields_values;	
	}
	
	public function renderList()
	{
		$links = MenuTopLinks::gets((int)$this->context->language->id, null, (int)Shop::getContextShopID());
		$fields_list = array(
			'id_linksmenutop' => array(
				'title' => $this->l('Id Link'),
				'type' => 'text',
			),
			'label' => array(
				'title' => $this->l('Label'),
				'type' => 'text',
			),
			'link' => array(
				'title' => $this->l('Link'),
				'type' => 'link',
			),
			'new_window' => array(
				'title' => $this->l('New Window'),
				'type' => 'bool',
				'align' => 'center',
				'active' => 'status',
			)
		);
		
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_linksmenutop';
		$helper->table = 'linksmenutop';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->title = $this->l('Link list');	
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
				
		return $helper->generateList($links, $fields_list);
	}
}
