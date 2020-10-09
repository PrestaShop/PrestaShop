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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Currency;
use Customer;
use Order;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerOrders;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerOrdersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrderSummary;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Handles GetCustomerOrders query using legacy object models
 */
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
