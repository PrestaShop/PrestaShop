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

class AdminPreferencesControllerCore extends AdminController
{

	public function __construct()
	{
		$this->bootstrap = true;
		$this->context = Context::getContext();
		$this->className = 'Configuration';
		$this->table = 'configuration';

		// Prevent classes which extend AdminPreferences to load useless data
		if (get_class($this) == 'AdminPreferencesController')
		{
			$round_mode = array(
				array(
					'value' => PS_ROUND_HALF_UP,
					'name' => $this->l('classical superior')
				),
				array(
					'value' => PS_ROUND_HALF_DOWN,
					'name' => $this->l('classical inferior')
				),
				array(
					'value' => PS_ROUND_HALF_EVEN,
					'name' => $this->l('classical to the next even value')
				),
				array(
					'value' => PS_ROUND_HALF_ODD,
					'name' => $this->l('classical to the next odd value')
				),
				array(
					'value' => PS_ROUND_UP,
					'name' => $this->l('superior')
				),
				array(
					'value' => PS_ROUND_DOWN,
					'name' => $this->l('inferior')
				),
			);
			$activities1 = array(
				0 => $this->l('-- Please choose your main activity --'),
				2 => $this->l('Animals and Pets'),
				3 => $this->l('Art and Culture'),
				4 => $this->l('Babies'),
				5 => $this->l('Beauty and Personal Care'),
				6 => $this->l('Cars'),
				7 => $this->l('Computer Hardware and Software'),
				8 => $this->l('Download'),
				9 => $this->l('Fashion and accessories'),
				10 => $this->l('Flowers, Gifts and Crafts'),
				11 => $this->l('Food and beverage'),
				12 => $this->l('HiFi, Photo and Video'),
				13 => $this->l('Home and Garden'),
				14 => $this->l('Home Appliances'),
				15 => $this->l('Jewelry'),
				1 => $this->l('Lingerie and Adult'),
				16 => $this->l('Mobile and Telecom'),
				17 => $this->l('Services'),
				18 => $this->l('Shoes and accessories'),
				19 => $this->l('Sport and Entertainment'),
				20 => $this->l('Travel')
			);
			$activities2 = array();
			foreach ($activities1 as $value => $name)
				$activities2[] = array('value' => $value, 'name' => $name);

			$fields = array(
				'PS_SSL_ENABLED' => array(
					'title' => $this->l('Enable SSL'),
					'desc' => $this->l('If your hosting provider allows SSL, you can activate SSL encryption (https://) for customer account identification and order processing.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0'
				),
			);

			if (Tools::getValue('PS_SSL_ENABLED', Configuration::get('PS_SSL_ENABLED')))
				$fields['PS_SSL_ENABLED_EVERYWHERE'] = array(
					'title' => $this->l('Force the SSL on all the pages'),
					'desc' => $this->l('Force all your store to use SSL.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0'
				);

			$fields = array_merge($fields, array(
				'PS_TOKEN_ENABLE' => array(
					'title' => $this->l('Increase Front Office security'),
					'desc' => $this->l('Enable or disable token in the Front Office to improve PrestaShop\'s security.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_ALLOW_HTML_IFRAME' => array(
					'title' => $this->l('Allow iframes on HTML fields'),
					'desc' => $this->l('Allow iframes on text fields like product description. We recommend that you leave this option disabled.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'default' => '0'
				),
				'PS_PRICE_ROUND_MODE' => array(
					'title' => $this->l('Round mode'),
					'desc' => $this->l('You can choose how to round prices: always round up, always round down or classic rounding (up if > .5, down if < .5).'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $round_mode,
					'identifier' => 'value'
				),
				'PS_DISPLAY_SUPPLIERS' => array(
					'title' => $this->l('Display suppliers and manufacturers'),
					'desc' => $this->l('Enable suppliers and manufacturers pages on your Front Office even when their respective modules are disabled.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool'
				),
				'PS_MULTISHOP_FEATURE_ACTIVE' => array(
					'title' => $this->l('Enable Multistore'),
					'desc' => $this->l('The multistore feature allows you to manage several e-shops with one Back Office. If this feature is enabled, a "Multistore" page will be available in the "Advanced Parameters" menu.'),
					'validation' => 'isBool',
					'cast' => 'intval',
					'type' => 'bool',
					'visibility' => Shop::CONTEXT_ALL
				),
				'PS_SHOP_ACTIVITY' => array(
					'title' => $this->l('Main Shop Activity'),
					'validation' => 'isInt',
					'cast' => 'intval',
					'type' => 'select',
					'list' => $activities2,
					'identifier' => 'value'
				),
			));

			// No HTTPS activation if you haven't already.
			if (!Tools::usingSecureMode() && !Configuration::get('PS_SSL_ENABLED'))
			{
				$fields['PS_SSL_ENABLED']['type'] = 'disabled';
				$fields['PS_SSL_ENABLED']['disabled'] = '<a class="btn btn-link" href="https://'.Tools::getShopDomainSsl().Tools::safeOutput($_SERVER['REQUEST_URI']).'">'.
					$this->l('Please click here to use HTTPS protocol before enabling SSL.').'</a>';
			}

			$this->fields_options = array(
				'general' => array(
					'title' =>	$this->l('General'),
					'icon' =>	'icon-cogs',
					'fields' =>	$fields,
					'submit' => array('title' => $this->l('Save')),
				),
			);
		}

		parent::__construct();
	}

	/**
	 * Enable / disable multishop menu if multishop feature is activated
	 *
	 * @param string $value
	 */
	public function updateOptionPsMultishopFeatureActive($value)
	{
		Configuration::updateValue('PS_MULTISHOP_FEATURE_ACTIVE', $value);

		$tab = Tab::getInstanceFromClassName('AdminShopGroup');
		$tab->active = (bool)Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
		$tab->update();
	}
}
