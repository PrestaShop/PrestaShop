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
