<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ManufacturerNameByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CountryByIdChoiceProvider;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class AddressFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_ADDRESS_ID = 1;

    /**
     * @When I add new brand address :manufacturerAddressReference with following details:
     *
     * @param string $manufacturerAddressReference
     * @param TableNode $table
     */
    public function addNewBrandAddressWithFollowingDetails(string $manufacturerAddressReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $editableManufacturerAddress = $this->mapEditableManufacturerAddress(
            $testCaseData,
            self::DEFAULT_ADDRESS_ID
        );
        /** @var AddressId $addressIdObject */
        $addressIdObject = $this->getCommandBus()->handle(new AddManufacturerAddressCommand(
            $editableManufacturerAddress->getLastName(),
            $editableManufacturerAddress->getFirstName(),
            $editableManufacturerAddress->getAddress(),
            $editableManufacturerAddress->getCountryId(),
            $editableManufacturerAddress->getCity(),
            $editableManufacturerAddress->getManufacturerId())
        );
        SharedStorage::getStorage()->set($manufacturerAddressReference, $addressIdObject->getValue());
    }

    /**
     * @Then brand address :manufacturerAddressReference should have following details:
     *
     * @param string $manufacturerAddressReference
     * @param TableNode $table
     */
    public function brandAddressShouldHaveFollowingDetails(string $manufacturerAddressReference, TableNode $table)
    {
        $manufacturerAddressId = SharedStorage::getStorage()->get($manufacturerAddressReference);
        /** @var EditableManufacturerAddress $editableManufacturerAddress */
        $editableManufacturerAddress = $this->getQueryBus()->handle(
            new GetManufacturerAddressForEditing($manufacturerAddressId)
        );
        $testCaseData = $table->getRowsHash();
        $expectedEditableManufacturerAddress = $this->mapEditableManufacturerAddress(
            $testCaseData,
            $manufacturerAddressId
        );
        PHPUnit_Framework_Assert::assertEquals($expectedEditableManufacturerAddress, $editableManufacturerAddress);
    }

    /**
     * @param array $testCaseData
     * @param int $addressId
     *
     * @return EditableManufacturerAddress
     */
    private function mapEditableManufacturerAddress(array $testCaseData, int $addressId): EditableManufacturerAddress
    {
        /** @var ManufacturerNameByIdChoiceProvider $manufacturerProvider */
        $manufacturerProvider = $this->getContainer()->get(
            'prestashop.adapter.form.choice_provider.manufacturer_name_by_id'
        );
        $manufacturerId = $manufacturerProvider->getChoices()[$testCaseData['Brand']];

        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = $countryChoiceProvider->getChoices()[$testCaseData['Country']];

        return new EditableManufacturerAddress(
            new AddressId($addressId),
            $testCaseData['Last name'],
            $testCaseData['First name'],
            $testCaseData['Address'],
            $testCaseData['City'],
            $manufacturerId,
            $countryId
        );
    }
}
