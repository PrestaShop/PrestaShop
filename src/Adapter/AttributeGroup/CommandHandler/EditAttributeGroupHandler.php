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

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\EditAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler\EditAttributeGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\CannotUpdateAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Handles editing of attribute groups using legacy logic.
 */
final class EditAttributeGroupHandler extends AbstractObjectModelHandler implements EditAttributeGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditAttributeGroupCommand $command): AttributeGroupId
    {
        $attributeGroup = new \AttributeGroup($command->getAttributeGroupId()->getValue());

        $attributeGroup->name = $command->getLocalizedNames();
        $attributeGroup->public_name = $command->getLocalizedPublicNames();
        $attributeGroup->group_type = $command->getType();

        if (false === $attributeGroup->validateFields(false)) {
            throw new AttributeGroupConstraintException('Invalid attribute data');
        }

        if (false === $attributeGroup->validateFieldsLang(false)) {
            throw new AttributeGroupConstraintException('Invalid attribute group data', AttributeGroupConstraintException::INVALID_NAME);
        }

        if (false === $attributeGroup->update()) {
            throw new CannotUpdateAttributeGroupException('Unable to update attribute group');
        }

        $this->associateWithShops($attributeGroup, $command->getShopAssociation());

        return new AttributeGroupId((int) $attributeGroup->id);
    }
}
