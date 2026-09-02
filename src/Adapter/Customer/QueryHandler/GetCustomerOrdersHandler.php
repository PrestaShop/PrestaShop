<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Currency;
use Customer;
use Order;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerOrders;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerOrdersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrderSummary;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Handles GetCustomerOrders query using legacy object models
 */
#[AsQueryHandler]
final class GetCustomerOrdersHandler extends AbstractCustomerHandler implements GetCustomerOrdersHandlerInterface
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
     * @param GetCustomerOrders $query
     *
     * @return OrderSummary[]
     *
     * @throws CustomerNotFoundException
     */
    public function handle(GetCustomerOrders $query): array
    {
        $customerId = $query->getCustomerId();

        $this->assertCustomerWasFound($customerId, new Customer($customerId->getValue()));

        return $this->getOrders($customerId->getValue());
    }

    /**
     * @param int $customerId
     *
     * @throws LocalizationException
     */
    private function getOrders(int $customerId)
    {
        $summarizedOrders = [];

        $customerOrders = Order::getCustomerOrders($customerId);
        foreach ($customerOrders as $customerOrder) {
            $currency = new Currency((int) $customerOrder['id_currency']);

            $summarizedOrders[] = new OrderSummary(
                (int) $customerOrder['id_order'],
                $customerOrder['date_add'],
                $customerOrder['payment'],
                $customerOrder['order_state'] ?: '',
                (int) $customerOrder['nb_products'],
                $this->locale->formatPrice(
                    $customerOrder['total_paid_real'],
                    $currency->iso_code
                )
            );
        }

        return $summarizedOrders;
    }
}
