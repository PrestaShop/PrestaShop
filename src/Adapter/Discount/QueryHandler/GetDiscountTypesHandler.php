<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountTypes;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryHandler\GetDiscountTypesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult\DiscountType;

#[AsQueryHandler]
class GetDiscountTypesHandler implements GetDiscountTypesHandlerInterface
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository,
    ) {
    }

    /**
     * @return DiscountType[]
     */
    public function handle(GetDiscountTypes $query): array
    {
        $groupedTypes = $this->discountTypeRepository->getAllTypes();

        return array_map(
            fn (array $type) => new DiscountType(
                $type['id_cart_rule_type'],
                $type['discount_type'],
                $type['names'],
                $type['descriptions'],
                $type['is_core'],
                $type['enabled']
            ),
            array_values($groupedTypes)
        );
    }
}
