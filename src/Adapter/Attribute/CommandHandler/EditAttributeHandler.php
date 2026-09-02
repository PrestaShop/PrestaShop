<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Attribute\Validate\AttributeValidator;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Adapter\File\Uploader\AttributeFileUploader;
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
        private AttributeValidator $attributeValidator,
        private AttributeFileUploader $attributeFileUploader
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

        if (null !== $command->getTextureFilePath()) {
            $this->attributeFileUploader->deleteOldFile($command->getAttributeId()->getValue());
            $this->attributeFileUploader->upload($command->getTextureFilePath(), $command->getAttributeId()->getValue());
        }

        $this->attributeValidator->validate($attribute);
        $this->attributeRepository->partialUpdate($attribute, $propertiesToUpdate);
    }
}
