<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Manufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

class ManufacturerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default language id from configs
     */
    private $defaultLangId;

    /**
     * @var int default shop id from configs
     */
    private $defaultShopId;

    /**
     * Used for storing latest thrown exception
     */
    private $latestException;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
        $this->defaultShopId = $configuration->get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I add new manufacturer :reference with following properties:
     */
    public function createManufacturerWithDefaultLang($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $this->createManufacturerUsingCommand($reference, $data);
    }

    /**
     * @When I add new manufacturer :manufacturer with empty name
     */
    public function createManufacturerWithEmptyName($reference)
    {
        $data = $this->getValidDataForManufacturerCreation();
        try {
            $data['name'] = '';
            $this->createManufacturerUsingCommand($reference, $data);
        } catch (ManufacturerException $e) {
            $this->latestException = $e;
        }
    }

    /**
     * @When I edit manufacturer :reference with following properties:
     */
    public function editManufacturerWithDefaultLang($reference, TableNode $node)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);
        $manufacturerId = (int) $manufacturer->id;
        $data = $node->getRowsHash();
        $command = new EditManufacturerCommand($manufacturerId);

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled((bool) $data['enabled']);
        }
        if (isset($data['short_description'])) {
            [$this->defaultLangId => $command->setLocalizedShortDescriptions($data['short_description'])];
        }
        if (isset($data['description'])) {
            [$this->defaultLangId => $command->setLocalizedDescriptions($data['description'])];
        }
        if (isset($data['meta_title'])) {
            [$this->defaultLangId => $command->setLocalizedMetaTitles($data['meta_title'])];
        }
        if (isset($data['meta_description'])) {
            [$this->defaultLangId => $command->setLocalizedMetaDescriptions($data['meta_description'])];
        }
        if (isset($data['meta_keywords'])) {
            [$this->defaultLangId => $command->setLocalizedMetaKeywords($data['meta_keywords'])];
        }

        $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId));
    }

    /**
     * @When I delete manufacturer with id :id
     */
    public function deleteManufacturerById($id)
    {
        $command = new DeleteManufacturerCommand((int) $id);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then manufacturer :reference name should be :name
     */
    public function assertManufacturerName($reference, $name)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->name !== $name) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" name, but "%s" was expected.',
                $reference,
                $manufacturer->name,
                $name
            ));
        }
    }

    /**
     * @Then manufacturer :reference :field in default language should be :value
     */
    public function assertFieldValue($reference, $field, $value)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" %s, but "%s" was expected.',
                $reference,
                $manufacturer->$field[$this->defaultLangId],
                $field,
                $value
            ));
        }
    }

    /**
     * @Then manufacturer :reference :field field in default language should be empty
     */
    public function assertFieldIsEmpty($reference, $field)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== '') {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" %s, but it was expected to be empty',
                $reference,
                $manufacturer->$field[$this->defaultLangId],
                $field
            ));
        }
    }

    /**
     * @When I toggle manufacturer :reference status
     */
    public function toggleStatus($reference)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);
        $manufacturerId = (int) $manufacturer->id;
        $expectedStatus = !(bool) $manufacturer->active;

        $this->getCommandBus()->handle(new ToggleManufacturerStatusCommand($manufacturerId, $expectedStatus));

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId));
    }

    /**
     * @When I :action manufacturers with ids: :ids in bulk action
     */
    public function bulkToggleStatusByIds($action, $ids)
    {
        $expectedStatus = 'enable' === $action ? true : false;

        $ids = array_map('intval', explode(',', $ids));

        $this->getQueryBus()->handle(new BulkToggleManufacturerStatusCommand($ids, $expectedStatus));
    }

    /**
     * @Given manufacturers with ids: :ids exists
     */
    public function assertManufacturersWithIdsExists($ids)
    {
        foreach (explode(',', $ids) as $id) {
            $this->assertManufacturerExistsById($id);
        }
    }

    /**
     * @Then manufacturers with ids: :ids should not be found
     */
    public function assertManufacturersNotFoundByIds($ids)
    {
        foreach (explode(',', $ids) as $id) {
            $this->assertManufacturerNotFoundById($id);
        }
    }

    /**
     * @Given manufacturers with ids: :ids should be :expectedStatus
     */
    public function assertManufacturersStatusByIds($ids, $expectedStatus)
    {
        $isEnabled = 'enabled' === $expectedStatus ? true : false;

        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            /** @var EditableManufacturer $editableManufacturer */
            $editableManufacturer = $this->getQueryBus()->handle(
                new GetManufacturerForEditing((int) $id)
            );
            if ($isEnabled !== $editableManufacturer->isEnabled()) {
                throw new RuntimeException(sprintf(
                    'Manufacturer with id "%s" expected to be %s, but it is %s',
                    $editableManufacturer->getManufacturerId()->getValue(),
                    $expectedStatus,
                    $editableManufacturer->isEnabled() ? 'enabled' : 'disabled'
                ));
            }
        }
    }

    /**
     * @When I bulk delete manufacturers with ids: :ids
     */
    public function deleteManufacturersByIds($ids)
    {
        $ids = array_map('intval', explode(',', $ids));
        $this->getQueryBus()->handle(new BulkDeleteManufacturerCommand($ids));
    }

    /**
     * @Then /^manufacturer "(.*)" should be (enabled|disabled)?$/
     */
    public function assertStatus($reference, $expectedStatus)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);

        $isEnabled = 'enabled' === $expectedStatus ? true : false;
        $actualStatus = (bool) $manufacturer->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" is %s, but it was expected to be %s',
                $reference,
                $actualStatus ? 'enabled' : 'disabled',
                $expectedStatus
            ));
        }
    }

    /**
     * @Given manufacturer with id :id exists
     */
    public function assertManufacturerExistsById($id)
    {
        $this->getQueryBus()->handle(new GetManufacturerForEditing((int) $id));
    }

    /**
     * @Then manufacturer with id :id should not be found
     */
    public function assertManufacturerNotFoundById($id)
    {
        try {
            $query = new GetManufacturerForEditing((int) $id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Manufacturer with id %s exists', $id));
        } catch (ManufacturerNotFoundException $e) {
        }
    }

    /**
     * @Then /^I should get error message '(.+)'$/
     */
    public function assertExceptionWasThrown($message)
    {
        if ($this->latestException instanceof \Exception) {
            if ($this->latestException->getMessage() !== $message) {
                throw new RuntimeException(sprintf(
                        'Got error message "%s", but expected %s', $this->latestException->getMessage(), $message)
                );
            }

            return true;
        }
        throw new NoExceptionAlthoughExpectedException('No exception was thrown in latest result');
    }

    /**
     * @param $reference
     * @param array $data
     */
    private function createManufacturerUsingCommand($reference, array $data)
    {
        $command = new AddManufacturerCommand(
            $data['name'],
            $data['enabled'],
            [$this->defaultLangId => $data['short_description']],
            [$this->defaultLangId => $data['description']],
            [$this->defaultLangId => $data['meta_title']],
            [$this->defaultLangId => $data['meta_description']],
            [$this->defaultLangId => $data['meta_keywords']],
            [$this->defaultShopId]
        );

        /**
         * @var ManufacturerId
         */
        $manufacturerId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId->getValue()));
    }

    private function getValidDataForManufacturerCreation()
    {
        return [
            'name' => 'best-shoes',
            'short_description' => 'Makes best shoes in Europe',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.',
            'meta_title' => 'Perfect quality shoes',
            'meta_description' => '',
            'meta_keywords' => 'Boots, shoes, slippers',
            'enabled' => 1,
        ];
    }
}
