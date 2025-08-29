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
use Country;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;
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
     * @Then I should get error that country was not found
     */
    public function assertCountryNotFound(): void
    {
        $this->assertLastErrorIs(CountryNotFoundException::class);
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
                (string) $data['address_format'],
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
            $command->setAddressFormat($data['address_format']);
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

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then /^the country "(.+)" should have the following properties:$/
     */
    public function assertQueryCustomerProperties($countryReference, TableNode $table)
    {
        $countryId = SharedStorage::getStorage()->get($countryReference);
        $expectedData = $this->localizeByRows($table);
        $expectedData = $this->formatCountryDataIfNeeded($expectedData);

        $queryBus = $this->getQueryBus();
        /** @var CountryForEditing $result */
        $result = $queryBus->handle(new GetCountryForEditing($countryId));

        Assert::assertEquals($expectedData['localizedNames'], $result->getLocalizedNames());
        Assert::assertEquals($expectedData['isoCode'], $result->getIsoCode());
        Assert::assertEquals($expectedData['callPrefix'], $result->getCallPrefix());
        Assert::assertEquals($expectedData['defaultCurrency'], $result->getDefaultCurrency());
        Assert::assertEquals($expectedData['zone'], $result->getZone());
        Assert::assertEquals($expectedData['needZipCode'], $result->isNeedZipCode());
        Assert::assertEquals($expectedData['zipCodeFormat'], $result->getZipCodeFormat()->getValue());
        Assert::assertEquals($expectedData['enabled'], $result->isEnabled());
        Assert::assertEquals($expectedData['containsStates'], $result->isContainsStates());
        Assert::assertEquals($expectedData['needIdNumber'], $result->isNeedIdNumber());
        Assert::assertEquals($expectedData['displayTaxLabel'], $result->isDisplayTaxLabel());
        Assert::assertEquals([$expectedData['shopAssociation']], $result->getShopAssociation());
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
}
