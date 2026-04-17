<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderDetailRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipmentProduct;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;
use Symfony\Component\HttpFoundation\RequestStack;

class SplitShipmentFormDataProvider extends ShipmentFormDataProvider
{
    public function __construct(
        private OrderDetailRepository $orderDetailRepository,
        private CommandBusInterface $queryBus,
        private RequestStack $requestStack
    ) {
        parent::__construct($queryBus);
    }

    public function getData($orderId)
    {
        $shipmentId = $this->requestStack->getMainRequest()->query->getInt('shipmentId');
        $productsFromQuery = $this->requestStack->getMainRequest()->get('products', []);
        $selectedCarrier = $this->requestStack->getMainRequest()->query->getInt('carrier');
        $orderShipmentProducts = $this->mergeProductsFromQueries($shipmentId, $productsFromQuery);

        return [
            'products' => $orderShipmentProducts,
            'carrier' => $selectedCarrier,
            'shipment_id' => $shipmentId,
            'order_id' => $orderId,
            'form_is_valid' => $this->checkFormValidity($orderShipmentProducts),
            'is_shipped' => $this->isShipmentShipped($orderId, $shipmentId),
        ];
    }

    public function getDefaultData()
    {
        return [];
    }

    /**
     * @param int $shipmentId
     * @param array<array{selected: string, selected_quantity: string, order_detail_id: string}> $productsFromQuery
     *
     * @return array<array{
     *     selected?: bool,
     *     selected_quantity?: int,
     *     order_detail_id: int,
     *     quantity: int,
     *     product_name: string,
     *     product_reference: string,
     *     product_image_path: string
     * }>
     *
     * @throws ShipmentException
     */
    private function mergeProductsFromQueries(
        int $shipmentId,
        array $productsFromQuery,
    ): array {
        foreach ($productsFromQuery as &$product) {
            if (isset($product['selected_quantity'])) {
                $product['selected_quantity'] = (int) $product['selected_quantity'];
            }
            if (isset($product['selected'])) {
                $product['selected'] = (bool) filter_var((int) $product['selected'], FILTER_VALIDATE_BOOLEAN);
            }
        }

        $productsQueryMap = array_column(
            $productsFromQuery,
            null,
            'order_detail_id'
        );

        /** @var OrderShipmentProduct[] $orderShipmentProducts */
        $orderShipmentProducts = $this->queryBus->handle(new GetShipmentProducts($shipmentId));

        $mergedProducts = [];

        foreach ($orderShipmentProducts as $product) {
            $productArray = $product->toArray();
            $id = $productArray['order_detail_id'] ?? null;
            if ($id !== null && isset($productsQueryMap[$id])) {
                $productArray['product_id'] = $this->orderDetailRepository
                    ->get(new OrderDetailId($productArray['order_detail_id']))
                    ->product_id;
                $productArray = array_merge($productsQueryMap[$id], $productArray);
            }

            $mergedProducts[] = $productArray;
        }

        return $mergedProducts;
    }

    /**
     * @param array<array{
     *      selected?: bool,
     *      selected_quantity?: int,
     *      order_detail_id: int,
     *      quantity: int,
     *      product_name: string,
     *      product_reference: string,
     *      product_image_path: string
     *  }> $products
     *
     * @return bool
     */
    private function checkFormValidity(array $products): bool
    {
        $allSelected = array_reduce($products, fn ($carry, $product) => $carry && ($product['selected'] ?? false), true);
        $allQuantitiesMatch = array_reduce($products, fn ($carry, $product) => $carry && (($product['selected_quantity'] ?? 0) === $product['quantity']), true);

        return !($allSelected && $allQuantitiesMatch);
    }
}
