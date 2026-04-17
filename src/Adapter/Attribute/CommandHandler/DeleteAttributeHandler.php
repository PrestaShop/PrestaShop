<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Attribute\AbstractAttributeHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\DeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\CommandHandler\DeleteAttributeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\DeleteAttributeException;

/**
 * Handles command which deletes the Attribute using legacy object model
 */
#[AsCommandHandler]
final class DeleteAttributeHandler extends AbstractAttributeHandler implements DeleteAttributeHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AttributeException
     */
    public function handle(DeleteAttributeCommand $command)
    {
        $attribute = $this->getAttributeById($command->getAttributeId());

        if (false === $this->deleteAttribute($attribute)) {
            throw new DeleteAttributeException(sprintf('Failed to delete attribute with id "%s".', $attribute->id), DeleteAttributeException::FAILED_DELETE);
        }
    }
}
