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
*  @version  Release: $Revision: 7095 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require dirname(__FILE__).'/menutoplinks.class.php';

class blocktopmenu extends Module
{
	private $_menu = '';
	private $_html = '';

	public function __construct()
	{
		$this->name = 'blocktopmenu';
		$this->tab = 'front_office_features';
		$this->version = 1.4;
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Top horizontal menu');
		$this->description = $this->l('Add a new menu on top of your shop.');
	}

	public function install()
	{
		if(!parent::install() ||
			!$this->registerHook('top') ||
			!Configuration::updateGlobalValue('MOD_BLOCKTOPMENU_ITEMS', 'CAT1,CMS1,CMS2,PRD1') ||
			!Configuration::updateGlobalValue('MOD_BLOCKTOPMENU_SEARCH', '1') ||
			!$this->installDB())
			return false;
		return true;
	}

	public function installDb()
	{
		return (Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'linksmenutop` (
			`id_linksmenutop` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_shop` INT UNSIGNED NOT NULL,
			`new_window` TINYINT( 1 ) NOT NULL,
			`link` VARCHAR( 128 ) NOT NULL,
			INDEX (`id_shop`)
		) ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;') AND
		Db::getInstance()->execute('
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'linksmenutop_lang` (
			`id_linksmenutop` INT NOT NULL,
			`id_lang` INT NOT NULL,
			`id_shop` INT NOT NULL,
			`label` VARCHAR( 128 ) NOT NULL ,
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
		global $cookie;
		if(Tools::isSubmit('submitBlocktopmenu'))
		{
			if (Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', Tools::getValue('items')))
				$this->_html .= $this->displayConfirmation($this->l('Settings Updated'));
			else
				$this->_html .= $this->displayError($this->l('Unable to update settings'));
			Configuration::updateValue('MOD_BLOCKTOPMENU_SEARCH', (bool)Tools::getValue('search'));
		}
		if(Tools::isSubmit('submitBlocktopmenuLinks'))
		{
			if(Tools::getValue('link') == '')
			{
				$this->_html .= $this->displayError($this->l('Unable to add this link'));
			}
			else
			{
				MenuTopLinks::add(Tools::getValue('link'), Tools::getValue('label'), Tools::getValue('new_window', 0), (int)$this->context->shop->id);
				$this->_html .= $this->displayConfirmation($this->l('The link has been added'));
			}
		}
		if(Tools::isSubmit('submitBlocktopmenuRemove'))
		{
			$id_linksmenutop = Tools::getValue('id_linksmenutop', 0);
			MenuTopLinks::remove($id_linksmenutop, (int)$this->context->shop->id);
			Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', str_replace(array('LNK'.$id_linksmenutop.',', 'LNK'.$id_linksmenutop), '', Configuration::get('MOD_BLOCKTOPMENU_ITEMS')));
			$this->_html .= $this->displayConfirmation($this->l('The link has been removed'));
		}
		
		$this->_html .= '
		<fieldset>
			<div class="multishop_info">
			'.$this->l('The modifications will be applied to').' '.(Shop::getContext() == Shop::CONTEXT_SHOP ? $this->l('shop:').' '.$this->context->shop->name : $this->l('all shops')).'.
			</div>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="form">
				<div style="display: none">
				<label>'.$this->l('Items').'</label>
				<div class="margin-form">
					<input type="text" name="items" id="itemsInput" value="'.Configuration::get('MOD_BLOCKTOPMENU_ITEMS').'" size="70" />
				</div>
				</div>

				<div class="clear">&nbsp;</div>
				<table style="margin-left: 130px;">
					<tbody>
						<tr>
							<td>
								<select multiple="multiple" id="items" style="width: 300px; height: 160px;">';
								$this->makeMenuOption();
								$this->_html .= '</select><br/>
								<br/>
								<a href="#" id="removeItem" style="border: 1px solid rgb(170, 170, 170); margin: 2px; padding: 2px; text-align: center; display: block; text-decoration: none; background-color: rgb(250, 250, 250); color: rgb(18, 52, 86);">'.$this->l('Remove').' &gt;&gt;</a>
							</td>
							<td style="padding-left: 20px;">
								<select multiple="multiple" id="availableItems" style="width: 300px; height: 160px;">';
								// BEGIN CMS
								$this->_html .= '<optgroup label="'.$this->l('CMS').'">';
								$_cms = CMS::listCms($cookie->id_lang);
								foreach($_cms as $cms)
									$this->_html .= '<option value="CMS'.$cms['id_cms'].'" style="margin-left:10px;">'.$cms['meta_title'].'</option>';
								$this->_html .= '</optgroup>';
								// END CMS
								// BEGIN SUPPLIER
								$this->_html .= '<optgroup label="'.$this->l('Supplier').'">';
								$suppliers = Supplier::getSuppliers(false, $cookie->id_lang);
								foreach($suppliers as $supplier)
									$this->_html .= '<option value="SUP'.$supplier['id_supplier'].'" style="margin-left:10px;">'.$supplier['name'].'</option>';
								$this->_html .= '</optgroup>';
								// END SUPPLIER
								// BEGIN Manufacturer
								$this->_html .= '<optgroup label="'.$this->l('Manufacturer').'">';
								$manufacturers = Manufacturer::getManufacturers(false, $cookie->id_lang);
								foreach($manufacturers as $manufacturer)
									$this->_html .= '<option value="MAN'.$manufacturer['id_manufacturer'].'" style="margin-left:10px;">'.$manufacturer['name'].'</option>';
								$this->_html .= '</optgroup>';
								// END Manufacturer
								// BEGIN Categories
								$this->_html .= '<optgroup label="'.$this->l('Categories').'">';
								$this->getCategoryOption(1, $cookie->id_lang);
								$this->_html .= '</optgroup>';
								// END Categories
								// BEGIN Products
								$this->_html .= '<optgroup label="'.$this->l('Products').'">';
									$this->_html .= '<option value="PRODUCT" style="margin-left:10px;font-style:italic">'.$this->l('Choose ID product').'</option>';
								$this->_html .= '</optgroup>';
								// END Products
								// BEGIN Menu Top Links
								$this->_html .= '<optgroup label="'.$this->l('Menu Top Links').'">';
								$links = MenuTopLinks::gets($cookie->id_lang, null, (int)$this->context->shop->id);
								foreach($links as $link)
									$this->_html .= '<option value="LNK'.$link['id_linksmenutop'].'" style="margin-left:10px;">'.$link['label'].'</option>';
								$this->_html .= '</optgroup>';
								// END Menu Top Links
								$this->_html .= '</select><br />
								<br />
								<a href="#" id="addItem" style="border: 1px solid rgb(170, 170, 170); margin: 2px; padding: 2px; text-align: center; display: block; text-decoration: none; background-color: rgb(250, 250, 250); color: rgb(18, 52, 86);">&lt;&lt; '.$this->l('Add').'</a>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="clear">&nbsp;</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#addItem").click(add);
					$("#availableItems").dblclick(add);
					$("#removeItem").click(remove);
					$("#items").dblclick(remove);
					function add()
					{
						$("#availableItems option:selected").each(function(i){
							var val = $(this).val();
							var text = $(this).text();
							if(val == "PRODUCT")
							{
								val = prompt("'.$this->l('Set ID product').'");
								if(val == null || val == "" || isNaN(val))
									return;
								text = "'.$this->l('Product ID').' "+val;
								val = "PRD"+val;
							}
							$("#items").append("<option value=\""+val+"\">"+text+"</option>");
						});
						serialize();
						return false;
					}
					function remove()
					{
						$("#items option:selected").each(function(i){
							$(this).remove();
						});
						serialize();
						return false;
					}
					function serialize()
					{
						var options = "";
						$("#items option").each(function(i){
							options += $(this).val()+",";
						});
						$("#itemsInput").val(options.substr(0, options.length - 1));
					}
				});
				</script>
				<label for="s">'.$this->l('Search Bar').'</label>
				<div class="margin-form">
					<input type="checkbox" name="search" id="s" value="1"'.((Configuration::get('MOD_BLOCKTOPMENU_SEARCH')) ? ' checked=""': '').'/>
				</div>
				<p class="center">
					<input type="submit" name="submitBlocktopmenu" value="'.$this->l('	Save	').'" class="button" />
				</p>
			</form>
		</fieldset><br />';

		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);
		$divLangName = 'link_label';
		$this->_html .= '
		<fieldset>
			<legend><img src="../img/admin/add.gif" alt="" title="" />'.$this->l('Add Menu Top Link').'</legend>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="form">
				<label>'.$this->l('Label').'</label>
				<div class="margin-form">';
				foreach ($languages as $language)
				{
					$this->_html .= '
					<div id="link_label_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="label['.$language['id_lang'].']" id="label_'.$language['id_lang'].'" size="70" value="" />
					</div>';
				 }
				$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'link_label', true);

				$this->_html .= '</div><p class="clear"> </p>
				<label>'.$this->l('Link').'</label>
				<div class="margin-form">
					<input type="text" name="link" value="" size="70" />
				</div>
				<label>'.$this->l('New Window').'</label>
				<div class="margin-form">
					<input type="checkbox" name="new_window" value="1" />
				</div>
				<p class="center">
					<input type="submit" name="submitBlocktopmenuLinks" value="'.$this->l('	Add	').'" class="button" />
				</p>
			</form>
		</fieldset><br />';

		$this->_html .= '
		<fieldset>
			<legend><img src="../img/admin/details.gif" alt="" title="" />'.$this->l('List Menu Top Link').'</legend>
			<table style="width:100%;">
				<thead>
					<tr>
						<th>'.$this->l('Id Link').'</th>
						<th>'.$this->l('Label').'</th>
						<th>'.$this->l('Link').'</th>
						<th>'.$this->l('New Window').'</th>
						<th>'.$this->l('Action').'</th>
					</tr>
				</thead>
				<tbody>';
				$links = MenuTopLinks::gets($cookie->id_lang, null, $this->context->shop->id);
				foreach($links as $link)
				{
					$this->_html .= '
					<tr>
						<td>'.$link['id_linksmenutop'].'</td>
						<td>'.$link['label'].'</td>
						<td>'.$link['link'].'</td>
						<td>'.(($link['new_window']) ? $this->l('Yes') : $this->l('No')).'</td>
						<td>
							<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
								<input type="hidden" name="id_linksmenutop" value="'.$link['id_linksmenutop'].'" />
								<input type="submit" name="submitBlocktopmenuRemove" value="'.$this->l('Remove').'" class="button" />
							</form>
						</td>
					</tr>';
				}
				$this->_html .= '</tbody>
			</table>
		</fieldset>';
		return $this->_html;
	}

	private function getMenuItems()
	{
		// @todo why this ?
		return array('global' => explode(',', Configuration::get('MOD_BLOCKTOPMENU_ITEMS')));
	}

	private function makeMenuOption()
	{
		global $cookie;
		foreach($this->getMenuItems() as $type => $items)
		{
			foreach ($items as $item)
			{
				$id = (int)substr($item, 3, strlen($item));
				$disabled = ((Shop::getContext() == Shop::CONTEXT_SHOP && $type == 'global') ? ' disabled="disabled"': '');
				switch(substr($item, 0, 3))
				{
					case'CAT':
						$this->getCategoryOption($id, $cookie->id_lang, false);
					break;
					case'PRD':
						$product = new Product($id, true, $cookie->id_lang);
						if(!is_null($product->id))
							$this->_html .= '<option value="PRD'.$id.'"'.$disabled.'>'.$product->name.'</option>'.PHP_EOL;
					break;
					case'CMS':
						$cms = CMS::getLinks($cookie->id_lang, array($id));
						if(count($cms))
							$this->_html .= '<option value="CMS'.$id.'"'.$disabled.'>'.$cms[0]['meta_title'].'</option>'.PHP_EOL;
					break;
					case'MAN':
						$manufacturer = new Manufacturer($id, $cookie->id_lang);
						if(!is_null($manufacturer->id))
							$this->_html .= '<option value="MAN'.$id.'"'.$disabled.'>'.$manufacturer->name.'</option>'.PHP_EOL;
					break;
					case'SUP':
						$supplier = new Supplier($id, $cookie->id_lang);
						if(!is_null($supplier->id))
							$this->_html .= '<option value="SUP'.$id.'"'.$disabled.'>'.$supplier->name.'</option>'.PHP_EOL;
					break;
					case'LNK':
						$link = MenuTopLinks::get($id, $cookie->id_lang, (int)$this->context->shop->id);
						if(count($link))
							$this->_html .= '<option value="LNK'.$id.'"'.$disabled.'>'.$link[0]['label'].'</option>'.PHP_EOL;
					break;
				}
			}
		}
	}

	private function makeMenu()
	{
		global $cookie, $page_name;
		foreach($this->getMenuItems() as $type => $items)
		{
			foreach ($items as $item)
			{
				$id = (int)substr($item, 3, strlen($item));
				switch(substr($item, 0, 3))
				{
					case'CAT':
						$this->getCategory($id, $cookie->id_lang);
					break;
					case'PRD':
						$selected = ($page_name == 'product' && (Tools::getValue('id_product') == $id)) ? ' class="sfHover"' : '';
						$product = new Product($id, true, $cookie->id_lang);
						if(!is_null($product->id))
							$this->_menu .= '<li'.$selected.'><a href="'.$product->getLink().'">'.$product->name.'</a></li>'.PHP_EOL;
					break;
					case'CMS':
						$selected = ($page_name == 'cms' && (Tools::getValue('id_cms') == $id)) ? ' class="sfHover"' : '';
						$cms = CMS::getLinks($cookie->id_lang, array($id));
						if(count($cms))
							$this->_menu .= '<li'.$selected.'><a href="'.$cms[0]['link'].'">'.$cms[0]['meta_title'].'</a></li>'.PHP_EOL;
					break;
					case'MAN':
						$selected = ($page_name == 'manufacturer' && (Tools::getValue('id_manufacturer') == $id)) ? ' class="sfHover"' : '';
						$manufacturer = new Manufacturer($id, $cookie->id_lang);
						if(!is_null($manufacturer->id))
						{
							if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
								$manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name, false);
							else
								$manufacturer->link_rewrite = 0;
							$link = new Link;
							$this->_menu .= '<li'.$selected.'><a href="'.$link->getManufacturerLink($id, $manufacturer->link_rewrite).'">'.$manufacturer->name.'</a></li>'.PHP_EOL;
						}
					break;
					case'SUP':
						$selected = ($page_name == 'supplier' && (Tools::getValue('id_supplier') == $id)) ? ' class="sfHover"' : '';
						$supplier = new Supplier($id, $cookie->id_lang);
						if(!is_null($supplier->id))
						{
							$link = new Link;
							$this->_menu .= '<li'.$selected.'><a href="'.$link->getSupplierLink($id, $supplier->link_rewrite).'">'.$supplier->name.'</a></li>'.PHP_EOL;
						}
					break;
					case'LNK':
						$link = MenuTopLinks::get($id, $cookie->id_lang, (int)$this->context->shop->id);
						if(count($link))
							$this->_menu .= '<li><a href="'.$link[0]['link'].'"'.(($link[0]['new_window']) ? ' target="_blank"': '').'>'.$link[0]['label'].'</a></li>'.PHP_EOL;
					break;
				}
			}
		}
	}

	private function getCategoryOption($id_category, $id_lang, $children = true)
	{
		$categorie = new Category($id_category, $id_lang);
		if(is_null($categorie->id))
			return;
		if(count(explode('.', $categorie->name)) > 1)
			$name = str_replace('.', '', strstr($categorie->name, '.'));
		else
			$name = $categorie->name;
		$this->_html .= '<option value="CAT'.$categorie->id.'" style="margin-left:'.(($children) ? round(15+(15*(int)$categorie->level_depth)) : 0).'px;">'.$name.'</option>';
		if($children)
		{
			$childrens = Category::getChildren($id_category, $id_lang);
			if(count($childrens))
				foreach($childrens as $children)
					$this->getCategoryOption($children['id_category'], $id_lang);
		}
	}

	private function getCategory($id_category, $id_lang)
	{
		global $page_name;

		$categorie = new Category($id_category, $id_lang);
		if(is_null($categorie->id))
			return;
		$selected = ($page_name == 'category' && ((int)Tools::getValue('id_category') == $id_category)) ? ' class="sfHoverForce"' : '';
		$this->_menu .= '<li'.$selected.'>';
		if(count(explode('.', $categorie->name)) > 1)
			$name = str_replace('.', '', strstr($categorie->name, '.'));
		else
			$name = $categorie->name;
		$this->_menu .= '<a href="'.$categorie->getLink().'">'.$name.'</a>';
		$childrens = Category::getChildren($id_category, $id_lang);
		if(count($childrens))
		{
			$this->_menu .= '<ul>';
			foreach($childrens as $children)
				$this->getCategory($children['id_category'], $id_lang);
			$this->_menu .= '</ul>';
		}
		$this->_menu .= '</li>';
	}

	public function hookTop($param)
	{
		global $smarty;
		$this->makeMenu();
		$smarty->assign('MENU_SEARCH', Configuration::get('MOD_BLOCKTOPMENU_SEARCH'));
		$smarty->assign('MENU', $this->_menu);
		$smarty->assign('this_path', $this->_path);
		return $this->display(__FILE__, 'blocktopmenu.tpl');
	}
}