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

use AttributeGroup;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Validate\AttributeGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\AddAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler\AddAttributeGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;

/**
 * Handles adding of attribute groups using legacy logic.
 */
#[AsCommandHandler]
final class AddAttributeGroupHandler implements AddAttributeGroupHandlerInterface
{
    public function __construct(
        private AttributeGroupRepository $attributeGroupRepository,
        private AttributeGroupValidator $validator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddAttributeGroupCommand $command): AttributeGroupId
    {
        $attributeGroup = new AttributeGroup();

        $attributeGroup->name = $command->getLocalizedNames();
        $attributeGroup->public_name = $command->getLocalizedPublicNames();
        $attributeGroup->group_type = $command->getType()->getValue();
        $attributeGroup->id_shop_list = $command->getAssociatedShopIds();

        $this->validator->validate($attributeGroup);
        $id = $this->attributeGroupRepository->add($attributeGroup);

        return $id;
    }
}
