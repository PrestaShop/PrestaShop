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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use AddressFormat;
use Cache;
use Country;
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

        //todo: uncomment and use it when address format PR is merged
//        $addressFormat = 'firstname lastname
//            company
//            vat_number
//            address1
//            address2
//            postcode city
//            Country:name
//            phone';
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
            'country[address_format]' => '', //todo: add when address format logic is added
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

        // this is the default format that is taken when opening country form, if you try to add spaces the test will fail.
        $addressFormat = 'firstname lastname
company
address1 address2
city, State:name postcode
Country:name
phone';
        //todo: change addressFormat and test if it was correctly changed when address format PR is merged. Now it only takes default value
        $isoCode = 'BB';
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
            'country[address_format]' => '', //todo: add address format when logic is added
            'country[is_enabled]' => 1,
            'country[contains_states]' => 0,
            'country[need_identification_number]' => 0,
            'country[display_tax_label]' => 1,
        ];
        $this->editEntityFromPage(['countryId' => $countryId], $formData);

        //todo: check this part with cache more deeply when address format logic is done.
        // need to clear cache because prestashop caches address format and without clear it sees the one before update
//        Cache::clear();

        // Then check that it was correctly updated
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
            ['country[iso_code]' => 'BB'],
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
