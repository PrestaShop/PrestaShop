<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Cart;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerCarts;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerCartsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\CartSummary;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Handles GetCustomerCartsQuery using legacy object models
 */
#[AsQueryHandler]
final class GetCustomerCartsHandler extends AbstractCustomerHandler implements GetCustomerCartsHandlerInterface
{
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @param LocaleInterface $locale
     */
    public function __construct(
        LocaleInterface $locale
    ) {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerCarts $query): array
    {
        $customerId = $query->getCustomerId();
        $this->assertCustomerWasFound($customerId, new Customer($customerId->getValue()));

        return $this->getCarts($customerId->getValue());
    }

    /**
     * @param int $customerId
     *
     * @return array
     *
     * @throws LocalizationException
     */
    private function getCarts(int $customerId): array
    {
        $carts = Cart::getCustomerCarts($customerId, false);
        $summarizedCarts = [];

        foreach ($carts as $customerCart) {
            $cartId = (int) $customerCart['id_cart'];
            $currency = new Currency((int) $customerCart['id_currency']);
            $cart = new Cart($cartId);

            $summarizedCarts[] = new CartSummary(
                $cart->id,
                $cart->date_add,
                $this->locale->formatPrice($cart->getOrderTotal(), $currency->iso_code)
            );
        }

        return $summarizedCarts;
    }
}
