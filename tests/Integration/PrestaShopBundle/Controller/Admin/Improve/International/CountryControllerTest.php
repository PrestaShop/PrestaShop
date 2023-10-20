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
        // First create country
        $formData = [
            'country[name][1]' => 'createName',
            'country[iso_code]' => 'TE',
            'country[call_prefix]' => 123,
            'country[default_currency]' => 1,
            'country[zone]' => 1,
            'country[need_zip_code]' => '1',
            'country[zip_code_format]' => '12NNNLL',
            'country[address_format]' => 'todo', //todo: add when address format will be implemented
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

        return $countryId;
    }

    /**
     * @depends testCreate
     *
     * @param int $countryId
     */
    public function testEdit(int $countryId): void
    {
        $this->markTestSkipped('Not implemented');
        // TODO: Implement when edit action is created
    }

    public function testFilters(): int
    {
        //todo: when edit form is finished we can use it for filter test. Example AddressControllerTest.php
        $countryId = 1;
        $gridFilters = [
            ['country[id_country]' => $countryId],
            ['country[name]' => 'Germany'],
            ['country[iso_code]' => 'DE'],
            ['country[call_prefix]' => 49],
            ['country[zone_name]' => 'Europe'],
            ['country[active]' => 0],
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
        // TODO: Implement generateEditUrl() method.
        return 'Not implemented yet';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditSubmitButtonSelector(): string
    {
        // TODO: Implement getEditSubmitButtonSelector() method.
        return 'Not implemented yet';
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
