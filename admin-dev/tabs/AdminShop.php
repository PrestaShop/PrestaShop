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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class AdminShop extends AdminTab
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'shop';
	 	$this->className = 'Shop';
	 	$this->edit = true;
		$this->delete = false;
		$this->deleted = false;
		
	 	$this->_select = 'gs.name group_shop_name, cl.name category_name';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'group_shop` gs ON (a.id_group_shop = gs.id_group_shop)
	 						 LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (a.id_category = cl.id_category AND cl.id_lang='.(int)$this->context->language->id.')';
	 	$this->_group = 'GROUP BY a.id_shop';

		$this->fieldsDisplay = array(
			'id_shop' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Shop'), 'width' => 130, 'filter_key' => 'b!name'),
			'group_shop_name' => array('title' => $this->l('Group Shop'), 'width' => 70),
			'category_name' => array('title' => $this->l('Category Root'), 'width' => 70),
			'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'),
		);

		$this->optionsList = array(
			'general' => array(
				'title' =>	$this->l('Shops options'),
				'fields' =>	array(
					'PS_SHOP_DEFAULT' => array('title' => $this->l('Default shop:'), 'desc' => $this->l('The default shop'), 'cast' => 'intval', 'type' => 'select', 'identifier' => 'id_shop', 'list' => Shop::getShops(), 'visibility' => Shop::CONTEXT_ALL)
				),
			),
		);
		parent::__construct();
	}

	public function afterAdd($newShop)
	{
		if (Tools::getValue('useImportData') && ($importData = Tools::getValue('importData')) && is_array($importData))
			$newShop->copyShopData((int)Tools::getValue('importFromShop'), $importData);
	}
	
	public function afterUpdate($newShop)
	{
		if (Tools::getValue('useImportData') && ($importData = Tools::getValue('importData')) && is_array($importData))
			$newShop->copyShopData((int)Tools::getValue('importFromShop'), $importData);
	}

	public function postProcess()
	{
		if ((Tools::isSubmit('status') || Tools::isSubmit('status'.$this->table) || (Tools::isSubmit('submitAdd'.$this->table) && Tools::getValue($this->identifier) && !Tools::getValue('active'))) && $this->loadObject() && $this->loadObject()->active)
		{
			if (Tools::getValue('id_shop') == Configuration::get('PS_SHOP_DEFAULT'))
				$this->_errors[] = Tools::displayError('You cannot disable the default shop.');
			else if (Shop::getTotalShops() == 1)
				$this->_errors[] = Tools::displayError('You cannot disable the last shop.');
		}
		
		if ($this->_errors)
			return false;
		return parent::postProcess();
	}

	public function displayConf()
	{
		if ($conf = Tools::getValue('conf'))
		{
			if ($conf == 3)
				echo '
				<div class="conf">
					<img src="../img/admin/ok2.png" alt="" /> <a href="index.php?tab=AdminShopUrl&addshop_url&token='.Tools::getAdminToken('AdminShopUrl'.Tab::getIdFromClassName('AdminShopUrl').(int)$this->context->employee->id).'">'.$this->l('Your store has been successfully created. To make your store accessible on front office, you must create a URL for your store by clicking on this text.').'</a>
				</div>';
			else
				parent::displayConf();
		}
	}

	public function displayForm($isMainTab = true)
	{
		parent::displayForm($isMainTab);

		if (!($obj = $this->loadObject(true)))
			return;

		$disabled = '';
		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = 'disabled="disabled"';

		echo '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend>'.$this->l('Shop').'</legend>
				<span class="hint" name="help_box" style="display:block;">'.$this->l('You can\'t change the GroupShop when you have more than one Shop').'</span><br />
				<label for="name">'.$this->l('Shop name').'</label>
				<div class="margin-form">
					<input type="text" name="name" id="name" value="'.$this->getFieldValue($obj, 'name').'" />
				</div>
				<label for="id_group_shop">'.$this->l('Group Shop').'</label>
				<div class="margin-form">';
				if ($disabled)
				{
					$groupShop = new GroupShop($obj->id_group_shop);
					echo $groupShop->name;
					echo '<input type="hidden" name="id_group_shop" value="'.$obj->id_group_shop.'" />';
				}
				else
				{
					echo '<select '.$disabled.' name="id_group_shop" id="id_group_shop">';
					foreach (GroupShop::getGroupShops() AS $group)
						echo '<option value="'.(int)$group['id_group_shop'].'" '.($obj->id_group_shop ==  $group['id_group_shop'] ? 'selected="selected"' : '').'">'.$group['name'].'</option>';
					echo '</select>';
				}
		echo '		</div>';
		echo '<label for="id_category">'.$this->l('Category root').'</label>
					<div class="margin-form">
						<select id="id_category" name="id_category">';
		$categories = Category::getCategories($this->context->language->id, false);
		Category::recurseCategory($categories, $categories[0][1], 1, $obj->id_category);

		echo '		
						</select>
					</div>
			<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enable or disable shop').'</p>
				</div>';

		// Theme list
		echo '<label for="id_theme">'.$this->l('Theme').'</label>
				<div class="margin-form">';
		foreach (Theme::getThemes() as $i => $theme)
		{
			$checked = ((!$obj->id && $i == 0) || $obj->id_theme == $theme['id_theme']) ? true : false;
			echo '<div class="select_theme '.(($checked) ? 'select_theme_choice' : '').'" onclick="$(this).find(\'input\').attr(\'checked\', true); $(\'.select_theme\').removeClass(\'select_theme_choice\'); $(this).toggleClass(\'select_theme_choice\');">';
				echo ucfirst($theme['name']).'<br />';
				echo '<img src="../themes/'.$theme['name'].'/preview.jpg" alt="'.$theme['name'].'" /><br />';
				echo '<input type="radio" name="id_theme" value="'.$theme['id_theme'].'" '.(($checked) ? 'checked="checked"' : '').' />';
			echo '</div>';
		}
		echo	'</div><div class="clear"></div>';

		echo '<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset><br /><br />';

		$importData = array(
			'carrier' => $this->l('Carriers'),
			'carrier_lang' => $this->l('Carriers lang'),
			'category_lang' => $this->l('Category lang'),
			'cms' => $this->l('CMS page'),
			'contact' => $this->l('Contact'),
			'country' => $this->l('Countries'),
			'currency' => $this->l('Currencies'),
			'discount' => $this->l('Discounts'),
			'image' => $this->l('Images'),
			'lang' => $this->l('Langs'),
			'manufacturer' => $this->l('Manufacturers'),
			'module' => $this->l('Modules'),
			'hook_module' => $this->l('Modules hook'),
			'hook_module_exceptions' => $this->l('Modules hook exceptions'),
			'meta_lang' => $this->l('Meta'),
			'module_country' => $this->l('Payment module country restrictions'),
			'module_group' => $this->l('Payment module customer group restrictions'),
			'module_currency' => $this->l('Payment module currency restrictions'),
			'product' => $this->l('Products'),
			'product_lang' => $this->l('Products lang'),
			'scene' => $this->l('Scenes'),
			'stock' => $this->l('Stock'),
			'store' => $this->l('Stores'),
		);

		$checked = (Tools::getValue('addshop') !== false) ? true : false;
		echo '<fieldset><legend>'.$this->l('Import data from another shop').'</legend>';
		echo '<label>'.$this->l('Import data from another shop').'</label>';
		echo '<div class="margin-form">';
			echo '<input type="checkbox" value="1" '.(($checked) ? 'checked="checked"' : '').' name="useImportData" onclick="$(\'#importList\').slideToggle(\'slow\')" /> ';
			echo $this->l('Duplicate data from shop');
			echo ' <select name="importFromShop">';
			foreach (Shop::getTree() as $gID => $gData)
			{
				echo '<optgroup label="'.$gData['name'].'">';
				foreach ($gData['shops'] as $sID => $sData)
					echo '<option value="'.(int)$sID.'" '.($sID == Configuration::get('PS_SHOP_DEFAULT') ? 'selected="selected"' : '').'">'.$sData['name'].'</option>';
				echo '</optgroup>';
			}
			echo '</select>';
			echo '<div id="importList" style="'.((!$checked) ? 'display: none' : '').'"><ul>';
			foreach ($importData as $table => $lang)
				echo '<li><label><input type="checkbox" name="importData['.$table.']" checked="checked" /> '.$lang.'</label></li>';
			echo '</ul></div>';
			echo '<p>'.$this->l('Use this option to associate data (products, modules, etc.) the same way as the selected shop').'</p>';
		echo '</div><div class="margin-form">
				<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
			</div>';
		echo '</fieldset>';

		echo '</form>';
	}

	protected function displayAddButton()
	{
		echo '<br /><a href="'.self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add new shop').'</a><br /><br />';
	}
}
