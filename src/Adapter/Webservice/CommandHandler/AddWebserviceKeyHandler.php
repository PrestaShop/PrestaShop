<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler\AddWebserviceKeyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;
use WebserviceKey;

/**
 * Handles command that adds new webservice key for PrestaShop's API
 *
 * @internal
 */
#[AsCommandHandler]
final class AddWebserviceKeyHandler extends AbstractWebserviceKeyHandler implements AddWebserviceKeyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddWebserviceKeyCommand $command)
    {
        $this->assertWebserviceKeyIsNotDuplicate($command->getKey());

        $webserviceKey = $this->createLegacyWebserviceKeyFromCommand($command);

        $this->associateWithShops($webserviceKey, $command->getAssociatedShops());
        $this->setPermissionsForWebserviceKey($webserviceKey, $command->getPermissions());

        return new WebserviceKeyId((int) $webserviceKey->id);
    }

    /**
     * Asserts that new webservice key does not duplicate already existing keys
     *
     * @param Key $key
     */
    private function assertWebserviceKeyIsNotDuplicate(Key $key)
    {
        if (WebserviceKey::keyExists($key->getValue())) {
            throw new DuplicateWebserviceKeyException(sprintf('Webservice key "%s" already exists', $key->getValue()));
        }
    }

    /**
     * @param AddWebserviceKeyCommand $command
     *
     * @return WebserviceKey
     */
    private function createLegacyWebserviceKeyFromCommand(AddWebserviceKeyCommand $command)
    {
        $webserviceKey = new WebserviceKey();
        $webserviceKey->key = $command->getKey()->getValue();
        $webserviceKey->description = $command->getDescription();
        $webserviceKey->active = $command->getStatus();

        if (false === $webserviceKey->validateFields(false)) {
            throw new WebserviceConstraintException('One or more fields are invalid in WebserviceKey');
        }

        if (false === $webserviceKey->add()) {
            throw new WebserviceException('Failed to add WebserviceKey');
        }

        return $webserviceKey;
    }
}
