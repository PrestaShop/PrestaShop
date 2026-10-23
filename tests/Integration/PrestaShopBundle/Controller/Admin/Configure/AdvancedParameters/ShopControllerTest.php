<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Configuration;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ShopResetter;

class ShopControllerTest extends FormGridControllerTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 1);
    }

    public static function tearDownAfterClass(): void
    {
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 0);
        ShopResetter::resetShops();
        parent::tearDownAfterClass();
    }

    public function testIndex(): int
    {
        $shops = $this->getEntitiesFromGrid();
        $this->assertCount(1, $shops);

        return $shops->count();
    }

    /**
     * @depends testIndex
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop[store][name]' => 'Created shop',
            'shop[store][color]' => '#ff0000',
            'shop[store][shop_group_id]' => '1',
            'shop[store][root_category_id]' => (string) Configuration::get('PS_HOME_CATEGORY'),
            'shop[store][theme_name]' => 'classic',
            'shop[import][use_import_data]' => '0',
        ];
        $shopId = $this->createEntityFromPage($formData);

        $shops = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $shops);
        $this->assertCollectionContainsEntity($shops, $shopId);
        unset($formData['shop[import][use_import_data]']);
        $this->assertFormValuesFromPage(['shopId' => $shopId], $formData);

        return $shopId;
    }

    /**
     * @depends testCreate
     */
    public function testEdit(int $shopId): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop[store][name]' => 'Edited shop',
            'shop[store][color]' => 'blue',
        ];
        $this->editEntityFromPage(['shopId' => $shopId], $formData);
        $this->assertFormValuesFromPage(['shopId' => $shopId], $formData);

        return $shopId;
    }

    /**
     * @depends testEdit
     */
    public function testFilters(int $shopId): int
    {
        foreach ([['shop[id_shop]' => $shopId], ['shop[name]' => 'Edited']] as $testFilter) {
            $shops = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertCount(1, $shops);
            $this->assertCollectionContainsEntity($shops, $shopId);
        }
        $this->resetGridFilters();

        return $shopId;
    }

    /**
     * @depends testFilters
     */
    public function testShopWithoutUrlHasAddUrlActionAndDefaultShopCannotBeDeleted(int $shopId): int
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $defaultShopId = (int) Configuration::get('PS_SHOP_DEFAULT');

        $this->assertCount(0, $this->getGridRow($crawler, $defaultShopId)->filter('.grid-delete-row-link'));
        $this->assertCount(1, $this->getGridRow($crawler, $shopId)->filter('.grid-delete-row-link'));
        $this->assertCount(1, $this->getGridRow($crawler, $shopId)->filter('a[href*="shop-urls/new?shopId=' . $shopId . '"]'));
        $this->assertCount(0, $this->getGridRow($crawler, $defaultShopId)->filter('a[href*="shop-urls/new"]'));

        return $shopId;
    }

    /**
     * @depends testShopWithoutUrlHasAddUrlActionAndDefaultShopCannotBeDeleted
     */
    public function testDelete(int $shopId): void
    {
        $this->client->disableReboot();

        $initialEntityCount = $this->getEntitiesFromGrid()->count();
        $this->deleteEntityFromPage('admin_shops_delete', ['shopId' => $shopId]);

        $this->assertCount($initialEntityCount - 1, $this->getEntitiesFromGrid());
    }

    private function getGridRow(Crawler $crawler, int $shopId): Crawler
    {
        return $crawler->filter($this->getGridSelector() . ' tbody tr')->reduce(
            static fn (Crawler $tr): bool => (int) trim($tr->filter('.column-id_shop')->text()) === $shopId
        );
    }

    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_shops_create');
    }

    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.shop_form_handler');

        return $checker;
    }

    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_shops_edit', $routeParams);
    }

    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'shop[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        return $this->router->generate('admin_shops_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#shop_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_shop')->text()),
            ['name' => trim($tr->filter('.column-name')->text())]
        );
    }
}
