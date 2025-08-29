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
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ManufacturerFeatureContext extends AbstractDomainFeatureContext
{
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
    public function createManufacturer(string $reference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);
        $command = new AddManufacturerCommand(
            $data['name'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
            $data['short_description'] ?? [],
            $data['description'] ?? [],
            $data['meta_title'] ?? [],
            $data['meta_description'] ?? [],
            [$this->getDefaultShopId()]
        );

        /** @var ManufacturerId $manufacturerId */
        $manufacturerId = $this->getCommandBus()->handle($command);
        $this->getSharedStorage()->set($reference, $manufacturerId->getValue());
    }

    /**
     * @When I edit manufacturer :manufacturerReference with following properties:
     */
    public function editManufacturerWithDefaultLang(string $manufacturerReference, TableNode $node): void
    {
        $manufacturerId = $this->referenceToId($manufacturerReference);
        $data = $this->localizeByRows($node);
        $command = new EditManufacturerCommand($manufacturerId);

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
        }
        if (isset($data['short_description'])) {
            $command->setLocalizedShortDescriptions($data['short_description']);
        }
        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }
        if (isset($data['meta_title'])) {
            $command->setLocalizedMetaTitles($data['meta_title']);
        }
        if (isset($data['meta_description'])) {
            $command->setLocalizedMetaDescriptions($data['meta_description']);
        }
        if (isset($data['logo image'])) {
            $this->pretendImageUploaded(_PS_MANU_IMG_DIR_, $data['logo image'], $manufacturerId);
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete manufacturer :manufacturerReference
     */
    public function deleteManufacturer(string $manufacturerReference): void
    {
        $this->getCommandBus()->handle(new DeleteManufacturerCommand($this->referenceToId($manufacturerReference)));
    }

    /**
     * @When I delete manufacturers: :manufacturerReferences using bulk action
     */
    public function bulkDeleteManufacturers(string $manufacturerReferences): void
    {
        $this->getCommandBus()->handle(new BulkDeleteManufacturerCommand($this->referencesToIds($manufacturerReferences)));
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
     * @Then manufacturer :manufacturerReference should have the following properties:
     *
     * @param string $manufacturerReference
     * @param TableNode $tableNode
     */
    public function assertManufacturerData(string $manufacturerReference, TableNode $tableNode): void
    {
        $manufacturerForEditing = $this->getEditableManufacturer($this->referenceToId($manufacturerReference));
        $data = $this->localizeByRows($tableNode);

        if (isset($data['name'])) {
            Assert::assertEquals($data['name'], $manufacturerForEditing->getName());
        }
        if (isset($data['enabled'])) {
            Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']), $manufacturerForEditing->isEnabled());
        }
        if (isset($data['short_description'])) {
            Assert::assertEquals($data['short_description'], $manufacturerForEditing->getLocalizedShortDescriptions());
        }
        if (isset($data['description'])) {
            Assert::assertEquals($data['description'], $manufacturerForEditing->getLocalizedDescriptions());
        }
        if (isset($data['meta_title'])) {
            Assert::assertEquals($data['meta_title'], $manufacturerForEditing->getLocalizedMetaTitles());
        }
        if (isset($data['meta_description'])) {
            Assert::assertEquals($data['meta_description'], $manufacturerForEditing->getLocalizedMetaDescriptions());
        }
    }

    /**
     * @When /^I (enable|disable)? manufacturer "(.*)"$/
     */
    public function toggleStatus(string $action, string $reference): void
    {
        $expectedStatus = 'enable' === $action;
        $this->getCommandBus()->handle(new ToggleManufacturerStatusCommand($this->referenceToId($reference), $expectedStatus));
    }

    /**
     * @When /^I (enable|disable) multiple manufacturers: "(.+)" using bulk action$/
     */
    public function bulkToggleStatus(string $action, string $manufacturerReferences): void
    {
        $expectedStatus = 'enable' === $action;
        $this->getQueryBus()->handle(new BulkToggleManufacturerStatusCommand(
            $this->referencesToIds($manufacturerReferences),
            $expectedStatus
        ));
    }

    /**
     * @Given /^manufacturers: "(.+)" should be (enabled|disabled)$/
     */
    public function assertMultipleManufacturersStatus(string $manufacturerReferences, string $expectedStatus): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($manufacturerReferences) as $manufacturerReference) {
            $this->assertStatus($manufacturerReference, $expectedStatus);
        }
    }

    /**
     * @Given /^manufacturer "(.*)" is (enabled|disabled)?$/
     *
     * @Then /^manufacturer "(.*)" should be (enabled|disabled)?$/
     */
    public function assertStatus(string $manufacturerReference, string $expectedStatus): void
    {
        $manufacturer = $this->getEditableManufacturer($this->referenceToId($manufacturerReference));
        $isEnabled = 'enabled' === $expectedStatus;
        Assert::assertEquals(
            $manufacturer->isEnabled(),
            $isEnabled,
            sprintf('Manufacturer "%s" is %s, but it was expected to be %s', $manufacturerReference, $manufacturer->isEnabled() ? 'enabled' : 'disabled', $expectedStatus)
        );
    }

    /**
     * @Then manufacturer :manufacturerReference should be deleted
     */
    public function assertManufacturerIsDeleted(string $manufacturerReference): void
    {
        try {
            $this->getEditableManufacturer($this->getSharedStorage()->get($manufacturerReference));

            throw new NoExceptionAlthoughExpectedException(sprintf('Manufacturer %s exists, but it was expected to be deleted', $manufacturerReference));
        } catch (ManufacturerNotFoundException $e) {
            $this->getSharedStorage()->clear($manufacturerReference);
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
        $this->getCommandBus()->handle(new DeleteManufacturerLogoImageCommand($this->referenceToId($manufacturerReference)));
    }

    /**
     * @Given the manufacturer :manufacturerReference has a logo image
     *
     * @param string $manufacturerReference
     */
    public function assertManufacturerHasLogoImage(string $manufacturerReference): void
    {
        $editableManufacturer = $this->getEditableManufacturer($this->referenceToId($manufacturerReference));
        Assert::assertNotNull($editableManufacturer->getLogoImage());
    }

    /**
     * @Then the manufacturer :manufacturerReference does not have a logo image
     *
     * @param string $manufacturerReference
     */
    public function assertManufacturerHasNotLogoImage(string $manufacturerReference): void
    {
        $editableManufacturer = $this->getEditableManufacturer($this->referenceToId($manufacturerReference));
        Assert::assertNull($editableManufacturer->getLogoImage());
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
        int $countOfProducts): void
    {
        /** @var ViewableManufacturer $viewableManufacturer */
        $viewableManufacturer = $this->getQueryBus()->handle(new GetManufacturerForViewing(
            $this->referenceToId($manufacturerReference),
            (int) $this->getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_LANG_DEFAULT')
        ));

        Assert::assertSame($countOfAddresses, count($viewableManufacturer->getManufacturerAddresses()));
        Assert::assertSame($countOfProducts, count($viewableManufacturer->getManufacturerProducts()));
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
