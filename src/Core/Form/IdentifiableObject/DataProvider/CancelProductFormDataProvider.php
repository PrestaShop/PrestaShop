<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Order\OrderTaxMethodProvider;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;

/**
 * Provides data for product cancellation form in order page
 */
final class CancelProductFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly CurrencyDataProviderInterface $currencyDataProvider,
        private readonly OrderRepository $orderRepository,
        private readonly OrderTaxMethodProvider $orderTaxMethodProvider,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderId)
    {
        $order = $this->orderRepository->get(new OrderId((int) $orderId));
        /** @var OrderProductsForViewing $products */
        $products = $this->queryBus->handle(GetOrderProductsForViewing::all((int) $order->id));
        $currency = $this->currencyDataProvider->getCurrencyById((int) $order->id_currency);

        return [
            'products' => $products->getProducts(),
            'taxMethod' => $this->orderTaxMethodProvider->getTaxMethod($order),
            'precision' => (new ComputingPrecision())->getPrecision($currency->precision),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [];
    }
}
