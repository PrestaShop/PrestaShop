<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\FeatureFlag;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

class GridViewControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const GRID_ID = 'order';
    private const GRID_ROUTE = 'admin_orders_index';
    private const SHOP_ID = 1;

    protected RouterInterface $router;
    protected EntityManagerInterface $entityManager;
    protected Connection $connection;
    protected string $dbPrefix;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->disableReboot();
        $this->loginUser($this->client);

        $container = $this->client->getContainer();
        $this->router = $container->get('router');
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->connection = $container->get(Connection::class);
        $this->dbPrefix = $container->getParameter('database_prefix');

        $this->setFeatureFlagState(true);
    }

    public function testPanelIsDisplayedOnTheOrdersGridWhenTheFeatureIsEnabled(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('.js-grid-views'));
        $this->assertCount(1, $crawler->filter('form[name="grid_view_order"]'));
        $this->assertCount(1, $crawler->filter('form[name="grid_configuration_order"]'));
    }

    public function testPanelAndEndpointsAreDisabledWithTheFeatureFlag(): void
    {
        $this->setFeatureFlagState(false);

        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('.js-grid-views'));

        $this->client->request('GET', $this->generateListUrl());
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateViewFromThePanelFormWithADynamicDateRule(): void
    {
        $this->resetPersistedFilters();

        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE, [
            'order' => ['filters' => ['date_add' => ['from' => '2020-01-01', 'to' => '2039-12-31']]],
        ]));

        $form = $crawler->filter('form[name="grid_view_order"]')->form();
        $form['grid_view_order[name]'] = 'Recent orders';
        $form['grid_view_order[shared]'] = '1';
        $form['grid_view_order[dynamic_date_rules][date_add][date_rule]'] = 'current_year';
        $this->client->submit($form);

        $this->assertJsonSuccess();

        $this->entityManager->clear();
        $gridView = $this->entityManager->getRepository(AdminGridView::class)->findOneBy(['name' => 'Recent orders']);

        $this->assertNotNull($gridView);
        $this->assertTrue($gridView->isShared());
        $criteria = json_decode($gridView->getFilters(), true);
        $this->assertSame(['from' => '2020-01-01', 'to' => '2039-12-31'], $criteria['filters']['date_add']);
        $this->assertSame('current_year', $gridView->getDynamicDateRules()['date_add']['date_rule']);
        $this->assertSame(self::GRID_ID, $gridView->getGridConfiguration()->getGridId());
        $this->assertSame($this->getTestEmployeeId(), $gridView->getGridConfiguration()->getEmployeeId());
    }

    public function testAViewCanBeSavedWithoutActiveFilters(): void
    {
        $this->resetPersistedFilters();

        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE));

        $form = $crawler->filter('form[name="grid_view_order"]')->form();
        $form['grid_view_order[name]'] = 'All orders';
        $this->client->submit($form);

        $this->assertJsonSuccess();

        $this->entityManager->clear();
        $gridView = $this->entityManager->getRepository(AdminGridView::class)->findOneBy(['name' => 'All orders']);

        $this->assertNotNull($gridView);
        $this->assertFalse($gridView->isShared());
        $this->assertEmpty(json_decode($gridView->getFilters(), true)['filters'] ?? []);
    }

    public function testApplyingAViewReplacesTheCurrentFiltersAndRecomputesDateRules(): void
    {
        $gridView = $this->createViewFixture('Current year orders', [
            'limit' => 50,
            'filters' => ['date_add' => ['from' => '2011-01-01', 'to' => '2012-12-31']],
        ], dateRules: ['date_add' => ['date_rule' => 'current_year', 'custom_days' => null]]);

        $this->client->request('GET', $this->router->generate(self::GRID_ROUTE, [
            'order' => ['filters' => ['date_add' => ['from' => '2035-01-01', 'to' => '2039-12-31']]],
        ]));

        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE, [
            'grid_view' => $gridView->getId(),
        ]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $dateFromInput = $crawler->filter('input[name="order[date_add][from]"]');
        $this->assertSame(date('Y') . '-01-01', $dateFromInput->attr('value'));
        $this->assertSame((string) $gridView->getId(), $crawler->filter('.js-grid-views')->attr('data-selected-view-id'));
    }

    public function testListOnlyExposesOwnAndSharedViews(): void
    {
        $ownView = $this->createViewFixture('My own view', ['filters' => ['reference' => 'OWN']]);
        $sharedView = $this->createViewFixture('Shared by colleague', ['filters' => ['reference' => 'SHARED']], employeeId: 99999, shared: true);
        $privateView = $this->createViewFixture('Private of colleague', ['filters' => ['reference' => 'PRIVATE']], employeeId: 99999, shared: false);

        $this->client->request('GET', $this->generateListUrl());
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $content = (string) $response->getContent();
        $this->assertStringContainsString('My own view', $content);
        $this->assertStringContainsString('Shared by colleague', $content);
        $this->assertStringNotContainsString('Private of colleague', $content);
        $this->assertStringContainsString('grid_view=' . $ownView->getId(), $content);
        $this->assertStringContainsString('grid_view=' . $sharedView->getId(), $content);
    }

    public function testCountsAreComputedServerSide(): void
    {
        $matchingAll = $this->createViewFixture('Counts: all', ['filters' => ['date_add' => ['from' => '2000-01-01', 'to' => '2099-12-31']]]);
        $matchingNone = $this->createViewFixture('Counts: none', ['filters' => ['date_add' => ['from' => '2035-01-01', 'to' => '2036-01-01']]]);

        $this->client->request('GET', $this->router->generate('admin_grid_views_counts', [
            'gridId' => self::GRID_ID,
            'route' => self::GRID_ROUTE,
        ]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $counts = json_decode((string) $this->client->getResponse()->getContent(), true)['counts'];

        $this->assertGreaterThan(0, $counts[$matchingAll->getId()]);
        $this->assertSame(0, $counts[$matchingNone->getId()]);
    }

    public function testCountsAndExportWorkOnTheProductGrid(): void
    {
        $productView = $this->createViewFixture(
            'All products',
            ['filters' => []],
            gridId: 'product',
            controllerRoute: 'admin_products_index'
        );

        $this->client->request('GET', $this->router->generate('admin_grid_views_counts', [
            'gridId' => 'product',
            'route' => 'admin_products_index',
        ]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $counts = json_decode((string) $this->client->getResponse()->getContent(), true)['counts'];
        $this->assertIsInt($counts[$productView->getId()]);
        $this->assertGreaterThan(0, $counts[$productView->getId()]);

        $this->client->request('GET', $this->router->generate('admin_grid_views_export', [
            'gridViewId' => $productView->getId(),
        ]));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $csvContent = (string) $this->client->getInternalResponse()->getContent();
        $this->assertGreaterThan(1, count(array_filter(explode("\n", $csvContent))));
    }

    public function testExportReturnsACsvOfTheViewRecords(): void
    {
        $gridView = $this->createViewFixture('Export me', ['filters' => ['date_add' => ['from' => '2000-01-01', 'to' => '2099-12-31']]]);

        $this->client->request('GET', $this->router->generate('admin_grid_views_export', [
            'gridViewId' => $gridView->getId(),
        ]));
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('content-disposition'));

        $csvContent = (string) $this->client->getInternalResponse()->getContent();
        $this->assertNotEmpty($csvContent);
        $this->assertGreaterThan(1, count(array_filter(explode("\n", $csvContent))));
    }

    public function testDuplicateThenDeleteOwnView(): void
    {
        $gridView = $this->createViewFixture('To duplicate', ['filters' => ['reference' => 'DUP']]);

        $this->client->request('POST', $this->router->generate('admin_grid_views_duplicate', [
            'gridViewId' => $gridView->getId(),
        ]));
        $this->assertJsonSuccess();

        $this->entityManager->clear();
        $copy = $this->entityManager->getRepository(AdminGridView::class)->findOneBy(['name' => 'Copy of To duplicate']);
        $this->assertNotNull($copy);
        $this->assertFalse($copy->isShared());
        $copyId = $copy->getId();

        $this->client->request('POST', $this->router->generate('admin_grid_views_delete', [
            'gridViewId' => $copyId,
        ]));
        $this->assertJsonSuccess();

        $this->entityManager->clear();
        $this->assertNull($this->entityManager->getRepository(AdminGridView::class)->find($copyId));
    }

    public function testDeletingAnotherEmployeeViewIsForbidden(): void
    {
        $foreignView = $this->createViewFixture('Not yours', ['filters' => ['reference' => 'X']], employeeId: 99999, shared: true);

        $this->client->request('POST', $this->router->generate('admin_grid_views_delete', [
            'gridViewId' => $foreignView->getId(),
        ]));

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    public function testSaveConfiguration(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate(self::GRID_ROUTE));

        $form = $crawler->filter('form[name="grid_configuration_order"]')->form();
        $form['grid_configuration_order[display_totals]']->untick();
        $form['grid_configuration_order[display_shared_filters]']->tick();
        $this->client->submit($form);

        $this->assertJsonSuccess();

        $this->entityManager->clear();
        $configuration = $this->entityManager->getRepository(AdminGridConfiguration::class)->findForEmployee(
            $this->getTestEmployeeId(),
            self::SHOP_ID,
            self::GRID_ID,
            self::GRID_ROUTE
        );

        $this->assertNotNull($configuration);
        $this->assertFalse($configuration->displayTotals());
        $this->assertTrue($configuration->displaySharedFilters());
    }

    private function generateListUrl(): string
    {
        return $this->router->generate('admin_grid_views_list', [
            'gridId' => self::GRID_ID,
            'route' => self::GRID_ROUTE,
        ]);
    }

    private function assertJsonSuccess(): void
    {
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), (string) $response->getContent());
        $decodedResponse = json_decode((string) $response->getContent(), true);
        $this->assertTrue($decodedResponse['success'] ?? false, (string) $response->getContent());
    }

    private function setFeatureFlagState(bool $enabled): void
    {
        /** @var FeatureFlag $featureFlag */
        $featureFlag = $this->entityManager->getRepository(FeatureFlag::class)->findOneBy([
            'name' => FeatureFlagSettings::FEATURE_FLAG_GRID_VIEWS,
        ]);

        $enabled ? $featureFlag->enable() : $featureFlag->disable();
        $this->entityManager->flush();
    }

    private function getTestEmployeeId(): int
    {
        /** @var Employee $employee */
        $employee = $this->entityManager->getRepository(Employee::class)->findOneBy(['email' => 'test@prestashop.com']);

        return $employee->getId();
    }

    private function resetPersistedFilters(): void
    {
        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . 'admin_filter WHERE filter_id = :filterId',
            ['filterId' => self::GRID_ID]
        );
    }

    private function createViewFixture(
        string $name,
        array $criteria,
        ?int $employeeId = null,
        bool $shared = false,
        ?array $dateRules = null,
        string $gridId = self::GRID_ID,
        string $controllerRoute = self::GRID_ROUTE,
    ): AdminGridView {
        $employeeId = $employeeId ?? $this->getTestEmployeeId();

        $configuration = $this->entityManager->getRepository(AdminGridConfiguration::class)->findOrCreateForEmployee(
            $employeeId,
            self::SHOP_ID,
            $gridId,
            $gridId,
            $controllerRoute
        );

        $gridView = new AdminGridView();
        $gridView
            ->setName($name)
            ->setFilterId($gridId)
            ->setShared($shared)
            ->setFilters((string) json_encode($criteria))
            ->setDynamicDateRules($dateRules)
        ;
        $configuration->addView($gridView);

        $this->entityManager->persist($gridView);
        $this->entityManager->flush();

        return $gridView;
    }
}
