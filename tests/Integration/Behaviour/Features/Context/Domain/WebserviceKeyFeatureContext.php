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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
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
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use WebserviceKey;

class WebserviceKeyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I specify following properties for new webservice key :reference:
     */
    public function specifyPropertiesForWebserviceKey(string $reference, TableNode $node): void
    {
        $data = $node->getRowsHash();

        $data['is_enabled'] = (bool) $data['is_enabled'];
        $data['shop_association'] = [
            SharedStorage::getStorage()->get($data['shop_association']),
        ];

        SharedStorage::getStorage()->set(sprintf('%s_properties', $reference), $data);
    }

    /**
     * @Then /^I specify "(View|Add|Modify|Delete|Fast view)" permission for "([^"]*)" resources for new webservice key "(.*)"$/
     */
    public function specifyResourcePermissions(string $permission, string $resources, string $reference): void
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);

        $permissionsMap = [
            'View' => 'GET',
            'Add' => 'POST',
            'Modify' => 'PUT',
            'Delete' => 'DELETE',
            'Fast view' => 'HEAD',
        ];

        $data['permissions'][$permissionsMap[$permission]] = PrimitiveUtils::castStringArrayIntoArray($resources);

        SharedStorage::getStorage()->set($propertiesKey, $data);
    }

    /**
     * @When I add webservice key :reference with specified properties
     */
    public function addWebserviceKeyFromSpecifiedProperties(string $reference): void
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);

        $command = new AddWebserviceKeyCommand(
            $data['key'],
            $data['description'],
            $data['is_enabled'],
            $data['permissions'],
            $data['shop_association']
        );

        try {
            /** @var WebserviceKeyId $webserviceKeyId */
            $webserviceKeyId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new WebserviceKey($webserviceKeyId->getValue()));
        } catch (Exception $e) {
            $this->setLastException($e);
        }

        SharedStorage::getStorage()->clear($propertiesKey);
    }

    /**
     * @When I edit webservice key :reference with specified properties:
     */
    public function editWebserviceKeyFromSpecifiedProperties(string $reference, TableNode $node): void
    {
        $webserviceKey = SharedStorage::getStorage()->get($reference);

        $data = $node->getRowsHash();

        $command = new EditWebserviceKeyCommand($webserviceKey->id);
        if (isset($data['key'])) {
            $command->setKey($data['key']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (isset($data['is_enabled'])) {
            $command->setStatus((bool) $data['is_enabled']);
        }

        try {
            $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new WebserviceKey($webserviceKey->id));
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
