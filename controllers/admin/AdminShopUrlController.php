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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShopUrlControllerCore extends AdminController
{
	public $display_multishop_toolbar = false;

	public function __construct()
	{
	 	$this->table = 'shop_url';
		$this->className = 'ShopUrl';
	 	$this->lang = false;
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldsDisplay = array(
			'id_shop_url' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'shop_name' => array(
				'title' => $this->l('Shop name'),
				'width' => 150,
				'filter_key' => 's!name'
			),
			'domain' => array(
				'title' => $this->l('Domain'),
				'width' => 'auto',
				'filter_key' => 'domain'
			),
			'domain_ssl' => array(
				'title' => $this->l('Domain SSL'),
				'width' => 130,
				'filter_key' => 'domain'
			),
			'uri' => array(
				'title' => $this->l('Uri'),
				'width' => 200,
				'filter_key' => 'uri',
				'havingFilter' => true
			),
			'main' => array(
				'title' => $this->l('Main URL'),
				'align' => 'center',
				'activeVisu' => 'main',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'main',
				'width' => 100,
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'active',
				'width' => 50,
			),
		);
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowActionSkipList('delete', array(1));

		$this->addRowAction('edit');
		$this->addRowAction('delete');

	 	$this->_select = 's.name AS shop_name, CONCAT(a.physical_uri, a.virtual_uri) AS uri';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';

	 	return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Shop Url')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Domain:'),
					'name' => 'domain',
					'size' => 50,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Domain SSL:'),
					'name' => 'domain_ssl',
					'size' => 50,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Physical URI:'),
					'name' => 'physical_uri',
					'desc' => $this->l('Physical folder of your store on your server. Leave this field empty if your store is installed on root path. E.g. if your store is available from www.my-prestashop.com/my-store/, you have to set my-store/ in this field.'),
					'size' => 50,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Virtual URI:'),
					'name' => 'virtual_uri',
					'desc' => array(
						$this->l('You can use this option if you want to create a store with an URI that doesn\'t exist on your server. E.g. if you want your store to be available with url www.my-prestashop.com/my-store/shoes/, you have to set shoes/ in this field (we considere that my-store/ is your physical URI).'),
						'<strong>'.$this->l('URL rewriting must be activated on your server to use this feature.').'</strong>'
					),
					'size' => 50,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Your final URL will be:'),
					'name' => 'final_url',
					'size' => 76,
					'readonly' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Shop:'),
					'name' => 'id_shop',
					'onchange' => 'checkMainUrlInfo(this.value);',
					'options' => array(
						'optiongroup' => array (
							'query' =>  Shop::getTree(),
							'label' => 'name'
						),
						'options' => array (
							'query' => 'shops',
							'id' => 'id_shop',
							'name' => 'name'
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Main URL:'),
					'name' => 'main',
					'class' => 't',
					'values' => array(
						array(
							'id' => 'main_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'main_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					),
					'desc' => array(
						$this->l('If you set this url as main url for selected shop, all urls set to this shop will be redirected to this url (you can only have one main url per shop).'),
						array(
							'text' => $this->l('Since the selected shop has no main url, you have to set this url as main'),
							'id' => 'mainUrlInfo'
						),
						array(
							'text' => $this->l('The selected shop has already a main url, if you set this one as main url, the older one will be set as normal url'),
							'id' => 'mainUrlInfoExplain'
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					),
					'desc' => $this->l('Enabled or disabled')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;
		$current_shop = Shop::initialize();

		$list_shop_with_url = array();
		foreach (Shop::getShops(false, null, true) as $id)
			$list_shop_with_url[$id] = (bool)count(ShopUrl::getShopUrls($id));

		$this->tpl_form_vars = array(
			'js_shop_url' => Tools::jsonEncode($list_shop_with_url)
		);

		$this->fields_value = array(
			'domain' => Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain') : $current_shop->domain,
			'domain_ssl' => Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain_ssl') : $current_shop->domain_ssl,
			'physical_uri' => Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'physical_uri') : $current_shop->physical_uri,
			'active' => true
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

		if ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->main)
						$this->errors[] = Tools::displayError('You can\'t disable a main url');
					elseif ($object->toggleStatus())
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$token);
					else
						$this->errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitAdd'.$this->table) && $this->tabAccess['add'] === '1')
		{
			if (ShopUrl::virtualUriExists(Tools::getValue('virtual_uri'), Tools::getValue('id_shop')))
				$this->errors[] = Tools::displayError('Virtual URI already used.');
			else
				return parent::postProcess();
		}
		else
			return parent::postProcess();
	}

	public function processAdd($token)
	{
			$object = $this->loadObject(true);
			if ($object->id && Tools::getValue('main'))
				$object->setMain();

			if ($object->main && !Tools::getValue('main'))
				$this->errors[] = Tools::displayError('You can\'t change a main url to a non main url, you have to set an other url as main url for selected shop');

			if (($object->main || Tools::getValue('main')) && !Tools::getValue('active'))
				$this->errors[] = Tools::displayError('You can\'t disable a main url');

			if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri')))
				$this->errors[] = Tools::displayError('A shop url that use this domain and uri already exists');

			parent::processAdd($token);
			if (!$this->errors)
				Tools::generateHtaccess();
	}

	protected function afterUpdate($object)
	{
		if (Tools::getValue('main'))
			$object->setMain();
	}
}


