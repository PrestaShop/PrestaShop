<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\QueryHandler;

use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnProducts;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryHandler\GetOrderReturnProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnProductForEditing;

#[AsQueryHandler]
class GetOrderReturnProductsHandler implements GetOrderReturnProductsHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository)
    {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderReturnProducts $query): array
    {
        $orderReturnId = $query->getOrderReturnId();
        $rows = [];

        // Customizations: surfaced first so customers' personalized lines are visually grouped
        // ahead of the regular product rows (mirrors the legacy template ordering).
        foreach ($this->orderReturnRepository->getCustomizedProductsForReturn($orderReturnId) as $customization) {
            $rows[] = new OrderReturnProductForEditing(
                (int) $customization['id_order_detail'],
                (int) $customization['id_customization'],
                (string) ($customization['reference'] ?? ''),
                (string) ($customization['name'] ?? ''),
                (int) ($customization['product_quantity'] ?? 0),
                true
            );
        }

        foreach ($this->orderReturnRepository->getProductsForReturn($orderReturnId) as $product) {
            $rows[] = new OrderReturnProductForEditing(
                (int) $product['id_order_detail'],
                (int) ($product['customizations'] ?? 0),
                (string) ($product['product_reference'] ?? ''),
                (string) ($product['product_name'] ?? ''),
                (int) ($product['product_quantity'] ?? 0),
                false
            );
        }

        return $rows;
    }
}
