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

namespace PrestaShop\PrestaShop\Adapter\Customer;

use PrestaShop\PrestaShop\Adapter\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use Gender;
use Risk;
use Address;
use AddressFormat;

class CustomerPresenter implements PresenterInterface
{
    public $objectPresenter;

    public function __construct(ObjectPresenter $objectPresenter)
    {
        $this->objectPresenter = $objectPresenter;
    }

    public function present($customer)
    {
        if (!is_a($customer, 'Customer')) {
            throw new \Exception('CustomerPresenter can only present instance of Customer');
        }

        $presentedCustomer = $this->objectPresenter->present($customer);

        $presentedCustomer['is_logged'] = $customer->isLogged(true);

        $presentedCustomer['gender'] = $this->objectPresenter->present(new Gender($presentedCustomer['id_gender']));

        $presentedCustomer['risk'] = $this->objectPresenter->present(new Risk($presentedCustomer['id_risk']));

        $addresses = $customer->getSimpleAddresses();
        foreach ($addresses as &$address) {
            $address['formatted'] = AddressFormat::generateAddress(new Address($address['id']), array(), '<br>');
        }
        $presentedCustomer['addresses'] = $addresses;

        unset(
            $presentedCustomer['secure_key'],
            $presentedCustomer['passwd'],
            $presentedCustomer['show_public_prices'],
            $presentedCustomer['deleted'],
            $presentedCustomer['id_lang'],
            $presentedCustomer['id_risk'],
            $presentedCustomer['id_gender']
        );

        return $presentedCustomer;
    }
}
