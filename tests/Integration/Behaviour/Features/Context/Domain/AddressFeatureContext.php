<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CountryStateByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ManufacturerNameByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CountryByIdChoiceProvider;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class AddressFeatureContext extends AbstractDomainFeatureContext
{
    private const DEFAULT_ADDRESS_ID = 1;
    private const DEFAULT_COUNTRY_STATE_ID = 0;

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
     * @When I add new address to customer :customerReference with following details:
     *
     * @param string $customerReference
     * @param TableNode $table
     */
    public function addNewAddressToCustomerWithFollowingDetails(string $customerReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $customerId = SharedStorage::getStorage()->get($customerReference);
        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = (int) $countryChoiceProvider->getChoices()[$testCaseData['Country']];
        /** @var CountryStateByIdChoiceProvider $countryStateChoiceProvider */
        $countryStateId = self::DEFAULT_COUNTRY_STATE_ID;
        if (isset($testCaseData['State'])) {
            $countryStateChoiceProvider = $this->getContainer()->get('prestashop.adapter.form.choice_provider.country_state_by_id');
            $countryStateId = $countryStateChoiceProvider->getChoices(['id_country' => $countryId])[$testCaseData['State']];
        }

        /** @var AddressId $addressIdObject */
        $addressIdObject = $this->getCommandBus()->handle(new AddCustomerAddressCommand(
            $customerId,
            $testCaseData['Address alias'],
            $testCaseData['First name'],
            $testCaseData['Last name'],
            $testCaseData['Address'],
            $testCaseData['City'],
            $countryId,
            $testCaseData['Postal code'],
            null,
            null,
            null,
            null,
            $countryStateId
        ));
        SharedStorage::getStorage()->set($testCaseData['Address alias'], $addressIdObject->getValue());
    }

    /**
     * @Then customer :customerReference should have address :addressReference with following details:
     *
     * @param string $customerReference
     * @param string $addressReference
     * @param TableNode $table
     */
    public function customerShouldHaveAddressWithFollowingDetails(
        string $customerReference,
        string $addressReference,
        TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $customerId = SharedStorage::getStorage()->get($customerReference);
        $customerAddressId = SharedStorage::getStorage()->get($addressReference);

        /** @var EditableCustomerAddress $customerAddress */
        $customerAddress = $this->getQueryBus()->handle(new GetCustomerAddressForEditing($customerAddressId));

        PHPUnit_Framework_Assert::assertSame($customerId, $customerAddress->getCustomerId()->getValue());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['Address alias'], $customerAddress->getAddressAlias());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['First name'], $customerAddress->getFirstName());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['Last name'], $customerAddress->getLastName());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['Address'], $customerAddress->getAddress());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['City'], $customerAddress->getCity());

        /** @var CountryByIdChoiceProvider $countryChoiceProvider */
        $countryChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.country_by_id');
        $countryId = (int) $countryChoiceProvider->getChoices()[$testCaseData['Country']];
        PHPUnit_Framework_Assert::assertSame((int) $countryId, $customerAddress->getCountryId()->getValue());

        $countryStateChoiceProvider = $this->getContainer()->get('prestashop.adapter.form.choice_provider.country_state_by_id');
        $countryStateId = $countryStateChoiceProvider->getChoices(['id_country' => $countryId])[$testCaseData['State']];
        PHPUnit_Framework_Assert::assertSame((int) $countryStateId, $customerAddress->getStateId()->getValue());
        PHPUnit_Framework_Assert::assertEquals($testCaseData['Postal code'], $customerAddress->getPostCode());
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

    /**
     * @When I delete address :addressReference
     *
     * @param string $addressReference
     */
    public function deleteAddress(string $addressReference)
    {
        $addressId = SharedStorage::getStorage()->get($addressReference);
        $this->getCommandBus()->handle(new DeleteAddressCommand($addressId));
    }

    /**
     * @Then brand address :addressReference does not exist
     *
     * @param string $addressReference
     */
    public function brandAddressDoesNotExist(string $addressReference)
    {
        $addressId = SharedStorage::getStorage()->get($addressReference);
        try {
            /* @var EditableManufacturerAddress $editableManufacturerAddress */
            $this->getQueryBus()->handle(new GetManufacturerAddressForEditing($addressId));
            throw new \RuntimeException(sprintf(
                'Manufacturer address "%s" should not be found',
                $addressReference
            ));
        } catch (AddressNotFoundException $exception) {
        }
    }
}
