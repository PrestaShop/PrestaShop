<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\AbstractAttributeHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\BulkDeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler\BulkDeleteAttributeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\DeleteAttributeException;

/**
 * Handles command which deletes attributes in bulk action using legacy object model
 */
#[AsCommandHandler]
final class BulkDeleteAttributeHandler extends AbstractAttributeHandler implements BulkDeleteAttributeHandlerInterface
{
    /**
     * @param BulkDeleteAttributeCommand $command
     *
     * @throws AttributeException
     */
    public function handle(BulkDeleteAttributeCommand $command)
    {
        foreach ($command->getAttributeIds() as $attributeId) {
            $attribute = $this->getAttributeById($attributeId);

            if (false === $this->deleteAttribute($attribute)) {
                throw new DeleteAttributeException(sprintf('Failed to delete attribute with id "%s"', $attribute->id), DeleteAttributeException::FAILED_BULK_DELETE);
            }
        }
    }
}
