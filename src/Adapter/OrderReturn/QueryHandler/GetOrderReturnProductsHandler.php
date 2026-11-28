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
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnCustomizationFieldForEditing;
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
        // Customized lines come first (mirrors the legacy template ordering)
        return array_map(
            static fn (array $line): OrderReturnProductForEditing => new OrderReturnProductForEditing(
                $line['id_order_detail'],
                $line['id_customization'],
                $line['product_reference'],
                $line['product_name'],
                $line['product_quantity'],
                $line['id_customization'] !== 0,
                array_map(
                    static fn (array $field): OrderReturnCustomizationFieldForEditing => new OrderReturnCustomizationFieldForEditing($field['type'], $field['value']),
                    $line['customized_data']
                )
            ),
            $this->orderReturnRepository->getProductLines($query->getOrderReturnId())
        );
    }
}
