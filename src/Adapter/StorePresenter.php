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

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Address;
use AddressFormat;

class StorePresenter implements PresenterInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function present($store)
    {
        $presentedStore = $store;

        // TODO: handle all formats
        $presentedStore['image'] = _THEME_STORE_DIR_.(int) $store['id_store'].'-stores_default.jpg';

        $presentedStore = $this->presentAddress($store, $presentedStore);
        $presentedStore = $this->presentBusinessHours($store, $presentedStore);

        unset(
            $presentedStore['active'],
            $presentedStore['hours']
        );

        return $presentedStore;
    }

    private function presentAddress($store, $presentedStore)
    {
        $address = new Address();
        $presentedStore['address'] = array();
        $attr = array('address1', 'address2', 'postcode', 'city', 'id_state', 'id_country');
        foreach ($attr as $a) {
            $address->{$a} = $store[$a];
            $presentedStore['address'][$a] = $store[$a];
        }
        $presentedStore['address']['formatted'] = AddressFormat::generateAddress($address, array(), '<br />');

        return $presentedStore;
    }

    private function presentBusinessHours($store, $presentedStore)
    {
        $hours = json_decode($store['hours'], true);
        $presentedStore['business_hours'] = array(
            array(
                'day' => $this->translator->trans('Monday', array(), 'Shop.Theme'),
                'hours' => $hours[0],
            ),
            array(
                'day' => $this->translator->trans('Tuesday', array(), 'Shop.Theme'),
                'hours' => $hours[1],
            ),
            array(
                'day' => $this->translator->trans('Wednesday', array(), 'Shop.Theme'),
                'hours' => $hours[2],
            ),
            array(
                'day' => $this->translator->trans('Thursday', array(), 'Shop.Theme'),
                'hours' => $hours[3],
            ),
            array(
                'day' => $this->translator->trans('Friday', array(), 'Shop.Theme'),
                'hours' => $hours[4],
            ),
            array(
                'day' => $this->translator->trans('Saturday', array(), 'Shop.Theme'),
                'hours' => $hours[5],
            ),
            array(
                'day' => $this->translator->trans('Sunday', array(), 'Shop.Theme'),
                'hours' => $hours[6],
            ),
        );

        return $presentedStore;
    }
}
