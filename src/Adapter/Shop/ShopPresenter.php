<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use AddressFormat;
use Language;
use State;
use Country;

class ShopPresenter implements PresenterInterface
{
    public $configuration;
    public $language;

    public function __construct(Configuration $configuration, Language $language)
    {
        $this->configuration = $configuration;
        $this->language = $language;
    }

    public function present($shop)
    {
        if (!is_a($shop, 'Shop')) {
            throw new \Exception('ShopPresenter can only present instance of Shop');
        }
        $address = $shop->getAddress();

        $presentedShop = array(
            'name' => $this->configuration->get('PS_SHOP_NAME'),
            'email' => $this->configuration->get('PS_SHOP_EMAIL'),
            'registration_number' => $this->configuration->get('PS_SHOP_DETAILS'),

            'long' => $this->configuration->get('PS_STORES_CENTER_LONG'),
            'lat' => $this->configuration->get('PS_STORES_CENTER_LAT'),

            'logo' => ($this->configuration->get('PS_LOGO')) ? _PS_IMG_.$this->configuration->get('PS_LOGO') : '',
            'stores_icon' => ($this->configuration->get('PS_STORES_ICON')) ? _PS_IMG_.$this->configuration->get('PS_STORES_ICON') : '',
            'favicon' => ($this->configuration->get('PS_FAVICON')) ? _PS_IMG_.$this->configuration->get('PS_FAVICON') : '',
            'favicon_update_time' => $this->configuration->get('PS_IMG_UPDATE_TIME'),

            'address' => array(
                'formatted' => AddressFormat::generateAddress($address, array(), '<br>'),
                'address1' => $address->address1,
                'address2' => $address->address2,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => (new State($address->id_state))->name[$this->language->id],
                'country' => (new Country($address->id_country))->name[$this->language->id],
            ),
            'phone' => $this->configuration->get('PS_SHOP_PHONE'),
            'fax' => $this->configuration->get('PS_SHOP_FAX'),
        );

        return $presentedShop;
    }
}
