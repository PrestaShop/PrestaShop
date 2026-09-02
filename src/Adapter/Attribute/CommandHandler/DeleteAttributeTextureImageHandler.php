<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\AbstractAttributeHandler;
use PrestaShop\PrestaShop\Adapter\File\Uploader\AttributeFileUploader;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler\DeleteAttributeTextureImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeTextureImageCommand;

/**
 * Handles command which deletes the Attribute using legacy object model
 */
#[AsCommandHandler]
final class DeleteAttributeTextureImageHandler extends AbstractAttributeHandler implements DeleteAttributeTextureImageHandlerInterface
{
    public function __construct(
        private AttributeFileUploader $attributeFileUploader
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteAttributeTextureImageCommand $command)
    {
        $attribute = $this->getAttributeById($command->getAttributeId());

        $this->attributeFileUploader->deleteOldFile($attribute->id);
    }
}
