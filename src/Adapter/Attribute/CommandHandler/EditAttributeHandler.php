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

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Attribute\Validate\AttributeValidator;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler\EditAttributeHandlerInterface;

/**
 * Handles editing of attribute groups using legacy logic.
 */
#[AsCommandHandler]
class EditAttributeHandler implements EditAttributeHandlerInterface
{
    use LocalizedObjectModelTrait;

    public function __construct(
        private AttributeRepository $attributeRepository,
        private AttributeValidator $attributeValidator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditAttributeCommand $command): void
    {
        $attribute = $this->attributeRepository->get($command->getAttributeId());
        $propertiesToUpdate = [];

        if (null !== $command->getLocalizedNames()) {
            $this->fillLocalizedValues($attribute, 'name', $command->getLocalizedNames(), $propertiesToUpdate);
        }

        if (null !== $command->getColor()) {
            $attribute->color = $command->getColor();
            $propertiesToUpdate[] = 'color';
        }

        if (null !== $command->getAttributeGroupId()) {
            $attribute->id_attribute_group = $command->getAttributeGroupId()->getValue();
            $propertiesToUpdate[] = 'id_attribute_group';
        }

        if (null !== $command->getAssociatedShopIds()) {
            $attribute->id_shop_list = $command->getAssociatedShopIds();
            $propertiesToUpdate[] = 'id_shop_list';
        }

        $this->attributeValidator->validate($attribute);
        $this->attributeRepository->partialUpdate($attribute, $propertiesToUpdate);
    }
}
