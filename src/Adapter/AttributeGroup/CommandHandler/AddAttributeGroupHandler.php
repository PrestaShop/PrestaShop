<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
