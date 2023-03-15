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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Integration\Utility\ContextMockerTrait;

class StoreControllerTest extends FormGridControllerTestCase
{
    use ContextMockerTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;

    public function testIndex(): int
    {
        $stores = $this->getEntitiesFromGrid();
        $this->assertNotEmpty($stores);

        return $stores->count();
    }

    /**
     * Testing filters by using already existing entities from fixtures
     */
    public function testFilters(): void
    {
        $gridFilters = [
            [
                'store[name]' => 'Dade',
            ],
            [
                'store[id_store]' => 1,
            ],
            [
                'store[address]' => '3030',
            ],
            [
                'store[city]' => 'miami',
            ],
            [
                'store[postcode]' => '33135',
            ],
            [
                'store[state]' => 'florida',
            ],
            [
                'store[country]' => 'United',
            ],
            [
                'store[active]' => 1,
            ],
        ];

        foreach ($gridFilters as $testFilter) {
            $stores = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertGreaterThanOrEqual(1, count($stores), sprintf(
                'Expected at least one store with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($stores, 1);
        }

        // additional assertion to make sure it doesn't find random entities all the time
        $this->assertEmpty(
            $this->getFilteredEntitiesFromGrid(
                [
                    // this id doesn't exist, so no entities should be found
                    'store[id_store]' => 5555,
                ]
            )
        );
    }

    protected function generateCreateUrl(): string
    {
        //@todo: fix when create action is implemented
        return '';
    }

    protected function getCreateSubmitButtonSelector(): string
    {
        //@todo: fix when create action is implemented
        return '';
    }

    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        //@todo: fix when form actions are implemented. Now random from_handler is used to avoid return type error
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.product_form_handler');

        return $checker;
    }

    protected function generateEditUrl(array $routeParams): string
    {
        //@todo: fix when edit action is implemented
        return '';
    }

    protected function getEditSubmitButtonSelector(): string
    {
        //@todo: fix when edit action is implemented
        return '';
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'store[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'store[offset]' => 0,
                'store[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_stores_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#store_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_store')->text()),
            []
        );
    }
}
