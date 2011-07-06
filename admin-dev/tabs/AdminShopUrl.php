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


class AdminShopUrl extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'shop_url';
	 	$this->className = 'ShopUrl';
	 	$this->edit = true;
		$this->delete = true;
	 	$this->_select = 's.name shop_name';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';
	 	$this->_listSkipDelete = array(1);

		$this->fieldsDisplay = array(
		'id_shop_url' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'domain' => array('title' => $this->l('Domain'), 'width' => 130, 'filter_key' => 'domain'),
		'domain_ssl' => array('title' => $this->l('Domain SSL'), 'width' => 130, 'filter_key' => 'domain'),
		'uri' => array('title' => $this->l('Uri'), 'width' => 130, 'filter_key' => 'uri'),
		'shop_name' => array('title' => $this->l('Shop name'), 'width' => 70),
		'main' => array('title' => $this->l('Main URL'), 'align' => 'center', 'activeVisu' => 'main', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'main'),
		'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'));
		$this->_fieldsOptions = array('_PS_DIRECTORY_' => array('title' => $this->l('PS directory'), 'desc' => $this->l('Name of the PrestaShop directory on your Web server, bracketed by forward slashes (e.g., /shop/)'), 'validation' => 'isUrl', 'type' => 'text', 'size' => 20, 'default' => _PS_DIRECTORY_, 'visibility' => Shop::CONTEXT_ALL));
		parent::__construct();
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$object = $this->loadObject(true);
			if ($object->id)
			{
				$beforeUpdate = new ShopUrl((int)$object->id);
				if ($beforeUpdate->main AND !Tools::getValue('main'))
					$this->_errors[] = Tools::displayError('You must have a main url per shop');
			}
			if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('uri')))
				$this->_errors[] = Tools::displayError('A shop url that use this domain and uri already exists');
				
			Tools::generateHtaccess(dirname(__FILE__).'/../../.htaccess', Configuration::get('PS_REWRITING_SETTINGS'), Configuration::get('PS_HTACCESS_CACHE_CONTROL'), Configuration::get('PS_HTACCESS_SPECIFIC'));
		}

		if (Tools::isSubmit('submitOptions'.$this->table))
		{
			$baseUrls = array();
			if ($_PS_DIRECTORY_ = Tools::getValue('_PS_DIRECTORY_'))
				$baseUrls['_PS_DIRECTORY_'] = $_PS_DIRECTORY_;
			rewriteSettingsFile($baseUrls, NULL, NULL);
			unset($this->_fieldsGeneral['_PS_DIRECTORY_']);
		}
		return parent::postProcess();
	}
	
	protected function afterUpdate($object)
	{
		if (Tools::getValue('main'))
			$object->setMain();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm($isMainTab = true);
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend>'.$this->l('Shop Url').'</legend>
				<label for="domain">'.$this->l('Domain').'</label>
				<div class="margin-form">
					<input type="text" name="domain" id="domain" value="'.$this->getFieldValue($obj, 'domain').'" />
				</div>
				<label for="domain">'.$this->l('Domain SSL').'</label>
				<div class="margin-form">
					<input type="text" name="domain_ssl" id="domain_ssl" value="'.$this->getFieldValue($obj, 'domain_ssl').'" />
				</div>
				<label for="uri">'.$this->l('URI').'</label>
				<div class="margin-form">
					<input type="text" name="uri" id="uri" value="'.$this->getFieldValue($obj, 'uri').'" />
					<p>'.$this->l('Folder of your store ex: ipods for http://yourshopname.com/ipods/, leave empty if no folder.').'<br /><b>'.$this->l('URL Rewrite must be activated to use this feature.').'</b></p>
				</div>
				<label for="id_shop">'.$this->l('Shop').'</label>
				<div class="margin-form">
					<select name="id_shop" id="id_shop">';
		foreach (Shop::getTree() AS $gID => $gData)
		{
			echo '<optgroup label="'.$gData['name'].'">';
			foreach ($gData['shops'] as $sID => $sData)
				echo '<option value="'.$sID.'" '.($obj->id_shop ==  $sID ? 'selected="selected"' : '').'>'.$sData['name'].'</option>';
			echo '</optgroup>';
		}
		echo '
					</select>
				</div>
				<label>'.$this->l('Main URL:').' </label>
				<div class="margin-form">
					<input type="radio" name="main" id="main_on" value="1" '.($this->getFieldValue($obj, 'main') ? 'checked="checked" ' : '').'/>
					<label class="t" for="main_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="main" id="main_off" value="0" '.(!$this->getFieldValue($obj, 'main') ? 'checked="checked" ' : '').'/>
					<label class="t" for="main_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Set this url as main, all urls set to this shop will be redirected to this url.').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enable or disable URL').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}
