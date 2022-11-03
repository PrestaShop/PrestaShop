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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\EditWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use WebserviceKey;

class WebserviceKeyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I add a new webservice key with specified properties:
     */
    public function addNewWebserviceKey(TableNode $node): void
    {
        $data = $node->getRowsHash();

        $data['shop_association'] = [
            SharedStorage::getStorage()->get($data['shop_association']),
        ];
        $data['permissions'] = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 11) !== 'permission_') {
                continue;
            }
            $data['permissions'][substr($key, 11)] = PrimitiveUtils::castStringArrayIntoArray($value);
        }

        $command = new AddWebserviceKeyCommand(
            $data['key'],
            $data['description'],
            (bool) $data['is_enabled'],
            $data['permissions'],
            $data['shop_association']
        );

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit webservice key :reference with specified properties:
     */
    public function editWebserviceKeyFromSpecifiedProperties(string $reference, TableNode $node): void
    {
        $webserviceKeyId = (int) WebserviceKey::getIdFromKey($reference);

        $data = $node->getRowsHash();
        $data['permissions'] = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 11) !== 'permission_') {
                continue;
            }
            $data['permissions'][substr($key, 11)] = PrimitiveUtils::castStringArrayIntoArray($value);
        }

        $command = new EditWebserviceKeyCommand($webserviceKeyId);
        if (isset($data['key'])) {
            $command->setKey($data['key']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (isset($data['is_enabled'])) {
            $command->setStatus((bool) $data['is_enabled']);
        }
        if (!empty($data['permissions'])) {
            $command->setPermissions($data['permissions']);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that webservice key is duplicate
     */
    public function assertLastErrorIsDuplicateWebserviceKey(): void
    {
        $this->assertLastErrorIs(DuplicateWebserviceKeyException::class);
    }
}
