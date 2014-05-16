<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminOrderPreferencesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		// List of CMS tabs
		$cms_tab = array(0 => array(
			'id' => 0,
			'name' => $this->l('None')
		));
		foreach (CMS::listCms($this->context->language->id) as $cms_file)
			$cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);

		// List of order process types
		$order_process_type = array(
			array(
				'value' => PS_ORDER_PROCESS_STANDARD,
				'name' => $this->l('Standard (Five steps)')
			),
			array(
				'value' => PS_ORDER_PROCESS_OPC,
				'name' => $this->l('One-page checkout')
			)
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'icon-cogs',
				'fields' =>	array(
					'PS_ORDER_PROCESS_TYPE' => array(
						'title' => $this->l('Order process type'),
						'hint' => $this->l('Please choose either the five-step or one-page checkout process.'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'select',
						'list' => $order_process_type,
						'identifier' => 'value'
					),
					'PS_GUEST_CHECKOUT_ENABLED' => array(
						'title' => $this->l('Enable guest checkout'),
						'hint' => $this->l('Allow guest visitors to place an order without registering.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_PURCHASE_MINIMUM' => array(
						'title' => $this->l('Minimum purchase total required in order to validate the order'),
						'hint' => $this->l('Set to 0 to disable this feature.'),
						'validation' => 'isFloat',
						'cast' => 'floatval',
						'type' => 'price'
					),
					'PS_ALLOW_MULTISHIPPING' => array(
						'title' => $this->l('Allow multishipping'),
						'hint' => $this->l('Allow the customer to ship orders to multiple addresses. This option will convert the customer\'s cart into one or more orders.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_SHIP_WHEN_AVAILABLE' => array(
						'title' => $this->l('Delayed shipping'),
						'hint' => $this->l('Allows you to delay shipping at your customers\' request. '),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_CONDITIONS' => array(
						'title' => $this->l('Terms of service'),
						'hint' => $this->l('Require customers to accept or decline terms of service before processing an order.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'js' => array(
							'on' => 'onchange="changeCMSActivationAuthorization()"',
							'off' => 'onchange="changeCMSActivationAuthorization()"'
						)
					),
					'PS_CONDITIONS_CMS_ID' => array(
						'title' => $this->l('CMS page for the Conditions of use'),
						'hint' => $this->l('Choose the CMS page which contains your store\'s conditions of use.'),
						'validation' => 'isInt',
						'type' => 'select',
						'list' => $cms_tab,
						'identifier' => 'id',
						'cast' => 'intval'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'pdf' => array(
				'title' => $this->l('PDF Settings'),
				'icon' => 'icon-file-text',
				'fields' => array(
					'PS_PDF_IMG_INVOICE' => array(
						'title' => $this->l('Enable product image on Invoice'),
						'hint' => $this->l('Adds a image before productname on Invoice'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_PDF_IMG_DELIVERY' => array(
						'title' => $this->l('Enable product image on Delivery-slip'),
						'hint' => $this->l('Adds a image before productname on Delivery-slip'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'gift' => array(
				'title' =>	$this->l('Gift options'),
				'icon' =>	'icon-gift',
				'fields' =>	array(
					'PS_GIFT_WRAPPING' => array(
						'title' => $this->l('Offer gift wrapping'),
						'hint' => $this->l('Suggest gift-wrapping to customers.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_GIFT_WRAPPING_PRICE' => array(
						'title' => $this->l('Gift-wrapping price'),
						'hint' => $this->l('Set a price for gift wrapping.'),
						'validation' => 'isPrice',
						'cast' => 'floatval',
						'type' => 'price'
					),
					'PS_GIFT_WRAPPING_TAX_RULES_GROUP' => array(
						'title' => $this->l('Gift-wrapping tax'),
						'hint' => $this->l('Set a tax for gift wrapping.'),
						'validation' => 'isInt',
						'cast' => 'intval',
						'type' => 'select',
						'list' => array_merge(array(array('id_tax_rules_group' => 0, 'name' => $this->l('None'))), TaxRulesGroup::getTaxRulesGroups(true)),
						'identifier' => 'id_tax_rules_group'
					),
					'PS_RECYCLABLE_PACK' => array(
						'title' => $this->l('Offer recycled packaging'),
						'hint' => $this->l('Suggest recycled packaging to customer.'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
				),
				'submit' => array('title' => $this->l('Save')),
			),
		);

		if (!Configuration::get('PS_ALLOW_MULTISHIPPING'))
			unset($this->fields_options['general']['fields']['PS_ALLOW_MULTISHIPPING']);
	}

	/**
	 * This method is called before we start to update options configuration
	 */
	public function beforeUpdateOptions()
	{
		$sql = 'SELECT `id_cms` FROM `'._DB_PREFIX_.'cms`
				WHERE id_cms = '.(int)Tools::getValue('PS_CONDITIONS_CMS_ID');
		if (Tools::getValue('PS_CONDITIONS') && (Tools::getValue('PS_CONDITIONS_CMS_ID') == 0 || !Db::getInstance()->getValue($sql)))
			$this->errors[] = Tools::displayError('Assign a valid CMS page if you want it to be read.');
	}
}
