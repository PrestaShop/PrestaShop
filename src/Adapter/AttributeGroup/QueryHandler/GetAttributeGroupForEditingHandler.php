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

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryHandler\GetAttributeGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles query which gets attribute group for editing
 */
#[AsQueryHandler]
final class GetAttributeGroupForEditingHandler implements GetAttributeGroupForEditingHandlerInterface
{
    private AttributeGroupRepository $attributeGroupRepository;

    private ShopContext $shopContext;

    public function __construct(AttributeGroupRepository $attributeGroupRepository, ShopContext $shopContext)
    {
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->shopContext = $shopContext;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetAttributeGroupForEditing $query): EditableAttributeGroup
    {
        $attributeGroup = $this->attributeGroupRepository->get(
            $query->getAttributeGroupId(),
            new ShopId($this->shopContext->getId())
        );

        return new EditableAttributeGroup(
            $query->getAttributeGroupId()->getValue(),
            $attributeGroup->name,
            $attributeGroup->public_name,
            $attributeGroup->group_type,
            $attributeGroup->getAssociatedShops()
        );
    }
}
