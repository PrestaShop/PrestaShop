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
	 	$this->_select = 's.name AS shop_name, CONCAT(a.physical_uri, a.virtual_uri) AS uri';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';
	 	$this->_listSkipDelete = array(1);

		$this->fieldsDisplay = array(
			'id_shop_url' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'domain' => array('title' => $this->l('Domain'), 'width' => 130, 'filter_key' => 'domain'),
			'domain_ssl' => array('title' => $this->l('Domain SSL'), 'width' => 130, 'filter_key' => 'domain'),
			'uri' => array('title' => $this->l('Uri'), 'width' => 130, 'filter_key' => 'uri'),
			'shop_name' => array('title' => $this->l('Shop name'), 'width' => 70),
			'main' => array('title' => $this->l('Main URL'), 'align' => 'center', 'activeVisu' => 'main', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'main'),
			'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'),
		);
		parent::__construct();
	}
	
	public function postProcess()
	{
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$object = $this->loadObject(true);
			if ($object->id && Tools::getValue('main'))
				$object->setMain();
				
			if ($object->main && !Tools::getValue('main'))
				$this->_errors[] = Tools::displayError('You can\'t change a main url to a non main url, you have to set an other url as main url for selected shop');
				
			if (($object->main || Tools::getValue('main')) && !Tools::getValue('active'))
				$this->_errors[] = Tools::displayError('You can\'t disable a main url');

			if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri')))
				$this->_errors[] = Tools::displayError('A shop url that use this domain and uri already exists');
			
			parent::postProcess();
			Tools::generateHtaccess(dirname(__FILE__).'/../../.htaccess', Configuration::get('PS_REWRITING_SETTINGS'), Configuration::get('PS_HTACCESS_CACHE_CONTROL'), '');
		}
		elseif ((isset($_GET['status'.$this->table]) OR isset($_GET['status'])) AND Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->main)
						$this->_errors[] = Tools::displayError('You can\'t disable a main url');
					else if ($object->toggleStatus())
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$token);
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else
			return parent::postProcess();
	}
	
	protected function afterUpdate($object)
	{
		if (Tools::getValue('main'))
			$object->setMain();
	}
	
	public function displayForm($isMainTab = true)
	{
		parent::displayForm($isMainTab = true);

		if (!($obj = $this->loadObject(true)))
			return;
		$currentShop = Shop::initialize();

		$listShopWithUrl = array();
		foreach (Shop::getShops(false, null, true) as $id)
			$listShopWithUrl[$id] = (bool)count(ShopUrl::getShopUrls($id));
		$jsShopUrl = Tools::jsonEncode($listShopWithUrl);

		echo <<<EOF
		<script type="text/javascript">
		//<![CDATA[
		function fillShopUrl()
		{
			var domain = $('#domain').val();
			var physical = $('#physical_uri').val();
			var virtual = $('#virtual_uri').val();
			url = ((domain) ? domain : '???');
			if (physical)
				url += '/'+physical;
			if (virtual)
				url += '/'+virtual;
			url = url.replace(/\/+/g, "/");
			$('#final_url').val('http://'+url);
		};

		var shopUrl = {$jsShopUrl};
		function checkMainUrlInfo(shopID)
		{
			if (!shopID)
				shopID = $('#id_shop').val();

			if (!shopUrl[shopID])
			{
				$('#main_off').attr('disabled', true);
				$('#main_on').attr('checked', true);
				$('#mainUrlInfo').css('display', 'block');
				$('#mainUrlInfoExplain').css('display', 'none');
			}
			else
			{
				$('#main_off').attr('disabled', false);
				$('#mainUrlInfo').css('display', 'none');
				$('#mainUrlInfoExplain').css('display', 'block');
			}
		}

		$().ready(function()
		{
			fillShopUrl();
			checkMainUrlInfo();
			$('#domain, #physical_uri, #virtual_uri').keyup(fillShopUrl);
		});
		//]]>
		</script>
EOF;

		echo '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend>'.$this->l('Shop Url').'</legend>
				<label for="domain">'.$this->l('Domain').'</label>
				<div class="margin-form">
					<input type="text" name="domain" id="domain" value="'.((Validate::isLoadedObject($obj)) ? $this->getFieldValue($obj, 'domain') : $currentShop->domain).'" />
				</div>
				<label for="domain">'.$this->l('Domain SSL').'</label>
				<div class="margin-form">
					<input type="text" name="domain_ssl" id="domain_ssl" value="'.((Validate::isLoadedObject($obj)) ? $this->getFieldValue($obj, 'domain_ssl') : $currentShop->domain_ssl).'" />
				</div>
				<label for="physical_uri">'.$this->l('Physical URI').'</label>
				<div class="margin-form">
					<input type="text" name="physical_uri" id="physical_uri" value="'.((Validate::isLoadedObject($obj)) ? $this->getFieldValue($obj, 'physical_uri') : $currentShop->physical_uri).'" />
					<p>'.$this->l('Physical folder of your store on your server. Leave this field empty if your store is installed on root path.').'</p>
				</div>';
			echo '<label for="virtual_uri">'.$this->l('Virtual URI').'</label>
				<div class="margin-form">
					<input type="text" name="virtual_uri" id="virtual_uri" value="'.$this->getFieldValue($obj, 'virtual_uri').'" />
					<p>'.$this->l('This virtual folder must not exist on your server and is used to associate an URI to a shop.').'<br /><b>'.$this->l('URL rewriting must be activated on your server to use this feature.').'</b></p>
				</div>';
		echo '	<label>'.$this->l('Your final URL will be').'</label>
				<div class="margin-form">
					<input type="text" readonly="readonly" id="final_url" style="width: 400px" /> 
				</div>
				<label for="id_shop">'.$this->l('Shop').'</label>
				<div class="margin-form">
					<select name="id_shop" id="id_shop" onchange="checkMainUrlInfo(this.value)">';
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
					<p>'.$this->l('If you set this url as main url for selected shop, all urls set to this shop will be redirected to this url (you can only have one main url per shop).').'</p>
					<p id="mainUrlInfo">'.$this->l('Since the selected shop has no main url, you have to set this url as main').'</p>
					<p id="mainUrlInfoExplain">'.$this->l('The selected shop has already a main url, if you set this one as main url, the older one will be set as normal url').'</p>
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
	
	protected function displayAddButton()
	{
		echo '<br /><a href="'.self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'"><img src="../img/admin/add.gif" border="0" /> '.$this->l('Add new shop URL').'</a><br /><br />';
	}
}
