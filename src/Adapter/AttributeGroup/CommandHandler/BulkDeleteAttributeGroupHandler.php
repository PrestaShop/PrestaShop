<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\AbstractAttributeGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\BulkDeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler\BulkDeleteAttributeGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\DeleteAttributeGroupException;

/**
 * Handles command which deletes multiple attribute groups using legacy object model
 */
#[AsCommandHandler]
final class BulkDeleteAttributeGroupHandler extends AbstractAttributeGroupHandler implements BulkDeleteAttributeGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteAttributeGroupCommand $command)
    {
        foreach ($command->getAttributeGroupIds() as $attributeGroupId) {
            $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

            if (false === $this->deleteAttributeGroup($attributeGroup)) {
                throw new DeleteAttributeGroupException(sprintf('Failed to delete attribute group with id "%s"', $attributeGroupId->getValue()), DeleteAttributeGroupException::FAILED_BULK_DELETE);
            }
        }
    }
}
