<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Group;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\CustomerGroupResetter;

class CustomerGroupsControllerTest extends GridControllerTestCase
{
    private const DEFAULT_LANGUAGE_ID = 1;
    private const DEFAULT_SHOP_ID = 1;
    private const VISITORS_GROUP_ID = 1;
    private const GUESTS_GROUP_ID = 2;
    private const CUSTOMERS_GROUP_ID = 3;
    private const NON_EXISTING_GROUP_ID = 999999;

    private const DEFAULT_GROUPS_FORM_BUTTON = 'form-default-groups-options-save-button';
    private const DEFAULT_GROUPS_FORM_NAME = 'customer-groups-default-groups';

    private static int $wholesaleGroupId;
    private static int $retailGroupId;
    private static int $firstBulkGroupId;
    private static int $secondBulkGroupId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        CustomerGroupResetter::resetCustomerGroups();

        /** @var CommandBusInterface $commandBus */
        $commandBus = self::bootKernel()->getContainer()->get('prestashop.core.command_bus');
        self::$wholesaleGroupId = self::addGroup($commandBus, 'Wholesale', '5', true);
        self::$retailGroupId = self::addGroup($commandBus, 'Retail', '0', false);
        self::$firstBulkGroupId = self::addGroup($commandBus, 'Bulk first', '0', true);
        self::$secondBulkGroupId = self::addGroup($commandBus, 'Bulk second', '0', true);
        self::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        CustomerGroupResetter::resetCustomerGroups();
    }

    public function testIndex(): void
    {
        $groups = $this->getEntitiesFromGrid();

        foreach ([self::VISITORS_GROUP_ID, self::GUESTS_GROUP_ID, self::CUSTOMERS_GROUP_ID, self::$wholesaleGroupId] as $groupId) {
            $this->assertCollectionContainsEntity($groups, $groupId);
        }

        /** @var TestEntityDTO $group */
        foreach ($groups as $group) {
            $isDefaultGroup = in_array($group->getId(), [self::VISITORS_GROUP_ID, self::GUESTS_GROUP_ID, self::CUSTOMERS_GROUP_ID], true);
            $this->assertSame(!$isDefaultGroup, $group->getVariable('hasBulkCheckbox'), sprintf('Bulk checkbox of group #%d', $group->getId()));
            $this->assertSame(!$isDefaultGroup, $group->getVariable('hasDeleteAction'), sprintf('Delete action of group #%d', $group->getId()));
        }
    }

    /**
     * @depends testIndex
     */
    public function testFilters(): void
    {
        $gridFilters = [
            ['customer_groups[id_group]' => self::$wholesaleGroupId],
            ['customer_groups[name]' => 'holesal'],
            ['customer_groups[reduction]' => 5],
            ['customer_groups[members]' => 0],
            ['customer_groups[show_prices]' => 1],
        ];

        foreach ($gridFilters as $testFilter) {
            $this->assertContains(
                self::$wholesaleGroupId,
                $this->getIds($this->getFilteredEntitiesFromGrid($testFilter)),
                sprintf('Filter %s should match the wholesale group', var_export($testFilter, true))
            );
            // The filters form keeps the previous values, so each filter is checked alone
            $this->resetGridFilters();
        }

        $notShowingPrices = $this->getFilteredEntitiesFromGrid(['customer_groups[show_prices]' => 0]);
        $this->assertCollectionContainsEntity($notShowingPrices, self::$retailGroupId);
        $this->assertNotContains(self::$wholesaleGroupId, $this->getIds($notShowingPrices));
        $this->resetGridFilters();

        // The id filter is an exact match: id 1 must not also match 10, 11...
        $this->assertSame([self::VISITORS_GROUP_ID], $this->getIds($this->getFilteredEntitiesFromGrid(['customer_groups[id_group]' => 1])));
        $this->resetGridFilters();

        $this->assertCount(0, $this->getFilteredEntitiesFromGrid(['customer_groups[id_group]' => self::NON_EXISTING_GROUP_ID]));
        $this->resetGridFilters();
    }

    /**
     * @depends testIndex
     */
    public function testDeletingDefaultGroupIsRefused(): void
    {
        $this->client->request('POST', $this->router->generate('admin_customer_groups_delete', ['groupId' => self::CUSTOMERS_GROUP_ID]));
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('default group', $crawler->filter('.alert-danger .alert-text')->text());
        $this->assertCount(0, $crawler->filter('.alert-success .alert-text'));
        $this->assertCollectionContainsEntity($this->getEntitiesFromGrid(), self::CUSTOMERS_GROUP_ID);
    }

    /**
     * @depends testIndex
     */
    public function testDelete(): void
    {
        $this->client->disableReboot();

        $initialCount = $this->getEntitiesFromGrid()->count();
        $this->deleteEntityFromPage('admin_customer_groups_delete', ['groupId' => self::$retailGroupId]);

        $groups = $this->getEntitiesFromGrid();
        $this->assertCount($initialCount - 1, $groups);
        $this->assertNotContains(self::$retailGroupId, $this->getIds($groups));
    }

    /**
     * @depends testDelete
     */
    public function testBulkDeleteSkipsDefaultGroups(): void
    {
        $this->client->disableReboot();

        $this->client->request(
            'POST',
            $this->router->generate('admin_customer_groups_bulk_delete'),
            ['customer_groups_bulk' => [self::$firstBulkGroupId, self::GUESTS_GROUP_ID, self::$secondBulkGroupId]]
        );
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('default group', $crawler->filter('.alert-danger .alert-text')->text());

        $ids = $this->getIds($this->getEntitiesFromGrid());
        $this->assertContains(self::GUESTS_GROUP_ID, $ids);
        $this->assertNotContains(self::$firstBulkGroupId, $ids);
        $this->assertNotContains(self::$secondBulkGroupId, $ids);
    }

    /**
     * @depends testIndex
     */
    public function testBulkDeleteWithoutSelectionShowsAnInfo(): void
    {
        $this->client->request('POST', $this->router->generate('admin_customer_groups_bulk_delete'));
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('select at least one element', $crawler->filter('.alert-info .alert-text')->text());
        $this->assertCount(0, $crawler->filter('.alert-danger .alert-text'));
    }

    /**
     * @depends testIndex
     */
    public function testSaveDefaultGroups(): void
    {
        $crawler = $this->saveDefaultCustomersGroup(self::$wholesaleGroupId);
        $this->assertCount(1, $crawler->filter('.alert-success .alert-text'));
        $this->assertSame((string) self::$wholesaleGroupId, $this->getDefaultCustomersGroupValue($crawler));

        // The new default group can no longer be deleted from the grid
        /** @var TestEntityDTO $group */
        foreach ($this->getEntitiesFromGrid() as $group) {
            if ($group->getId() === self::$wholesaleGroupId) {
                $this->assertFalse($group->getVariable('hasDeleteAction'));
            }
        }

        $crawler = $this->saveDefaultCustomersGroup(self::CUSTOMERS_GROUP_ID);
        $this->assertSame((string) self::CUSTOMERS_GROUP_ID, $this->getDefaultCustomersGroupValue($crawler));
    }

    /**
     * @depends testSaveDefaultGroups
     */
    public function testSavingInvalidDefaultGroupShowsAnError(): void
    {
        $crawler = $this->saveDefaultCustomersGroup(self::NON_EXISTING_GROUP_ID);

        $this->assertGreaterThan(0, $crawler->filter('.alert-danger .alert-text')->count());
        $this->assertCount(0, $crawler->filter('.alert-success .alert-text'));
        $this->assertSame((string) self::CUSTOMERS_GROUP_ID, $this->getDefaultCustomersGroupValue($crawler));
    }

    public function testDisabledFeatureShowsOnlyTheWarning(): void
    {
        $configuration = $this->client->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_GROUP_FEATURE_ACTIVE', 0);
        // Group::isFeatureActive() keeps the value in a static cache for the whole process
        Group::clearCachedValues();

        try {
            $crawler = $this->client->request('GET', $this->generateGridUrl());
            $this->assertResponseIsSuccessful();

            $warning = $crawler->filter('.alert-warning');
            $this->assertStringContainsString('This feature has been disabled', $warning->text());
            $this->assertStringContainsString('#optional_features', (string) $warning->filter('a')->attr('href'));
            $this->assertCount(0, $crawler->filter($this->getGridSelector()));
            $this->assertCount(0, $crawler->filter('#' . self::DEFAULT_GROUPS_FORM_BUTTON));
        } finally {
            $configuration->set('PS_GROUP_FEATURE_ACTIVE', 1);
            Group::clearCachedValues();
        }
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'customer_groups[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'customer_groups[offset]' => 0,
                'customer_groups[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_customer_groups_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#customer_groups_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        $groupId = (int) trim($tr->filter('.column-id_group')->text());
        $deleteUrl = $this->router->generate('admin_customer_groups_delete', ['groupId' => $groupId]);

        return new TestEntityDTO(
            $groupId,
            [
                'hasBulkCheckbox' => $tr->filter('.js-bulk-action-checkbox')->count() > 0,
                'hasDeleteAction' => $tr->filter(sprintf('[data-url^="%s"]', strtok($deleteUrl, '?')))->count() > 0,
            ]
        );
    }

    private static function addGroup(CommandBusInterface $commandBus, string $name, string $reduction, bool $showPrices): int
    {
        /** @var GroupId $groupId */
        $groupId = $commandBus->handle(new AddCustomerGroupCommand(
            [self::DEFAULT_LANGUAGE_ID => $name],
            new DecimalNumber($reduction),
            false,
            $showPrices,
            [self::DEFAULT_SHOP_ID]
        ));

        return $groupId->getValue();
    }

    private function saveDefaultCustomersGroup(int $groupId): Crawler
    {
        $this->client->disableReboot();

        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton(self::DEFAULT_GROUPS_FORM_BUTTON)->form();
        $form->disableValidation();
        $form[self::DEFAULT_GROUPS_FORM_NAME . '[customers_group]'] = (string) $groupId;

        $this->client->submit($form);

        return $this->client->followRedirect();
    }

    private function getDefaultCustomersGroupValue(Crawler $crawler): string
    {
        return (string) $crawler->selectButton(self::DEFAULT_GROUPS_FORM_BUTTON)->form()[self::DEFAULT_GROUPS_FORM_NAME . '[customers_group]']->getValue();
    }

    /**
     * @return int[]
     */
    private function getIds(iterable $groups): array
    {
        $ids = [];
        foreach ($groups as $group) {
            $ids[] = $group->getId();
        }

        return $ids;
    }
}
