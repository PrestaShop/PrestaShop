<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Grid;

use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\RequestSqlFilters;
use RequestSql;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

class RequestSqlGridExtraPropertyTest extends KernelTestCase
{
    use ContextMockerTrait;

    private const MODULE = 'extrapropertyrequestsqlgridtest';

    private static ExtraPropertyRegistryInterface $registry;

    private RequestSql $requestSql;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        // Same context preparation as ContactGridExtraPropertyTest: the grid query builder reads
        // the legacy static Context, and the grid modifier consumes the ShopContext a kernel test
        // does not initialize.
        static::mockContext();

        $container = self::getContainer();

        $shopContextBuilder = $container->get(ShopContextBuilder::class);
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$registry->register(self::commonDefinition());
    }

    public static function tearDownAfterClass(): void
    {
        self::$registry->unregister(self::commonDefinition(), true);
        static::resetContext();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The install data provides no SQL query: the test adds its own row.
        $this->requestSql = new RequestSql();
        $this->requestSql->name = 'Extra property grid test';
        $this->requestSql->sql = 'SELECT id_lang FROM ' . _DB_PREFIX_ . 'lang';
        $this->assertTrue($this->requestSql->add());
    }

    protected function tearDown(): void
    {
        $this->requestSql->delete();

        parent::tearDown();
    }

    public function testRequestSqlGridSelectsItsExtraProperty(): void
    {
        $requestSqlId = (int) $this->requestSql->id;

        /** @var ExtraPropertyWriterInterface $writer */
        $writer = self::getContainer()->get(ExtraPropertyWriterInterface::class);
        $writer->writeAll('request_sql', 'id_request_sql', $requestSqlId, [self::MODULE => [
            'owner' => 'Reporting',
        ]], ShopConstraint::allShops());

        /** @var GridDataFactoryInterface $gridDataFactory */
        $gridDataFactory = self::getContainer()->get('prestashop.core.grid.data_factory.request_sql');
        $gridData = $gridDataFactory->getData(new RequestSqlFilters(RequestSqlFilters::getDefaults()));

        $this->assertGreaterThan(0, $gridData->getRecordsTotal());

        $records = iterator_to_array($gridData->getRecords());
        $recordsById = array_column($records, null, 'id_request_sql');
        $this->assertArrayHasKey($requestSqlId, $recordsById);
        // The data factory must give the extra properties modifier the grid id of the definition
        // ("sql_request"): with another id the extra table is never joined and the column stays empty.
        $this->assertSame('Reporting', $recordsById[$requestSqlId][self::commonDefinition()->getFieldName()] ?? null);
    }

    private static function commonDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'sql_request',
            propertyName: 'owner',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
            size: 64,
            associatedGrids: ['sql_request'],
            labelWording: 'Owner',
            labelDomain: 'Modules.Extrapropertyrequestsqlgridtest.Admin',
        );
    }
}
