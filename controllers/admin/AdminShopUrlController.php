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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShopUrlControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'shop_url';
		$this->className = 'ShopUrl';
	 	$this->lang = false;
		$this->requiredDatabase = true;
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->id_shop = Tools::getValue('id_shop');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fields_list = array(
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
			'url' => array(
				'title' => $this->l('URL'),
				'filter_key' => 'url',
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

		parent::__construct();
	}

	public function viewAccess($disable = false)
	{
		return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
	}

	public function renderList()
	{
		$this->addRowActionSkipList('delete', array(1));

		$this->addRowAction('edit');
		$this->addRowAction('delete');

	 	$this->_select = 's.name AS shop_name, CONCAT(\'http://\', a.domain, a.physical_uri, a.virtual_uri) AS url';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';

		if ($id_shop = (int)Tools::getValue('id_shop'))
			$this->_where = 'AND a.id_shop = '.$id_shop;

	 	return parent::renderList();
	}

	public function renderForm()
	{
		$update_htaccess = Tools::modRewriteActive() && ((file_exists('.htaccess') && is_writable('.htaccess')) || is_writable(dirname('.htaccess')));

		$this->multiple_fieldsets = true;
		if (!$update_htaccess)
			$desc_virtual_uri = array(
				'<span class="warning_mod_rewrite">'.$this->l('You need to activate the URL Rewriting if you want to add a virtual URI.').'</span>'
			);
		else
			$desc_virtual_uri = array(
				$this->l('You can use this option if you want to create a store with a URI that doesn\'t exist on your server (e.g. if you want your store to be available with the URL www.my-prestashop.com/my-store/shoes/, you have to set shoes/ in this field, assuming that my-store/ is your Physical URI).'),
				'<strong>'.$this->l('URL rewriting must be activated on your server to use this feature.').'</strong>'
			);
		$this->fields_form = array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('URL options')
					),
					'input' => array(
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
								$this->l('If you set this URL as the Main URL for the selected shop, all URLs set to this shop will be redirected to this URL (you can only have one Main URL per shop).'),
								array(
									'text' => $this->l('Since the selected shop has no Main URL, you have to set this URL as the Main URL'),
									'id' => 'mainUrlInfo'
								),
								array(
									'text' => $this->l('The selected shop has already a Main URL, if you set this one as the Main URL, the older one will be set as the Normal URL.'),
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
						'title' => $this->l('Save'),
						'class' => 'button'
					),
				),
			),
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Shop URL')
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
							'desc' => $this->l('Physical folder of your store on your server. Leave this field empty if your store is installed on the root path (e.g. if your store is available at www.my-prestashop.com/my-store/, you would set my-store/ in this field).'),
							'size' => 50,
						),
						array(
							'type' => 'text',
							'label' => $this->l('Virtual URI:'),
							'name' => 'virtual_uri',
							'desc' => $desc_virtual_uri,
							'size' => 50,
							'hint' => (!$update_htaccess) ? $this->l('Warning: URL rewriting (e.g. mod_rewrite for Apache) seems to be disabled. If your URL don\'t work, please check with your host provider how to activate URL rewriting.') : '',
						),
						array(
							'type' => 'text',
							'label' => $this->l('Your final URL will be:'),
							'name' => 'final_url',
							'size' => 76,
							'readonly' => true
						),
					),
				),
			),
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

	public function initToolbar()
	{
		if ($this->id_object)
			$this->loadObject();

		if (!$this->id_shop && $this->object && $this->object->id_shop)
			$this->id_shop = $this->object->id_shop;

		if (!$this->display && $this->id_shop)
			$this->toolbar_btn['edit'] = array(
				'desc' => $this->l('Edit this shop'),
				'href' => $this->context->link->getAdminLink('AdminShop').'&amp;updateshop&amp;id_shop='.$this->id_shop,
			);

		parent::initToolbar();

		$this->show_toolbar = false;
		if (isset($this->toolbar_btn['new']))
			$this->toolbar_btn['new'] = array(
				'desc' => $this->l('Add new URL'),
				'href' => $this->context->link->getAdminLink('AdminShopUrl').'&amp;add'.$this->table.'&amp;id_shop='.$this->id_shop,
			);

		if (isset($this->toolbar_btn['back']))
			$this->toolbar_btn['back']['href'] .= '&amp;id_shop='.$this->id_shop;
	}

	public function initContent()
	{
		$this->list_simple_header = true;
		parent::initContent();

		$this->addJqueryPlugin('cookie-plugin');
		$this->addJqueryPlugin('jstree');
		$this->addCSS(_PS_JS_DIR_.'jquery/plugins/jstree/themes/classic/style.css');

		if (!$this->display && $this->id_shop)
		{
			$shop = new Shop($this->id_shop);
			$this->toolbar_title[] = $shop->name;
		}

		$this->context->smarty->assign(array(
			'toolbar_scroll' => 1,
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->toolbar_title,
			'selected_tree_id' => ($this->display == 'edit' ? 'tree-url-'.$this->id_object : (Tools::getValue('id_shop') ? 'tree-shop-'.Tools::getValue('id_shop') : '')),
		));
	}

	public function postProcess()
	{
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

		$result = true;
		if ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->main)
						$this->errors[] = Tools::displayError('You can\'t disable a Main URL');
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
		else
			$result = parent::postProcess();

		if ($this->redirect_after)
			$this->redirect_after .= '&id_shop='.$this->id_shop;

		return $result;
	}

	public function processSave()
	{
		$object = $this->loadObject(true);
		if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri')))
			$this->errors[] = Tools::displayError('A shop URL that use this domain and uri already exists');

		$return = parent::processSave();
		if (!$this->errors)
			Tools::generateHtaccess();

		return $return;
	}

	public function processAdd()
	{
		$object = $this->loadObject(true);
		if ($object->id && Tools::getValue('main'))
			$object->setMain();

		if ($object->main && !Tools::getValue('main'))
			$this->errors[] = Tools::displayError('You can\'t change a Main URL to a non-Main URL, you have to set another URL as Main URL for selected shop');

		if (($object->main || Tools::getValue('main')) && !Tools::getValue('active'))
			$this->errors[] = Tools::displayError('You can\'t disable a Main URL');

		return parent::processAdd();
	}

	public function processUpdate()
	{
		$this->redirect_shop_url = false;
		$current_url = parse_url($_SERVER['REQUEST_URI']);
		if (trim(dirname(dirname($current_url['path'])), '/') == trim($this->object->getBaseURI(), '/'))
			$this->redirect_shop_url = true;

		return parent::processUpdate();
	}

	protected function afterUpdate($object)
	{
		if (Tools::getValue('main'))
			$object->setMain();

		if ($this->redirect_shop_url)
			$this->redirect_after = $object->getBaseURI().basename(_PS_ADMIN_DIR_).'/'.$this->context->link->getAdminLink('AdminShopUrl');
	}
}


