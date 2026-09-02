<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\BusinessEntityResetter;

class BusinessEntityControllerTest extends GridControllerTestCase
{
    private const DEFAULT_SHOP_ID = 1;
    private const DEFAULT_CUSTOMER_GROUP_ID = 1;
    private const DEFAULT_COUNTRY_ID = 8;

    private const ACTIVE_COMPANY_NAME = 'Grid active company';
    private const PENDING_COMPANY_NAME = 'Grid pending company';

    private static int $activeBusinessEntityId;

    private static int $pendingBusinessEntityId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        BusinessEntityResetter::resetBusinessEntities();

        $commandBus = self::bootKernel()->getContainer()->get('prestashop.core.command_bus');
        self::$activeBusinessEntityId = self::createBusinessEntity(
            $commandBus,
            self::ACTIVE_COMPANY_NAME,
            'Grid active legal name',
            BusinessEntityStatus::ACTIVE
        );
        self::$pendingBusinessEntityId = self::createBusinessEntity(
            $commandBus,
            self::PENDING_COMPANY_NAME,
            'Grid pending legal name',
            BusinessEntityStatus::PENDING
        );
        self::ensureKernelShutdown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        BusinessEntityResetter::resetBusinessEntities();
    }

    public function testIndex(): void
    {
        $businessEntities = $this->getEntitiesFromGrid();

        $this->assertCount(2, $businessEntities);
        $this->assertCollectionContainsEntity($businessEntities, self::$activeBusinessEntityId);
        $this->assertCollectionContainsEntity($businessEntities, self::$pendingBusinessEntityId);
    }

    /**
     * @depends testIndex
     */
    public function testFilters(): void
    {
        $testCases = [
            [
                ['business_entity[id_business_entity]' => self::$activeBusinessEntityId],
                self::$activeBusinessEntityId,
            ],
            [
                ['business_entity[name]' => self::PENDING_COMPANY_NAME],
                self::$pendingBusinessEntityId,
            ],
            [
                ['business_entity[legal_name]' => 'Grid active legal name'],
                self::$activeBusinessEntityId,
            ],
            [
                ['business_entity[status]' => BusinessEntityStatus::ACTIVE->value],
                self::$activeBusinessEntityId,
            ],
        ];

        foreach ($testCases as [$testFilter, $expectedBusinessEntityId]) {
            $this->resetGridFilters();
            $businessEntities = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertCount(1, $businessEntities, sprintf(
                'Expected exactly one business entity with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($businessEntities, $expectedBusinessEntityId);
        }

        $this->resetGridFilters();
        $this->assertEmpty($this->getFilteredEntitiesFromGrid([
            'business_entity[name]' => 'No business entity bears this name',
        ]));

        $this->resetGridFilters();
    }

    private static function createBusinessEntity(
        CommandBusInterface $commandBus,
        string $name,
        string $legalName,
        BusinessEntityStatus $status
    ): int {
        $businessEntityId = $commandBus->handle(new AddBusinessEntityCommand(
            $name,
            $legalName,
            null,
            true,
            $status,
            self::DEFAULT_SHOP_ID,
            self::DEFAULT_CUSTOMER_GROUP_ID,
            true,
            [
                new BusinessEntityBillingAddress(
                    'Billing',
                    '123 Main St',
                    null,
                    'Paris',
                    '75001',
                    self::DEFAULT_COUNTRY_ID,
                    true,
                    null
                ),
            ]
        ));

        return $businessEntityId->getValue();
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'business_entity[actions][search]';
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'business_entity[offset]' => 0,
                'business_entity[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_business_entities_list', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#business_entity_grid_table';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_business_entity')->text()),
            []
        );
    }
}
