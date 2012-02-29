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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPreferencesControllerCore extends AdminController
{

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->className = 'Configuration';
		$this->table = 'configuration';

		$max_upload = (int)ini_get('upload_max_filesize');
		$max_post = (int)ini_get('post_max_size');
		$upload_mb = min($max_upload, $max_post);

		// Prevent classes which extend AdminPreferences to load useless data
		if (get_class($this) == 'AdminPreferencesController')
		{
			$timezones = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT name FROM '._DB_PREFIX_.'timezone');
			$taxes[] = array('id' => 0, 'name' => $this->l('None'));
			foreach (Tax::getTaxes($this->context->language->id) as $tax)
				$taxes[] = array('id' => $tax['id_tax'], 'name' => $tax['name']);

			$order_process_type = array(
				array(
					'value' => PS_ORDER_PROCESS_STANDARD,
					'name' => $this->l('Standard (5 steps)')
				),
				array(
					'value' => PS_ORDER_PROCESS_OPC,
					'name' => $this->l('One page checkout')
				)
			);

			$registration_process_type = array(
				array(
					'value' => PS_REGISTRATION_PROCESS_STANDARD,
					'name' => $this->l('Only account creation')
				),
				array(
					'value' => PS_REGISTRATION_PROCESS_AIO,
					'name' => $this->l('Standard (account creation and address creation)')
				)
			);

			$round_mode = array(
				array(
					'value' => PS_ROUND_UP,
					'name' => $this->l('superior')
				),
				array(
					'value' => PS_ROUND_DOWN,
					'name' => $this->l('inferior')
				),
				array(
					'value' => PS_ROUND_HALF,
					'name' => $this->l('classical')
				)
			);

			$cms_tab = array(0 =>
				array(
					'id' => 0,
					'name' => $this->l('None')
				)
			);
			foreach (CMS::listCms($this->context->language->id) as $cms_file)
				$cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);

			$fields = array(
				'PS_SHOP_ENABLE' => array(
					'title' => $this->l('Enable Shop'),
					'desc' => $this->l('Activate or deactivate your shop. Deactivate your shop while you perform maintenance on it. Please note that the webservice will not be disabled'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_MAINTENANCE_IP' => array(
					'title' => $this->l('Maintenance IP'),
					'desc' => $this->l('IP addresses allowed to access the Front Office even if shop is disabled. Use a comma to separate them (e.g., 42.24.4.2,127.0.0.1,99.98.97.96)'),
					'validation' => 'isGenericName',
					'type' => 'maintenance_ip',
					'size' => 30,
					'default' => ''
				),
				'PS_SSL_ENABLED' => array(
					'title' => $this->l('Enable SSL'),
					'desc' => $this->l('If your hosting provider allows SSL, you can activate SSL encryption (https://) for customer account identification and order processing'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0'
				),
				'PS_COOKIE_CHECKIP' => array(
					'title' => $this->l('Check IP on the cookie'),
					'desc' => $this->l('Check the IP address of the cookie in order to avoid your cookie being stolen'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_TOKEN_ENABLE' => array(
					'title' => $this->l('Increase Front Office security'),
					'desc' => $this->l('Enable or disable token on the Front Office in order to improve PrestaShop security'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_HELPBOX' => array(
					'title' => $this->l('Back Office help boxes'),
					'desc' => $this->l('Enable yellow help boxes which are displayed under form fields in the Back Office'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_COOKIE_LIFETIME_FO' => array(
					'title' => $this->l('Lifetime of the Front Office cookie'),
					'desc' => $this->l('Indicate the number of hours'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'text',
					'default' => '480',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_COOKIE_LIFETIME_BO' => array(
					'title' => $this->l('Lifetime of the Back Office cookie'),
					'desc' => $this->l('Indicate the number of hours'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'text',
					'default' => '480',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_B2B_ENABLE' => array(
					'title' => $this->l('Enable B2B mode'),
					'desc' => $this->l('Activate or deactivate B2B mode. When this option is enable some features about B2B appear.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_ORDER_PROCESS_TYPE' => array(
					'title' => $this->l('Order process type'),
					'desc' => $this->l('You can choose the order process type as either standard (5 steps) or One Page Checkout'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $order_process_type,
					'identifier' => 'value'
				),
				'PS_ALLOW_MULTISHIPPING' => array(
					'title' => $this->l('Allow multi-shipping'),
					'desc' => $this->l('Allow the customer to ship his order to multiple addresses. This option will transform the customer cart in one or more orders.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_SHIP_WHEN_AVAILABLE' => array(
					'title' => $this->l('Delayed shipping'),
					'desc' => $this->l('Allow the customer to split his order. One with the products "in stock", and an other with the other products. This option will transform the customer cart in two orders.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_REGISTRATION_PROCESS_TYPE' => array(
					'title' => $this->l('Registration process type'),
					'desc' => $this->l('The "Only account creation" step register process allows the customer to register faster, and create his address later.'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $registration_process_type,
					'identifier' => 'value'
				),
				'PS_GUEST_CHECKOUT_ENABLED' => array(
					'title' => $this->l('Enable guest checkout'),
					'desc' => $this->l('Your guest can make an order without registering'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_CONDITIONS' => array(
					'title' => $this->l('Terms of service'),
					'desc' => $this->l('Require customers to accept or decline terms of service before processing the order'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'js' => array(
						'on' => 'onchange="changeCMSActivationAuthorization()"',
						'off' => 'onchange="changeCMSActivationAuthorization()"'
					)
				),
				'PS_CONDITIONS_CMS_ID' => array(
					'title' => $this->l('Conditions of use CMS page'),
					'desc' => $this->l('Choose the Conditions of use CMS page'),
					'validation' => 'isInt',
					'type' => 'select',
					'list' => $cms_tab,
					'identifier' => 'id',
					'cast' => 'intval'
				),
				'PS_GIFT_WRAPPING' => array(
					'title' => $this->l('Offer gift-wrapping'),
					'desc' => $this->l('Suggest gift-wrapping to customer and possibility of leaving a message'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_GIFT_WRAPPING_PRICE' => array(
					'title' => $this->l('Gift-wrapping price'),
					'desc' => $this->l('Set a price for gift-wrapping'),
					'validation' => 'isPrice',
					'cast' => 'floatval',
					'type' => 'price'
				),
				'PS_GIFT_WRAPPING_TAX' => array(
					'title' => $this->l('Gift-wrapping tax'),
					'desc' => $this->l('Set a tax for gift-wrapping'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $taxes,
					'identifier' => 'id'
				),
				'PS_GIFT_WRAPPING_ACCOUNT_NUMBER' => array(
					'title' => $this->l('Gift-wrapping account number'),
					'desc' => $this->l('Set an account number for your gift-wrapping (used for accounting)'),
					'validation' => 'isString',
					'type' => 'text',
					'size' => 30,
				),

				'PS_ATTACHMENT_MAXIMUM_SIZE' => array(
					'title' => $this->l('Attachment maximum size'),
					'desc' => $this->l('Set the maximum size of attachment files (in MegaBytes).').' '.$this->l('Maximum:').' '.
						((int)str_replace('M', '', ini_get('post_max_size')) > (int)str_replace('M', '', ini_get('upload_max_filesize')) ? ini_get('upload_max_filesize') : ini_get('post_max_size')),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'text',
					'default' => '2'
				),
				'PS_RECYCLABLE_PACK' => array(
					'title' => $this->l('Offer recycled packaging'),
					'desc' => $this->l('Suggest recycled packaging to customer'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_CART_FOLLOWING' => array(
					'title' => $this->l('Cart re-display at login'),
					'desc' => $this->l('After customer logs in, recall and display contents of his/her last shopping cart'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_PRICE_ROUND_MODE' => array(
					'title' => $this->l('Round mode'),
					'desc' => $this->l('You can choose how to round prices: always round superior; always round inferior, or classic rounding'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $round_mode,
					'identifier' => 'value'
				),
				'PRESTASTORE_LIVE' => array(
					'title' => $this->l('Automatically check for module updates'),
					'desc' => $this->l('New modules and updates are displayed on the modules page'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_HIDE_OPTIMIZATION_TIPS' => array(
					'title' => $this->l('Hide optimization tips'),
					'desc' => $this->l('Hide optimization tips on the back office homepage'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_DISPLAY_SUPPLIERS' => array(
					'title' => $this->l('Display suppliers and manufacturers'),
					'desc' => $this->l('Display manufacturers and suppliers list even if corresponding blocks are disabled'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_SHOW_NEW_ORDERS' => array(
					'title' => $this->l('Show notifications for new orders'),
					'desc' => $this->l('This will display notifications when new orders will be made on your shop'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_SHOW_NEW_CUSTOMERS' => array(
					'title' => $this->l('Show notifications for new customers'),
					'desc' => $this->l('This will display notifications when new customers will register on your shop'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_SHOW_NEW_MESSAGES' => array(
					'title' => $this->l('Show notifications for new messages'),
					'desc' => $this->l('This will display notifications when new messages will be posted on your shop'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_LIMIT_UPLOAD_FILE_VALUE' => array(
					'title' => $this->l('Limit upload file value'),
					'desc' => $this->l('Define the limit upload for a downloadable product, this value have to be inferior or egal to your server\'s maximum upload file ').sprintf('(%s MB).', $upload_mb),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'text',
					'suffix' => $this->l('Megabits'),
					'default' => '1'
				),
				'PS_LIMIT_UPLOAD_IMAGE_VALUE' => array(
					'title' => $this->l('Limit upload image value'),
					'desc' => $this->l('Define the limit upload for an image, this value have to be inferior or egal to your server\'s maximum upload file ').sprintf('(%s MB).', $upload_mb),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'text',
					'suffix' => $this->l('Megabits'),
					'default' => '1'
				),
			);

			if (function_exists('date_default_timezone_set'))
				$fields['PS_TIMEZONE'] = array(
					'title' => $this->l('Time Zone:'),
					'validation' => 'isAnything',
					'type' => 'select',
					'list' => $timezones,
					'identifier' => 'name',
					'visibility' => Shop::CONTEXT_ALL
				);

			// No HTTPS activation if you haven't already.
			if (!Tools::usingSecureMode())
			{
				$fields['PS_SSL_ENABLED']['type'] = 'disabled';
				$fields['PS_SSL_ENABLED']['disabled'] = '<a href="https://'.Tools::getShopDomainSsl().Tools::safeOutput($_SERVER['REQUEST_URI']).'">'.
					$this->l('Please click here to use HTTPS protocol before enabling SSL.').'</a>';
			}

			$this->options = array(
				'general' => array(
					'title' =>	$this->l('General'),
					'icon' =>	'tab-preferences',
					'fields' =>	$fields,
					'submit' => array('title' => $this->l('   Save   '), 'class' => 'button'),

				),
			);
		}

		parent::__construct();
	}

	public function postProcess()
	{
		$upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
		$post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
		$max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;

		if (Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE') > $max_size || Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE') > $max_size)
		{
			$this->errors[] = Tools::displayError('The limit choosen is superior to the server\'s maximum upload file You need to improve the limit of your server.');
			return;
		}

		if (Tools::getIsset('PS_LIMIT_UPLOAD_FILE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE'))
			$_POST['PS_LIMIT_UPLOAD_FILE_VALUE'] = 1;

		if (Tools::getIsset('PS_LIMIT_UPLOAD_IMAGE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE'))
			$_POST['PS_LIMIT_UPLOAD_IMAGE_VALUE'] = 1;

		Tools::clearCache($this->context->smarty);
		parent::postProcess();
	}

	protected function getConf($fields, $languages)
	{
		$tab['_MEDIA_SERVER_1_'] = _MEDIA_SERVER_1_;
		$tab['_MEDIA_SERVER_2_'] = _MEDIA_SERVER_2_;
		$tab['_MEDIA_SERVER_3_'] = _MEDIA_SERVER_3_;

		return $tab;
	}

	/**
	 * This method is called before we start to update options configuration
	 */
	public function beforeUpdateOptions()
	{
		if (get_class($this) != 'AdminPreferences')
			return;

		$sql = 'SELECT `id_cms` FROM `'._DB_PREFIX_.'cms`
				WHERE id_cms = '.(int)Tools::getValue('PS_CONDITIONS_CMS_ID');
		if (Tools::getValue('PS_CONDITIONS') && (Tools::getValue('PS_CONDITIONS_CMS_ID') == 0 || !Db::getInstance()->getValue($sql)))
			$this->errors[] = Tools::displayError('Assign a valid CMS page if you want it to be read.');
	}

	/**
	 * Update PS_ATTACHMENT_MAXIMUM_SIZE
	 */
	public function updateOptionPsAttachementMaximumSize($value)
	{
		if (!$value)
			return;

		$upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
		$post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
		$max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;
		$value = ($max_size < Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE')) ? $max_size : Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE');
		Configuration::update('PS_ATTACHMENT_MAXIMUM_SIZE', $value);
	}

	/**
	 * Update PS_B2B_ENABLE and enables / disables the associated tabs
	 * @param $value integer Value of option
	 */
	public function updateOptionPsB2bEnable($value)
	{
		$value = (int)$value;

		$tabs_class_name = array('AdminOutstanding');
		if (!empty($tabs_class_name))
		{
			foreach ($tabs_class_name as $tab_class_name)
			{
				$tab = Tab::getInstanceFromClassName($tab_class_name);
				if (Validate::isLoadedObject($tab))
				{
					$tab->active = $value;
					$tab->save();
				}
			}
		}
		Configuration::updateValue('PS_B2B_ENABLE', $value);
	}
}
