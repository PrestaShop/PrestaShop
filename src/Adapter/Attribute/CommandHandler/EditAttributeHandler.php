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

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\Attribute\CommandHandler\EditAttributeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Attribute\Exception\CannotUpdateAttributeException;
use PrestaShop\PrestaShop\Core\Domain\Attribute\ValueObject\AttributeId;

/**
 * Handles editing of attribute groups using legacy logic.
 */
final class EditAttributeHandler extends AbstractObjectModelHandler implements EditAttributeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditAttributeCommand $command): AttributeId
    {
        $attribute = new \ProductAttribute($command->getAttributeId()->getValue());

        $attribute->name = $command->getLocalizedValue();
        $attribute->color = $command->getColor();
        $attribute->id_attribute_group = $command->getAttributeGroupId()->getValue();

        if (false === $attribute->validateFields(false)) {
            throw new AttributeConstraintException('Invalid attribute data');
        }

        if (false === $attribute->validateFieldsLang(false)) {
            throw new AttributeConstraintException('Invalid attribute data', AttributeConstraintException::INVALID_NAME);
        }

        if (false === $attribute->update()) {
            throw new CannotUpdateAttributeException('Unable to update attribute');
        }

        $this->associateWithShops($attribute, $command->getShopAssociation());

        return new AttributeId((int) $attribute->id);
    }
}
