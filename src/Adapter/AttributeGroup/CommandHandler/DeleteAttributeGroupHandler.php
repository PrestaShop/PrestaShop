<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\AbstractAttributeGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\CommandHandler\DeleteAttributeGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\DeleteAttributeGroupException;

/**
 * Handles command which deletes attribute group using legacy object model
 */
#[AsCommandHandler]
final class DeleteAttributeGroupHandler extends AbstractAttributeGroupHandler implements DeleteAttributeGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttributeGroupException
     */
    public function handle(DeleteAttributeGroupCommand $command)
    {
        $attributeGroupId = $command->getAttributeGroupId();
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        if (false === $this->deleteAttributeGroup($attributeGroup)) {
            throw new DeleteAttributeGroupException(sprintf('Failed deleting attribute group with id "%s"', $attributeGroupId->getValue()), DeleteAttributeGroupException::FAILED_DELETE);
        }
    }
}
