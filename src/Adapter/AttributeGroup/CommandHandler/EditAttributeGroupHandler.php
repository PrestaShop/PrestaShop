<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Validate\AttributeGroupValidator;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\EditAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler\EditAttributeGroupHandlerInterface;

/**
 * Handles editing of attribute groups using legacy logic.
 */
#[AsCommandHandler]
final class EditAttributeGroupHandler implements EditAttributeGroupHandlerInterface
{
    use LocalizedObjectModelTrait;

    public function __construct(
        private AttributeGroupRepository $attributeGroupRepository,
        private AttributeGroupValidator $validator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditAttributeGroupCommand $command): void
    {
        $attributeGroup = $this->attributeGroupRepository->get($command->getAttributeGroupId());
        $propertiesToUpdate = [];

        if (null !== $command->getLocalizedNames()) {
            $this->fillLocalizedValues($attributeGroup, 'name', $command->getLocalizedNames(), $propertiesToUpdate);
        }
        if (null !== $command->getLocalizedPublicNames()) {
            $this->fillLocalizedValues($attributeGroup, 'public_name', $command->getLocalizedPublicNames(), $propertiesToUpdate);
        }
        if (null !== $command->getType()) {
            $propertiesToUpdate[] = 'group_type';
            $attributeGroup->group_type = $command->getType()->getValue();
            $propertiesToUpdate[] = 'is_color_group';
            $attributeGroup->is_color_group = $attributeGroup->group_type === 'color';
        }
        if (null !== $command->getAssociatedShopIds()) {
            $attributeGroup->id_shop_list = $command->getAssociatedShopIds();
            $propertiesToUpdate[] = 'id_shop_list';
        }

        $this->validator->validate($attributeGroup);
        $this->attributeGroupRepository->partialUpdate($attributeGroup, $propertiesToUpdate);
    }
}
