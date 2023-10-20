<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use Cart;
use Context;
use Currency;
use HelperKpi;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;

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
        $helper->title = $translator->trans('Total cart', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $translator->trans('Cart #%ID%', ['%ID%' => $cart->id], 'Admin.Orderscustomers.Feature');
        $helper->value = $this->locale->formatPrice(
            $cart->getCartTotalPrice(),
            Currency::getIsoCodeById((int) $cart->id_currency)
        );
        $helper->source = Context::getContext()->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=shopping_cart_total&cartId=' . $cart->id;

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
}
