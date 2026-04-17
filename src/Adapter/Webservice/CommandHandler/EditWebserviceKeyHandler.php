<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\EditWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler\EditWebserviceKeyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceKeyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;
use WebserviceKey;

/**
 * Handles command that edits legacy WebserviceKey
 *
 * @internal
 */
#[AsCommandHandler]
final class EditWebserviceKeyHandler extends AbstractWebserviceKeyHandler implements EditWebserviceKeyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditWebserviceKeyCommand $command)
    {
        $webserviceKey = $this->getLegacyWebserviceKey($command->getWebserviceKeyId());

        $this->updateLegacyWebserviceKeyWithCommandData($webserviceKey, $command);
    }

    /**
     * @param WebserviceKeyId $webserviceKeyId
     *
     * @return WebserviceKey
     */
    private function getLegacyWebserviceKey(WebserviceKeyId $webserviceKeyId)
    {
        $webserviceKey = new WebserviceKey($webserviceKeyId->getValue());

        if ($webserviceKeyId->getValue() !== $webserviceKey->id) {
            throw new WebserviceKeyNotFoundException(sprintf('Webservice key with id "%s was not found', $webserviceKeyId->getValue()));
        }

        return $webserviceKey;
    }

    /**
     * @param WebserviceKey $webserviceKey
     * @param EditWebserviceKeyCommand $command
     */
    private function updateLegacyWebserviceKeyWithCommandData(
        WebserviceKey $webserviceKey,
        EditWebserviceKeyCommand $command
    ) {
        if (null !== $command->getKey()) {
            $webserviceKey->key = $command->getKey()->getValue();
        }

        if (null !== $command->getDescription()) {
            $webserviceKey->description = $command->getDescription();
        }

        if (null !== $command->getStatus()) {
            $webserviceKey->active = $command->getStatus();
        }

        if (false === $webserviceKey->validateFields(false)) {
            throw new WebserviceConstraintException('One or more fields are invalid in WebserviceKey');
        }

        if (false === $webserviceKey->update()) {
            throw new WebserviceException(sprintf('Failed to update WebserviceKey with id "%s"', $webserviceKey->id));
        }

        if (null !== $command->getShopAssociation()) {
            $this->associateWithShops($webserviceKey, $command->getShopAssociation());
        }

        if (null !== $command->getPermissions()) {
            $this->setPermissionsForWebserviceKey($webserviceKey, $command->getPermissions());
        }
    }
}
