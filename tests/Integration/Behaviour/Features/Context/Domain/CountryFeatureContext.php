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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Country;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

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
                (bool) $data['need_zip_code'],
                $data['zip_code_format'],
                (string) $data['address_format'],
                (bool) $data['is_enabled'],
                (bool) $data['contains_states'],
                (bool) $data['need_identification_number'],
                (bool) $data['display_tax_label'],
                [$this->getDefaultShopId()]
            ));
            $this->getSharedStorage()->set($countryReference, $countryId->getValue());
        } catch (CountryException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then country :reference name should be :name
     *
     * @param string $countryReference
     * @param string $name
     */
    public function assertCountryName(string $countryReference, string $name): void
    {
        $country = new Country(SharedStorage::getStorage()->get($countryReference));

        if (!in_array($name, $country->name)) {
            throw new RuntimeException(sprintf('Country "%s" has "%s" name, but "%s" was expected.', $countryReference, $country->name, $name));
        }
    }

    /**
     * @Given /^country "(.*)" is (enabled|disabled)?$/
     * @Then /^country "(.*)" should be (enabled|disabled)?$/
     *
     * @param string $countryReference
     * @param string $expectedStatus
     */
    public function assertStatus(string $countryReference, string $expectedStatus): void
    {
        $country = new Country(SharedStorage::getStorage()->get($countryReference));

        $isEnabled = 'enabled' === $expectedStatus;
        $actualStatus = (bool) $country->active;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(sprintf('Country "%s" is %s, but it was expected to be %s', $countryReference, $actualStatus ? 'enabled' : 'disabled', $expectedStatus));
        }
    }
}
