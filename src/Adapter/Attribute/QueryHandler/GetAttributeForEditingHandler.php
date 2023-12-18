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

namespace PrestaShop\PrestaShop\Adapter\Attribute\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryHandler\GetAttributeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\EditableAttribute;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Handles query which gets attribute group for editing
 */
#[AsQueryHandler]
final class GetAttributeForEditingHandler implements GetAttributeForEditingHandlerInterface
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetAttributeForEditing $query): EditableAttribute
    {
        $attributeId = $query->getAttributeId();
        $attribute = $this->attributeRepository->get($attributeId);

        return new EditableAttribute(
            $attributeId->getValue(),
            (int) $attribute->id_attribute_group,
            $attribute->name,
            $attribute->color,
            $attribute->getAssociatedShops()
        );
    }
}
