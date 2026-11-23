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
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Util\Sorter;

/**
 * Provides data for product cancellation form in order page
 */
final class CancelProductFormDataProvider implements FormDataProviderInterface
{
    private ?OrderForViewing $preloadedOrderForViewing = null;

    public function __construct(
        private readonly CommandBusInterface $queryBus,
        private readonly CurrencyDataProviderInterface $currencyDataProvider,
        private readonly OrderRepository $orderRepository,
        private readonly OrderTaxMethodProvider $orderTaxMethodProvider,
    ) {
    }

    /**
     * Lets a caller that just built the order view hand it over, so the next getData() for this order does not
     * query its products again. It is used once: a later call reads the order again, as it may have changed.
     */
    public function preloadOrderForViewing(OrderForViewing $orderForViewing): void
    {
        $this->preloadedOrderForViewing = $orderForViewing;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderId)
    {
        $orderForViewing = $this->preloadedOrderForViewing;
        $this->preloadedOrderForViewing = null;

        if (null !== $orderForViewing && $orderForViewing->getId() === (int) $orderId) {
            return $this->buildData(
                $this->applyDefaultSorting($orderForViewing->getProducts()->getProducts()),
                $orderForViewing->getTaxMethod(),
                $orderForViewing->getCurrencyId()
            );
        }

        $orderId = new OrderId((int) $orderId);
        $priceDisplaySettings = $this->orderRepository->getPriceDisplaySettings($orderId);
        /** @var OrderProductsForViewing $products */
        $products = $this->queryBus->handle(GetOrderProductsForViewing::all($orderId->getValue()));

        return $this->buildData(
            $products->getProducts(),
            $this->orderTaxMethodProvider->getTaxMethod($priceDisplaySettings['is_tax_included']),
            $priceDisplaySettings['currency_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [];
    }

    /**
     * The order view may be sorted another way: the form data keeps the sorting of GetOrderProductsForViewing::all().
     *
     * @param OrderProductForViewing[] $products
     *
     * @return OrderProductForViewing[]
     */
    private function applyDefaultSorting(array $products): array
    {
        $rows = array_map(static fn (OrderProductForViewing $product): array => [
            'product_reference' => $product->getReference(),
            'product_supplier_reference' => $product->getSupplierReference(),
            'product' => $product,
        ], $products);

        return array_column(
            (new Sorter())->natural($rows, Sorter::ORDER_ASC, 'product_reference', 'product_supplier_reference'),
            'product'
        );
    }

    private function buildData(array $products, string $taxMethod, int $currencyId): array
    {
        $currency = $this->currencyDataProvider->getCurrencyById($currencyId);

        return [
            'products' => $products,
            'taxMethod' => $taxMethod,
            'precision' => (new ComputingPrecision())->getPrecision($currency->precision),
        ];
    }
}
