<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use Configuration;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;
use ShopUrl;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ShopResetter;

class ShopGroupControllerTest extends FormGridControllerTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 1);
    }

    public static function tearDownAfterClass(): void
    {
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 0);
        parent::tearDownAfterClass();
    }

    public function testIndex(): int
    {
        $shopGroups = $this->getEntitiesFromGrid();
        $this->assertCount(1, $shopGroups);

        return $shopGroups->count();
    }

    /**
     * @depends testIndex
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop_group[name]' => 'Created group',
            'shop_group[color]' => '#ff0000',
            'shop_group[share_customer]' => '1',
            'shop_group[share_stock]' => '1',
            'shop_group[share_order]' => '1',
            'shop_group[active]' => '1',
        ];
        $shopGroupId = $this->createEntityFromPage($formData);

        $shopGroups = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $shopGroups);
        $this->assertCollectionContainsEntity($shopGroups, $shopGroupId);
        $this->assertFormValuesFromPage(['shopGroupId' => $shopGroupId], $formData);

        return $shopGroupId;
    }

    /**
     * @depends testCreate
     */
    public function testEdit(int $shopGroupId): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop_group[name]' => 'Edited group',
            'shop_group[color]' => 'blue',
            'shop_group[share_customer]' => '1',
            'shop_group[share_stock]' => '0',
            'shop_group[share_order]' => '0',
            'shop_group[active]' => '0',
        ];
        $this->editEntityFromPage(['shopGroupId' => $shopGroupId], $formData);
        $this->assertFormValuesFromPage(['shopGroupId' => $shopGroupId], $formData);

        return $shopGroupId;
    }

    /**
     * @depends testEdit
     */
    public function testFilters(int $shopGroupId): int
    {
        foreach ([['shop_group[id_shop_group]' => $shopGroupId], ['shop_group[name]' => 'Edited']] as $testFilter) {
            $shopGroups = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertCount(1, $shopGroups);
            $this->assertCollectionContainsEntity($shopGroups, $shopGroupId);
        }
        $this->resetGridFilters();

        return $shopGroupId;
    }

    /**
     * @depends testFilters
     */
    public function testDelete(int $shopGroupId): void
    {
        $this->client->disableReboot();

        $initialEntityCount = $this->getEntitiesFromGrid()->count();
        $this->deleteEntityFromPage('admin_shop_groups_delete', ['shopGroupId' => $shopGroupId]);

        $this->assertCount($initialEntityCount - 1, $this->getEntitiesFromGrid());
    }

    public function testGroupContainingShopsCannotBeDeletedFromGrid(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());

        $this->assertCount(0, $crawler->filter('#shop_group_grid_table .grid-delete-row-link'));
    }

    public function testShopTreeCanBeCollapsedAndExpanded(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());

        $this->assertCount(1, $crawler->filter('#shop-tree .js-shop-tree-collapse-all'));
        $this->assertCount(1, $crawler->filter('#shop-tree .js-shop-tree-expand-all'));
        $this->assertCount(1, $crawler->filter('#shop-tree .js-shop-tree-group.collapse.show'));
    }

    public function testSaveDefaultShop(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $form = $crawler->filter('#save-multistore-options-button')->form();
        $form['multistore_options[default_shop_id]'] = (string) Configuration::get('PS_SHOP_DEFAULT');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('.alert-success .alert-text'));
        $this->assertCount(0, $crawler->filter('.alert-danger .alert-text'));
    }

    public function testSaveDefaultShopFromSingleShopContext(): void
    {
        $contextShopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        $newDefaultShopId = $this->addShopWithMainUrl((int) (new Shop($contextShopId))->id_shop_group);

        try {
            static::loginUser($this->client, ShopConstraint::shop($contextShopId));

            $crawler = $this->client->request('GET', $this->generateGridUrl());
            $form = $crawler->filter('#save-multistore-options-button')->form();
            $form['multistore_options[default_shop_id]'] = (string) $newDefaultShopId;

            $this->client->submit($form);
            $crawler = $this->client->followRedirect();

            $this->assertCount(1, $crawler->filter('.alert-success .alert-text'));
            $this->assertSame($newDefaultShopId, (int) Db::getInstance()->getValue(
                'SELECT `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_SHOP_DEFAULT" AND `id_shop` IS NULL AND `id_shop_group` IS NULL'
            ));
            $this->assertSame(0, (int) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_SHOP_DEFAULT" AND (`id_shop` IS NOT NULL OR `id_shop_group` IS NOT NULL)'
            ));
        } finally {
            ShopResetter::resetShops();
        }
    }

    private function addShopWithMainUrl(int $shopGroupId): int
    {
        $shop = new Shop();
        $shop->name = 'Second shop';
        $shop->id_shop_group = $shopGroupId;
        $shop->id_category = (int) Configuration::get('PS_HOME_CATEGORY');
        $shop->add();

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = (int) $shop->id;
        $shopUrl->domain = 'second-shop.test';
        $shopUrl->domain_ssl = 'second-shop.test';
        $shopUrl->physical_uri = '/';
        $shopUrl->virtual_uri = '';
        $shopUrl->main = true;
        $shopUrl->active = true;
        $shopUrl->add();

        return (int) $shop->id;
    }

    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_shop_groups_create');
    }

    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.shop_group_form_handler');

        return $checker;
    }

    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_shop_groups_edit', $routeParams);
    }

    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'shop_group[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        return $this->router->generate('admin_shop_groups_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#shop_group_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_shop_group')->text()),
            ['name' => trim($tr->filter('.column-name')->text())]
        );
    }
}
