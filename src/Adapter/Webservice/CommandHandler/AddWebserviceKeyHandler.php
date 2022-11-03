<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

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
