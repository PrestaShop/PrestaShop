<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
