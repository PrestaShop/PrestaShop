<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\QueryHandler\AbstractAttributeGroupQueryHandler;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryHandler\GetProductAttributeGroupsHandlerInterface;

/**
 * Handles the query GetProductAttributeGroups using adapter repository
 */
#[AsQueryHandler]
class GetProductAttributeGroupsHandler extends AbstractAttributeGroupQueryHandler implements GetProductAttributeGroupsHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeGroupRepository $attributeGroupRepository,
        ProductRepository $productRepository
    ) {
        parent::__construct($attributeRepository, $attributeGroupRepository);
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetProductAttributeGroups $query): array
    {
        $shopConstraint = $query->getShopConstraint();
        $productId = $query->getProductId();
        $attributeGroupIds = $this->productRepository->getProductAttributesGroupIds($productId, $shopConstraint);

        if (empty($attributeGroupIds)) {
            return [];
        }

        $attributeIds = $this->productRepository->getProductAttributesIds($productId, $shopConstraint);

        if (empty($attributeIds)) {
            return [];
        }

        $attributeGroups = $this->attributeGroupRepository->getAttributeGroups($shopConstraint, $attributeGroupIds);

        return $this->formatAttributeGroupsList(
            $attributeGroups,
            $this->attributeRepository->getGroupedAttributes(
                $shopConstraint,
                $attributeGroupIds,
                $attributeIds
            )
        );
    }
}
