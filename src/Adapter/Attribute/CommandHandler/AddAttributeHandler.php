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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler\AddAttributeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use ProductAttribute;

/**
 * Handles adding of attribute value using legacy logic.
 */
#[AsCommandHandler]
class AddAttributeHandler implements AddAttributeHandlerInterface
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly AttributeValidator $attributeValidator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddAttributeCommand $command): AttributeId
    {
        $attribute = new ProductAttribute();

        $attribute->name = $command->getLocalizedNames();
        $attribute->id_shop_list = $command->getAssociatedShopIds();

        if (!empty($command->getColor())) {
            $attribute->color = $command->getColor();
        }

        $attribute->id_attribute_group = $command->getAttributeGroupId()->getValue();

        $this->attributeValidator->validate($attribute);

        $id = $this->attributeRepository->add($attribute);

        return $id;
    }
}
