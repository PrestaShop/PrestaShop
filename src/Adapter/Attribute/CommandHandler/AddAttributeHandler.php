<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Attribute\Validate\AttributeValidator;
use PrestaShop\PrestaShop\Adapter\File\Uploader\AttributeFileUploader;
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
        private readonly AttributeFileUploader $attributeFileUploader
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

        if (null !== $command->getTextureFilePath()) {
            $this->attributeFileUploader->upload(
                $command->getTextureFilePath(),
                $id->getValue()
            );
        }

        return $id;
    }
}
