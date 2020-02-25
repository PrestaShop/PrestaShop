<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

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
     * @When I edit manufacturer :reference with following properties:
     */
    public function editManufacturerWithDefaultLang($reference, TableNode $node)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);

        $manufacturerId = (int) $manufacturer->id;
        $data = $node->getRowsHash();
        $command = new EditManufacturerCommand($manufacturerId);

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
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
     * @When I delete manufacturer :manufacturerReference
     */
    public function deleteManufacturer($manufacturerReference)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $this->getCommandBus()->handle(new DeleteManufacturerCommand((int) $manufacturer->id));
    }

    /**
     * @When I delete manufacturers: :manufacturerReferences using bulk action
     */
    public function bulkDeleteManufacturers($manufacturerReferences)
    {
        $manufacturerIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $manufacturerIds[] = (int) SharedStorage::getStorage()->get($manufacturerReference)->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteManufacturerCommand($manufacturerIds));
    }

    /**
     * @Then manufacturers: :manufacturerReferences should be deleted
     */
    public function assertManufacturersAreDeleted($manufacturerReferences)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $this->assertManufacturerIsDeleted($manufacturerReference);
        }
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
     * @When /^I (enable|disable)? manufacturer "(.*)"$/
     */
    public function toggleStatus($action, $reference)
    {
        $expectedStatus = 'enable' === $action;

        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);
        $manufacturerId = (int) $manufacturer->id;

        $this->getCommandBus()->handle(new ToggleManufacturerStatusCommand($manufacturerId, $expectedStatus));

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId));
    }

    /**
     * @When /^I (enable|disable) multiple manufacturers: "(.+)" using bulk action$/
     */
    public function bulkToggleStatus($action, $manufacturerReferences)
    {
        $expectedStatus = 'enable' === $action;
        $manufacturerIdsByReference = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);
            $manufacturerIdsByReference[$manufacturerReference] = (int) $manufacturer->id;
        }

        $this->getQueryBus()->handle(new BulkToggleManufacturerStatusCommand(
            $manufacturerIdsByReference,
            $expectedStatus
        ));

        foreach ($manufacturerIdsByReference as $reference => $id) {
            SharedStorage::getStorage()->set($reference, new Manufacturer($id));
        }
    }

    /**
     * @Given /^manufacturers: "(.+)" should be (enabled|disabled)$/
     */
    public function assertMultipleManufacturersStatus($manufacturerReferences, $expectedStatus)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $this->assertStatus($manufacturerReference, $expectedStatus);
        }
    }

    /**
     * @Given /^manufacturer "(.*)" is (enabled|disabled)?$/
     * @Then /^manufacturer "(.*)" should be (enabled|disabled)?$/
     */
    public function assertStatus($manufacturerReference, $expectedStatus)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $manufacturer->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" is %s, but it was expected to be %s',
                $manufacturerReference,
                $actualStatus ? 'enabled' : 'disabled',
                $expectedStatus
            ));
        }
    }

    /**
     * @Then manufacturer :manufacturerReference should be deleted
     */
    public function assertManufacturerIsDeleted($manufacturerReference)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        try {
            $query = new GetManufacturerForEditing((int) $manufacturer->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf(
                'Manufacturer %s exists, but it was expected to be deleted',
                $manufacturerReference
            ));
        } catch (ManufacturerNotFoundException $e) {
            SharedStorage::getStorage()->clear($manufacturerReference);
        }
    }

    /**
     * @param $reference
     * @param array $data
     */
    private function createManufacturerUsingCommand($reference, array $data)
    {
        $command = new AddManufacturerCommand(
            $data['name'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
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
}
