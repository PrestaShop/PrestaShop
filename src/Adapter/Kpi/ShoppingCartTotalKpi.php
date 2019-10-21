<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use Cart;
use Context;
use Group;
use HelperKpi;
use Order;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Validate;

/**
 * {@inheritdoc}
 */
final class ShoppingCartTotalKpi implements KpiInterface
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var array
     */
    private $options;

    /**
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $translator = Context::getContext()->getTranslator();
        $cart = new Cart($this->options['cart_id']);

        $helper = new HelperKpi();
        $helper->id = 'box-kpi-cart';
        $helper->icon = 'shopping_cart';
        $helper->color = 'color1';
        $helper->title = $translator->trans('Total Cart', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $translator->trans('Cart #%ID%', ['%ID%' => $cart->id], 'Admin.Orderscustomers.Feature');
        $helper->value = $this->locale->formatPrice(
            $this->getCartTotalPrice($cart),
            \Currency::getIsoCodeById((int) $cart->id_currency)
        );

        return $helper->generate();
    }

    /**
     * Sets options for Kpi
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param Cart $cart
     *
     * @return float
     */
    private function getCartTotalPrice(Cart $cart)
    {
        $summary = $cart->getSummaryDetails();

        $id_order = (int) Order::getIdByCartId($cart->id);
        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            $taxCalculationMethod = $order->getTaxCalculationMethod();
        } else {
            $taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        $totalPrice = $taxCalculationMethod == PS_TAX_EXC ?
            $summary['total_price_without_tax'] :
            $summary['total_price'];

        return $totalPrice;
    }
}
