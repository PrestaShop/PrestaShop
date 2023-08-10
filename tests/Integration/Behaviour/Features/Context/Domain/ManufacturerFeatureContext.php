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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use AdminController;
use Behat\Gherkin\Node\TableNode;
use Context;
use FrontController;
use Manufacturer;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\EditManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\ViewableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use RuntimeException;
use stdClass;
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
     * Needed for getting Viewable objects from handlers, for example ViewableManufacturer
     *
     * @BeforeScenario
     */
    public function before()
    {
        // needed because if no controller defined then CONTEXT_ALL is selected and exception is thrown
        /** @var AdminController|FrontController $adminControllerTestDouble */
        $adminControllerTestDouble = new stdClass();
        $adminControllerTestDouble->controller_type = 'admin';
        $adminControllerTestDouble->php_self = 'dummyTestDouble';
        Context::getContext()->controller = $adminControllerTestDouble;
    }

    /**
     * @When I add new manufacturer :reference with following properties:
     */
    public function createManufacturerWithDefaultLang(string $reference, TableNode $node): void
    {
        $data = $node->getRowsHash();

        $this->createManufacturerUsingCommand($reference, $data);
    }

    /**
     * @When I edit manufacturer :reference with following properties:
     */
    public function editManufacturerWithDefaultLang(string $reference, TableNode $node): void
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
        if (isset($data['logo image'])) {
            $this->pretendImageUploaded(_PS_MANU_IMG_DIR_, $data['logo image'], $manufacturerId);
        }

        $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId));
    }

    /**
     * @When I delete manufacturer :manufacturerReference
     */
    public function deleteManufacturer(string $manufacturerReference): void
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $this->getCommandBus()->handle(new DeleteManufacturerCommand((int) $manufacturer->id));
    }

    /**
     * @When I delete manufacturers: :manufacturerReferences using bulk action
     */
    public function bulkDeleteManufacturers(string $manufacturerReferences): void
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
    public function assertManufacturersAreDeleted(string $manufacturerReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $this->assertManufacturerIsDeleted($manufacturerReference);
        }
    }

    /**
     * @Then manufacturer :reference name should be :name
     */
    public function assertManufacturerName(string $reference, string $name): void
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->name !== $name) {
            throw new RuntimeException(sprintf('Manufacturer "%s" has "%s" name, but "%s" was expected.', $reference, $manufacturer->name, $name));
        }
    }

    /**
     * @Then manufacturer :reference :field in default language should be :value
     */
    public function assertFieldValue(string $reference, $field, $value)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf('Manufacturer "%s" has "%s" %s, but "%s" was expected.', $reference, $manufacturer->$field[$this->defaultLangId], $field, $value));
        }
    }

    /**
     * @Then manufacturer :reference :field field in default language should be empty
     */
    public function assertFieldIsEmpty(string $reference, $field)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== '') {
            throw new RuntimeException(sprintf('Manufacturer "%s" has "%s" %s, but it was expected to be empty', $reference, $manufacturer->$field[$this->defaultLangId], $field));
        }
    }

    /**
     * @When /^I (enable|disable)? manufacturer "(.*)"$/
     */
    public function toggleStatus(string $action, string $reference)
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
    public function bulkToggleStatus(string $action, string $manufacturerReferences)
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
    public function assertMultipleManufacturersStatus(string $manufacturerReferences, string $expectedStatus)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $this->assertStatus($manufacturerReference, $expectedStatus);
        }
    }

    /**
     * @Given /^manufacturer "(.*)" is (enabled|disabled)?$/
     * @Then /^manufacturer "(.*)" should be (enabled|disabled)?$/
     */
    public function assertStatus(string $manufacturerReference, string $expectedStatus)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $manufacturer->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf('Manufacturer "%s" is %s, but it was expected to be %s', $manufacturerReference, $actualStatus ? 'enabled' : 'disabled', $expectedStatus));
        }
    }

    /**
     * @Then manufacturer :manufacturerReference should be deleted
     */
    public function assertManufacturerIsDeleted(string $manufacturerReference)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        try {
            $this->getEditableManufacturer((int) $manufacturer->id);

            throw new NoExceptionAlthoughExpectedException(sprintf('Manufacturer %s exists, but it was expected to be deleted', $manufacturerReference));
        } catch (ManufacturerNotFoundException $e) {
            SharedStorage::getStorage()->clear($manufacturerReference);
        }
    }

    /**
     * @Then I should get error that manufacturer does not exist
     */
    public function assertManufacturerDoesNotExistError()
    {
        $this->assertLastErrorIs(ManufacturerNotFoundException::class);
    }

    /**
     * @Given manufacturer :manufacturerReference named :name exists
     *
     * @param string $name
     * @param string $manufacturerReference
     */
    public function assertManufacturerExistsByName(string $name, string $manufacturerReference): void
    {
        if ($manufacturerId = Manufacturer::getIdByName($name)) {
            $this->getSharedStorage()->set($manufacturerReference, $manufacturerId);

            return;
        }

        throw new RuntimeException(sprintf('Manufacturer %s named "%s" does not exist', $manufacturerReference, $name));
    }

    /**
     * @When I delete the manufacturer :manufacturerReference logo image
     *
     * @param string $manufacturerReference
     */
    public function deleteCategoryLogoImage(string $manufacturerReference): void
    {
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $this->getCommandBus()->handle(new DeleteManufacturerLogoImageCommand((int) $manufacturer->id));
    }

    /**
     * @Given the manufacturer :manufacturerReference has a logo image
     *
     * @param string $manufacturerReference
     */
    public function assertManufacturerHasLogoImage(string $manufacturerReference): void
    {
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $editableManufacturer = $this->getEditableManufacturer((int) $manufacturer->id);

        Assert::assertNotNull($editableManufacturer->getLogoImage());
    }

    /**
     * @Then the manufacturer :manufacturerReference does not have a logo image
     *
     * @param string $manufacturerReference
     */
    public function assertManufacturerHasNotLogoImage(string $manufacturerReference)
    {
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);

        $editableManufacturer = $this->getEditableManufacturer((int) $manufacturer->id);
        Assert::assertNull($editableManufacturer->getLogoImage());
    }

    /**
     * @param string $reference
     * @param array $data
     */
    private function createManufacturerUsingCommand(string $reference, array $data): void
    {
        $command = new AddManufacturerCommand(
            $data['name'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
            [$this->defaultLangId => $data['short_description']],
            [$this->defaultLangId => $data['description']],
            [$this->defaultLangId => $data['meta_title']],
            [$this->defaultLangId => $data['meta_description']],
            [$this->defaultShopId]
        );

        /**
         * @var ManufacturerId
         */
        $manufacturerId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId->getValue()));
    }

    /**
     * @Then manufacturer :manufacturerReference should have :countOfAddresses addresses and :countOfProducts products
     *
     * @param string $manufacturerReference
     * @param int $countOfAddresses
     * @param int $countOfProducts
     */
    public function manufacturerShouldHaveAddedAddresses(
        string $manufacturerReference,
        int $countOfAddresses,
        int $countOfProducts)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($manufacturerReference);
        /** @var ViewableManufacturer $viewableMaufacturer */
        $viewableMaufacturer = $this->getQueryBus()->handle(new GetManufacturerForViewing(
            $manufacturer->id,
            (int) $this->getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT')
        ));

        Assert::assertSame($manufacturer->name, $viewableMaufacturer->getName());
        Assert::assertSame($countOfAddresses, count($viewableMaufacturer->getManufacturerAddresses()));
        Assert::assertSame($countOfProducts, count($viewableMaufacturer->getManufacturerProducts()));
    }

    /**
     * @param int $manufacturerId
     *
     * @return EditableManufacturer
     */
    private function getEditableManufacturer(int $manufacturerId): EditableManufacturer
    {
        return $this->getQueryBus()->handle(new GetManufacturerForEditing($manufacturerId));
    }
}
