<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use Address;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use DeliveryOptionsFinder;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * DeliveryOptionsFinder::getDeliveryOptions() is called several times per checkout render and
 * re-presents every carrier each time. It now memoizes the result per delivery address. This guards
 * that the memoized result is identical to a freshly-computed one (same carriers, prices, labels).
 */
class DeliveryOptionsFinderMemoTest extends KernelTestCase
{
    public function testMemoizedDeliveryOptionsMatchAFreshComputation(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        Context::getContext()->container = $container;

        $cartId = (int) Db::getInstance()->getValue(
            'SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart WHERE id_address_delivery > 0 ORDER BY id_cart'
        );
        $cart = new Cart($cartId);

        $context = Context::getContext();
        $context->cart = $cart;
        $context->customer = new Customer((int) $cart->id_customer);
        $context->currency = new Currency((int) $cart->id_currency);
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $address = new Address((int) $cart->id_address_delivery);
        $context->country = new Country((int) $address->id_country);

        $translator = $container->get('translator');
        $objectPresenter = new ObjectPresenter();
        $priceFormatter = new PriceFormatter();

        $finder = new DeliveryOptionsFinder($context, $translator, $objectPresenter, $priceFormatter);
        $firstCall = $finder->getDeliveryOptions();
        $secondCall = $finder->getDeliveryOptions();

        // A fresh finder (no memo yet) recomputes from scratch.
        $freshFinder = new DeliveryOptionsFinder($context, $translator, $objectPresenter, $priceFormatter);
        $recomputed = $freshFinder->getDeliveryOptions();

        self::assertNotEmpty($firstCall, 'the demo cart must resolve at least one carrier');
        self::assertSame($firstCall, $secondCall, 'the memoized call must return the same result');
        self::assertEquals($recomputed, $firstCall, 'the memoized result must match a fresh computation');
    }
}
