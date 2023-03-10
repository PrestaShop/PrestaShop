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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;

class AttributeGroupFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var EditableAttributeGroup $editableAttributeGroup */
        $editableAttributeGroup = $this->queryBus->handle(new GetAttributeGroupForEditing((int) $id));

        return [
            'name' => $editableAttributeGroup->getName(),
            'public_name' => $editableAttributeGroup->getPublicName(),
            'group_type' => $editableAttributeGroup->getType(),
            'shop_association' => $editableAttributeGroup->getShopAssociationIds(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'group_type' => AttributeGroupType::ATTRIBUTE_GROUP_TYPE_SELECT,
        ];
    }
}
