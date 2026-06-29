<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkToggleCountriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkUpdateCountryZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\BulkCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DuplicateCountryIsoCodeException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\InvalidAddressFormatException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CountryFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer representing country id which should never exist in test database
     */
    private const NON_EXISTING_COUNTRY_ID = 74000211;

    /**
     * @Given country :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingCountryReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that country "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_COUNTRY_ID);
    }

    /**
     * @Given country :reference has invalid id
     */
    public function setInvalidCountryReference(string $reference): void
    {
        $this->getSharedStorage()->set($reference, 0);
    }

    /**
     * @Then I should get error that country was not found
     */
    public function assertCountryNotFound(): void
    {
        $this->assertLastErrorIs(CountryNotFoundException::class);
    }

    /**
     * @Then I should get error that country id is invalid
     */
    public function assertCountryIdIsInvalid(): void
    {
        $this->assertLastErrorIs(CountryConstraintException::class, CountryConstraintException::INVALID_ID);
    }

    /**
     * @Then I should get error that zone was not found
     */
    public function assertZoneNotFound(): void
    {
        $this->assertLastErrorIs(ZoneNotFoundException::class);
    }

    /**
     * @Then I should get a bulk country exception containing :expectedErrorsCount errors
     */
    public function assertBulkCountryExceptionContainingErrors(int $expectedErrorsCount): void
    {
        /** @var BulkCountryException $lastError */
        $lastError = $this->assertLastErrorIs(BulkCountryException::class);
        Assert::assertCount($expectedErrorsCount, $lastError->getExceptions());
    }

    /**
     * @When I add new country :countryReference with following properties:
     *
     * @param string $countryReference
     * @param TableNode $table
     */
    public function createCountry(string $countryReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        try {
            $countryId = $this->getCommandBus()->handle(new AddCountryCommand(
                $data['name'],
                (string) $data['iso_code'],
                (int) $data['call_prefix'],
                (int) $data['default_currency'],
                (int) $data['zone'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['need_zip_code']),
                $data['zip_code_format'],
                $this->unescapeFormat((string) $data['address_format']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['contains_states']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['need_identification_number']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['display_tax_label']),
                [$this->getDefaultShopId()]
            ));
            $this->getSharedStorage()->set($countryReference, $countryId->getValue());
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit country :countryReference with following properties:
     *
     * @param string $countryReference
     * @param TableNode $table
     */
    public function editCountry(string $countryReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        $command = new EditCountryCommand(SharedStorage::getStorage()->get($countryReference));

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }

        if (isset($data['iso_code'])) {
            $command->setIsoCode($data['iso_code']);
        }

        if (isset($data['call_prefix'])) {
            $command->setCallPrefix((int) $data['call_prefix']);
        }

        if (isset($data['default_currency'])) {
            $command->setDefaultCurrency((int) $data['default_currency']);
        }

        if (isset($data['zone'])) {
            $command->setZoneId((int) $data['zone']);
        }

        if (isset($data['need_zip_code'])) {
            $command->setNeedZipCode(PrimitiveUtils::castStringBooleanIntoBoolean($data['need_zip_code']));
        }

        if (isset($data['zip_code_format'])) {
            $command->setZipCodeFormat($data['zip_code_format']);
        }

        if (isset($data['address_format'])) {
            $command->setAddressFormat($this->unescapeFormat($data['address_format']));
        }

        if (isset($data['is_enabled'])) {
            $command->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['is_enabled']));
        }

        if (isset($data['contains_states'])) {
            $command->setContainsStates(PrimitiveUtils::castStringBooleanIntoBoolean($data['contains_states']));
        }

        if (isset($data['need_identification_number'])) {
            $command->setNeedIdNumber(PrimitiveUtils::castStringBooleanIntoBoolean($data['need_identification_number']));
        }

        if (isset($data['display_tax_label'])) {
            $command->setDisplayTaxLabel(PrimitiveUtils::castStringBooleanIntoBoolean($data['display_tax_label']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an :exceptionShortName error
     */
    public function assertCountryDomainError(string $exceptionShortName): void
    {
        $map = [
            'InvalidAddressFormat' => InvalidAddressFormatException::class,
            'DuplicateCountryIsoCode' => DuplicateCountryIsoCodeException::class,
        ];

        if (!isset($map[$exceptionShortName])) {
            throw new RuntimeException(sprintf('Unknown country error short name "%s"', $exceptionShortName));
        }

        $this->assertLastErrorIs($map[$exceptionShortName]);
    }

    /**
     * Behat tables strip backslash escapes in cell values, so the feature file uses
     * the literal `\n` two-character sequence instead of a newline. Convert back here.
     */
    private function unescapeFormat(string $format): string
    {
        return str_replace('\\n', "\n", $format);
    }

    /**
     * @When I toggle country status :countryReference
     */
    public function toggleCountryStatus(string $countryReference): void
    {
        try {
            $this->getCommandBus()->handle(new ToggleCountryStatusCommand($this->referenceToId($countryReference)));
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk enable countries :countryReferences
     */
    public function bulkEnableCountries(string $countryReferences): void
    {
        $this->bulkToggleCountriesStatus($countryReferences, true);
    }

    /**
     * @When I bulk disable countries :countryReferences
     */
    public function bulkDisableCountries(string $countryReferences): void
    {
        $this->bulkToggleCountriesStatus($countryReferences, false);
    }

    /**
     * @When I bulk enable an empty list of countries
     */
    public function bulkEnableEmptyCountriesList(): void
    {
        try {
            $this->getCommandBus()->handle(new BulkToggleCountriesStatusCommand(true, []));
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk update an empty list of countries to zone :zoneId
     */
    public function bulkUpdateEmptyCountriesListZone(int $zoneId): void
    {
        try {
            $this->getCommandBus()->handle(new BulkUpdateCountryZoneCommand([], $zoneId));
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then no exception should have been thrown
     */
    public function assertNoExceptionWasThrown(): void
    {
        $this->assertLastErrorIsNull();
    }

    /**
     * @Then I should get error that country list is empty
     */
    public function assertCountryListIsEmpty(): void
    {
        $this->assertLastErrorIs(CountryException::class);
    }

    /**
     * @When I bulk update countries :countryReferences to zone :zoneId
     */
    public function bulkUpdateCountriesZone(string $countryReferences, int $zoneId): void
    {
        try {
            $this->getCommandBus()->handle(new BulkUpdateCountryZoneCommand(
                $this->getCountryIdsFromReferences($countryReferences),
                $zoneId
            ));
        } catch (CountryException|ZoneNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then country :countryReference should be enabled
     */
    public function assertCountryIsEnabled(string $countryReference): void
    {
        $country = $this->getQueryBus()->handle(new GetCountryForEditing($this->referenceToId($countryReference)));
        Assert::assertTrue($country->isEnabled());
    }

    /**
     * @Then country :countryReference should be disabled
     */
    public function assertCountryIsDisabled(string $countryReference): void
    {
        $country = $this->getQueryBus()->handle(new GetCountryForEditing($this->referenceToId($countryReference)));
        Assert::assertFalse($country->isEnabled());
    }

    /**
     * @Then country :countryReference should be assigned to zone :zoneId
     */
    public function assertCountryZone(string $countryReference, int $zoneId): void
    {
        $country = $this->getQueryBus()->handle(new GetCountryForEditing($this->referenceToId($countryReference)));
        Assert::assertSame($zoneId, $country->getZone());
    }

    /**
     * @Then /^the country "(.+)" should have the following properties:$/
     */
    public function assertCountryProperties(string $countryReference, TableNode $table)
    {
        $countryId = SharedStorage::getStorage()->get($countryReference);
        $expectedData = $this->localizeByRows($table);
        $expectedData = $this->formatCountryDataIfNeeded($expectedData);

        $queryBus = $this->getQueryBus();
        /** @var CountryForEditing $result */
        $result = $queryBus->handle(new GetCountryForEditing($countryId));

        Assert::assertEquals($expectedData['name'], $result->getLocalizedNames(), 'unexpected name');
        Assert::assertEquals($expectedData['iso_code'], $result->getIsoCode(), 'unexpected iso_code');
        Assert::assertEquals($expectedData['call_prefix'], $result->getCallPrefix(), 'unexpected call_prefix');
        Assert::assertEquals($expectedData['default_currency'], $result->getDefaultCurrency(), 'unexpected default_currency');
        Assert::assertEquals($expectedData['zone'], $result->getZone(), 'unexpected zone');
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['need_zip_code']), $result->isNeedZipCode(), 'unexpected need_zip_code');
        Assert::assertEquals($expectedData['zip_code_format'], $result->getZipCodeFormat()->getValue(), 'unexpected zip_code_format');
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['is_enabled']), $result->isEnabled(), 'unexpected enabled');
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['contains_states']), $result->isContainsStates(), 'unexpected contains_states');
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['need_identification_number']), $result->isNeedIdNumber(), 'unexpected need_identification_number');
        Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($expectedData['display_tax_label']), $result->isDisplayTaxLabel(), 'unexpected display_tax_label');
        Assert::assertEquals([$expectedData['shop_association']], $result->getShopAssociation(), 'unexpected shop_association');
        if (array_key_exists('address_format', $expectedData)) {
            Assert::assertEquals(
                $this->unescapeFormat((string) $expectedData['address_format']),
                $result->getAddressFormat(),
                'unexpected address_format'
            );
        }
    }

    private function formatCountryDataIfNeeded(array $data)
    {
        if (array_key_exists('callPrefix', $data)) {
            $data['callPrefix'] = (int) $data['callPrefix'];
        }
        if (array_key_exists('defaultCurrency', $data)) {
            $data['defaultCurrency'] = (int) $data['defaultCurrency'];
        }
        if (array_key_exists('zone', $data)) {
            $data['zone'] = (int) $data['zone'];
        }
        if (array_key_exists('needZipCode', $data)) {
            $data['needZipCode'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['needZipCode']);
        }
        if (array_key_exists('enabled', $data)) {
            $data['enabled'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        }
        if (array_key_exists('containsStates', $data)) {
            $data['containsStates'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['containsStates']);
        }
        if (array_key_exists('needIdNumber', $data)) {
            $data['needIdNumber'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['needIdNumber']);
        }
        if (array_key_exists('displayTaxLabel', $data)) {
            $data['displayTaxLabel'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['displayTaxLabel']);
        }

        return $data;
    }

    /**
     * @When I delete country :countryReference
     *
     * @param string $countryReference
     */
    public function deleteCountry(string $countryReference): void
    {
        $countryId = SharedStorage::getStorage()->get($countryReference);

        $this->getCommandBus()->handle(new DeleteCountryCommand((int) $countryId));
    }

    /**
     * @Then country :countryReference should be deleted
     *
     * @param string $countryReference
     */
    public function assertCountryIsDeleted(string $countryReference): void
    {
        $countryId = SharedStorage::getStorage()->get($countryReference);

        try {
            $this->getQueryBus()->handle(new GetCountryForEditing($countryId));

            throw new NoExceptionAlthoughExpectedException(sprintf('Country %s exists, but it was expected to be deleted', $countryReference));
        } catch (CountryNotFoundException $e) {
            SharedStorage::getStorage()->clear($countryReference);
        }
    }

    private function bulkToggleCountriesStatus(string $countryReferences, bool $expectedStatus): void
    {
        try {
            $this->getCommandBus()->handle(new BulkToggleCountriesStatusCommand(
                $expectedStatus,
                $this->getCountryIdsFromReferences($countryReferences)
            ));
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @return int[]
     */
    private function getCountryIdsFromReferences(string $countryReferences): array
    {
        $references = array_map('trim', explode(',', $countryReferences));

        return array_map(function (string $reference): int {
            return $this->referenceToId($reference);
        }, $references);
    }
}
