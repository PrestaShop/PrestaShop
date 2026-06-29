<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;

class CountryControllerTest extends FormGridControllerTestCase
{
    public function testIndex(): int
    {
        $countries = $this->getEntitiesFromGrid();
        $this->assertNotEmpty($countries);

        return $countries->count();
    }

    /**
     * @depends testIndex
     *
     * @param int $initialEntityCount
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        // Address format must contain every required field (firstname, lastname,
        // address1, city, Country:name) — both the Symfony form constraint and the
        // CQRS handler reject formats missing any of them.
        $addressFormat = "firstname lastname\naddress1\ncity\nCountry:name";
        $isoCode = 'AA';
        $zipCodeFormat = '1NL';

        // First create country
        $formData = [
            'country[name][1]' => 'createName',
            'country[iso_code]' => $isoCode,
            'country[call_prefix]' => 123,
            'country[default_currency]' => 1,
            'country[zone]' => 1,
            'country[need_zip_code]' => '1',
            'country[zip_code_format]' => $zipCodeFormat,
            'country[address_format]' => $addressFormat,
            'country[is_enabled]' => 1,
            'country[contains_states]' => 0,
            'country[need_identification_number]' => 0,
            'country[display_tax_label]' => 1,
        ];
        $countryId = $this->createEntityFromPage($formData);

        // Check that there is one more country in the list
        $newCountry = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $newCountry);
        $this->assertCollectionContainsEntity($newCountry, $countryId);

        $this->assertFormValuesFromPage(
            ['countryId' => $countryId],
            $formData
        );

        return $countryId;
    }

    /**
     * @depends testCreate
     *
     * @param int $countryId
     */
    public function testEdit(int $countryId): int
    {
        $this->client->disableReboot();

        // Use a different format than testCreate so the assertion proves the edit
        // round-trips. All required fields are present.
        // ISO code must be a user-assigned code absent from the country fixtures
        // (e.g. ZZ), otherwise the duplicate ISO code validation rejects the edit.
        $addressFormat = "firstname lastname\ncompany\naddress1\npostcode city\nCountry:name";
        $isoCode = 'ZZ';
        $zipCodeFormat = '2NL';

        // First update the country with new data
        $formData = [
            'country[name][1]' => 'editName',
            'country[iso_code]' => $isoCode,
            'country[call_prefix]' => 1234,
            'country[default_currency]' => 1,
            'country[zone]' => 1,
            'country[need_zip_code]' => '1',
            'country[zip_code_format]' => $zipCodeFormat,
            'country[address_format]' => $addressFormat,
            'country[is_enabled]' => 1,
            'country[contains_states]' => 0,
            'country[need_identification_number]' => 0,
            'country[display_tax_label]' => 1,
        ];
        $this->editEntityFromPage(['countryId' => $countryId], $formData);

        // Then check that it was correctly updated.
        // Note: the AddressFormat::getFormatDB static cache is invalidated by the
        // Add/Edit handlers via Cache::clean, so the read here returns the new value.
        $this->assertFormValuesFromPage(
            ['countryId' => $countryId],
            $formData
        );

        return $countryId;
    }

    /**
     * @depends testEdit
     *
     * @param int $countryId
     *
     * @return int
     */
    public function testFilters(int $countryId): int
    {
        $gridFilters = [
            ['country[id_country]' => $countryId],
            ['country[name]' => 'editName'],
            ['country[iso_code]' => 'ZZ'],
            ['country[call_prefix]' => 1234],
            ['country[zone_name]' => 'Europe'],
            ['country[active]' => 1],
        ];

        foreach ($gridFilters as $testFilter) {
            $countries = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertGreaterThanOrEqual(1, count($countries), sprintf(
                'Expected at least one address with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($countries, $countryId);
        }

        return $countryId;
    }

    /**
     * @depends testFilters
     *
     * @param int $countryId
     */
    public function testDelete(int $countryId): void
    {
        $this->client->disableReboot();

        $countries = $this->getEntitiesFromGrid();
        $initialEntityCount = $countries->count();

        $this->deleteEntityFromPage('admin_countries_delete', ['countryId' => $countryId]);

        $newCountries = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount - 1, $newCountries);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateCreateUrl(): string
    {
        // TODO: Implement generateCreateUrl() method.
        return $this->router->generate('admin_countries_create');
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    /**
     * {@inheritDoc}
     */
    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        // TODO: Implement getFormHandlerChecker() method.
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.country_form_handler');

        return $checker;
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_countries_edit', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterSearchButtonSelector(): string
    {
        return 'country[actions][search]';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'country[offset]' => 0,
                'country[limit]' => 1000,
            ];
        }

        return $this->router->generate('admin_countries_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#country_grid_table';
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_country')->text()),
            [
                'country' => trim($tr->filter('.column-name')->text()),
                'isoCode' => trim($tr->filter('.column-iso_code')->text()),
                'callPrefix' => trim($tr->filter('.column-call_prefix')->text()),
                'zone' => trim($tr->filter('.column-zone_name')->text()),
                'enabled' => trim($tr->filter('.column-active')->text()),
            ]
        );
    }
}
