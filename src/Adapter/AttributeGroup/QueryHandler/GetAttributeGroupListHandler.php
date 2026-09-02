<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryHandler\GetAttributeGroupListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Handles the query GetAttributeGroupList using Doctrine repository
 */
#[AsQueryHandler]
class GetAttributeGroupListHandler extends AbstractAttributeGroupQueryHandler implements GetAttributeGroupListHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(GetAttributeGroupList $query): array
    {
        $shopConstraint = $query->getShopConstraint();
        $attributeGroups = $this->attributeGroupRepository->getAttributeGroups($shopConstraint);

        return $this->formatAttributeGroupsList(
            $attributeGroups,
            $this->attributeRepository->getGroupedAttributes(
                $shopConstraint,
                array_map(static function (int $id): AttributeGroupId {
                    return new AttributeGroupId($id);
                }, array_keys($attributeGroups))
            )
        );
    }
}
