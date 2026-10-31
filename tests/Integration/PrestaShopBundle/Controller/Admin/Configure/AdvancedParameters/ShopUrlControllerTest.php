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

class ShopUrlControllerTest extends FormGridControllerTestCase
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
        $shopUrls = $this->getEntitiesFromGrid();
        $this->assertCount(1, $shopUrls);

        return $shopUrls->count();
    }

    /**
     * @depends testIndex
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop_url[url_options][shop_id]' => '1',
            'shop_url[url_options][main]' => '0',
            'shop_url[url_options][active]' => '1',
            'shop_url[store_url][domain]' => 'created.test',
            'shop_url[store_url][domain_ssl]' => 'created.test',
            'shop_url[store_url][physical_uri]' => '/',
            'shop_url[store_url][virtual_uri]' => 'created/',
        ];
        $shopUrlId = $this->createEntityFromPage($formData);

        $shopUrls = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $shopUrls);
        $this->assertCollectionContainsEntity($shopUrls, $shopUrlId);
        $this->assertFormValuesFromPage(['shopUrlId' => $shopUrlId], $formData);

        return $shopUrlId;
    }

    /**
     * @depends testCreate
     */
    public function testEdit(int $shopUrlId): int
    {
        $this->client->disableReboot();

        $formData = [
            'shop_url[url_options][shop_id]' => '1',
            'shop_url[url_options][main]' => '0',
            'shop_url[url_options][active]' => '0',
            'shop_url[store_url][domain]' => 'edited.test',
            'shop_url[store_url][domain_ssl]' => 'secure.edited.test',
            'shop_url[store_url][physical_uri]' => '/store/',
            'shop_url[store_url][virtual_uri]' => 'edited/',
        ];
        $this->editEntityFromPage(['shopUrlId' => $shopUrlId], $formData);
        $this->assertFormValuesFromPage(['shopUrlId' => $shopUrlId], $formData);

        return $shopUrlId;
    }

    /**
     * @depends testEdit
     */
    public function testFilters(int $shopUrlId): int
    {
        foreach ([['shop_url[id_shop_url]' => $shopUrlId], ['shop_url[url]' => 'edited.test/store/edited']] as $testFilter) {
            $shopUrls = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertCount(1, $shopUrls);
            $this->assertCollectionContainsEntity($shopUrls, $shopUrlId);
        }
        $this->resetGridFilters();

        return $shopUrlId;
    }

    /**
     * @depends testFilters
     */
    public function testDelete(int $shopUrlId): void
    {
        $this->client->disableReboot();

        $initialEntityCount = $this->getEntitiesFromGrid()->count();
        $this->deleteEntityFromPage('admin_shop_urls_delete', ['shopUrlId' => $shopUrlId]);

        $this->assertCount($initialEntityCount - 1, $this->getEntitiesFromGrid());
    }

    public function testMainUrlCannotBeDeletedFromGrid(): void
    {
        $crawler = $this->client->request('GET', $this->generateGridUrl());

        $this->assertCount(0, $crawler->filter('#shop_url_grid_table .grid-delete-row-link'));
    }

    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_shop_urls_create');
    }

    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.shop_url_form_handler');

        return $checker;
    }

    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_shop_urls_edit', $routeParams);
    }

    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'shop_url[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        return $this->router->generate('admin_shop_urls_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#shop_url_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_shop_url')->text()),
            ['url' => trim($tr->filter('.column-url')->text())]
        );
    }
}
