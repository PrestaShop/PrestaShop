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

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\StoreResetter;

//@todo: when form actions are ready, this class should extend FormGridControllerTestCase and add additional tests for forms
class StoreControllerTest extends GridControllerTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var Router
     */
    protected $router;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
        StoreResetter::resetStores();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        StoreResetter::resetStores();
    }

    public function testIndex(): void
    {
        $stores = $this->getEntitiesFromGrid();
        $this->assertNotEmpty($stores);
    }

    /**
     * @depends testIndex
     *
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

        $this->resetGridFilters();
    }

    /**
     * @depends testFilters
     */
    public function testToggleStatus(): void
    {
        $enabledStores = $this->getFilteredEntitiesFromGrid(['store[active]' => 1]);
        $disabledStoresCount = $this->getFilteredEntitiesFromGrid(['store[active]' => 0])->count();

        Assert::assertGreaterThan(0, $enabledStores->count());
        Assert::assertEmpty($disabledStoresCount);

        /** @var TestEntityDTO $firstFoundStore */
        $firstFoundStore = $enabledStores->first();

        $this->toggleStatus('admin_stores_toggle_status', ['storeId' => $firstFoundStore->getId()]);

        Assert::assertSame(
            $this->getFilteredEntitiesFromGrid(['store[active]' => 1])->count(),
            $enabledStores->count() - 1
        );

        Assert::assertSame(
            $this->getFilteredEntitiesFromGrid(['store[active]' => 0])->count(),
            $disabledStoresCount + 1
        );

        $this->resetGridFilters();
    }

    /**
     * @depends testToggleStatus
     */
    public function testBulkStatusUpdate(): void
    {
        $allStores = $this->getEntitiesFromGrid();
        $allStoresCount = $allStores->count();

        $enabledStores = $this->getFilteredEntitiesFromGrid(['store[active]' => 1]);
        $disabledStoresCount = $this->getFilteredEntitiesFromGrid(['store[active]' => 0])->count();

        // these numbers are know because of testToggleStatus which only disables one store
        Assert::assertEquals(1, $disabledStoresCount);
        Assert::assertEquals($allStoresCount - 1, $enabledStores->count());

        $this->resetGridFilters();

        $allStoreIds = [];
        /** @var TestEntityDTO $store */
        foreach ($allStores as $store) {
            $allStoreIds[] = $store->getId();
        }

        //first disable all of them
        $this->client->request(
            'POST',
            $this->router->generate('admin_stores_bulk_disable'),
            ['store_bulk' => $allStoreIds]
        );

        // check that all of them was disabled
        Assert::assertEquals(
            $allStoresCount,
            $this->getFilteredEntitiesFromGrid(['store[active]' => 0])->count()
        );
        // and none left active
        Assert::assertEmpty($this->getFilteredEntitiesFromGrid(['store[active]' => 1]));

        // then enable all of them again
        $this->client->request(
            'POST',
            $this->router->generate('admin_stores_bulk_enable'),
            ['store_bulk' => $allStoreIds]
        );

        // check that all of them was enabled
        Assert::assertEquals(
            $allStoresCount,
            $this->getFilteredEntitiesFromGrid(['store[active]' => 1])->count()
        );
        // and none left disabled
        Assert::assertEmpty($this->getFilteredEntitiesFromGrid(['store[active]' => 0]));

        // and reset grid filters, so it doesn't impact further steps
        $this->resetGridFilters();
    }

    /**
     * @depends testToggleStatus
     *
     * @return int
     */
    public function testDelete(): int
    {
        $initialEntityCount = $this->getEntitiesFromGrid()->count();
        $this->deleteEntityFromPage('admin_stores_delete', ['storeId' => 5]);

        $entityCount = $this->getEntitiesFromGrid()->count();
        $this->assertSame($initialEntityCount - 1, $entityCount);

        return $entityCount;
    }

    /**
     * @depends testDelete
     */
    public function testBulkDelete(): void
    {
        $initialEntityCount = $this->getEntitiesFromGrid()->count();
        $this->bulkDeleteEntitiesFromPage('admin_stores_bulk_delete', ['store_bulk' => [2, 3]]);
        $this->assertCount($initialEntityCount - 2, $this->getEntitiesFromGrid());
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
