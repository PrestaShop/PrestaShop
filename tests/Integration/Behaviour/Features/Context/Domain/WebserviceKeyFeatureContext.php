<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\BulkDeleteWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\DeleteWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\EditWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use RuntimeException;
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
     * @When I delete webservice key :reference
     */
    public function deleteWebserviceKeyUsingCommand(string $reference): void
    {
        $this->getCommandBus()->handle(
            new DeleteWebserviceKeyCommand((int) WebserviceKey::getIdFromKey($reference))
        );
    }

    /**
     * @Then I bulk delete webservice keys :references
     */
    public function bulkDeleteWebserviceKeyeUsingCommand(string $references): void
    {
        $references = explode(',', $references);
        $webserviceKeyIds = [];
        foreach ($references as $reference) {
            $webserviceKeyIds[] = (int) WebserviceKey::getIdFromKey($reference);
        }
        $this->getCommandBus()->handle(
            new BulkDeleteWebserviceKeyCommand($webserviceKeyIds)
        );
    }

    /**
     * @When webservice key :reference should not exist
     */
    public function assertWebserviceKeyDoesNotExist(string $reference): void
    {
        $wkExists = WebserviceKey::keyExists($reference);
        if ($wkExists) {
            throw new RuntimeException(sprintf('Webservice Key %s still exists', $reference));
        }
    }

    /**
     * @When webservice key :reference should exist
     */
    public function assertWebserviceKeyDoesExist(string $reference): void
    {
        $wkExists = WebserviceKey::keyExists($reference);
        if (!$wkExists) {
            throw new RuntimeException(sprintf('Webservice Key %s doesn\'t exists', $reference));
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
