<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Grid;

use Db;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\SupplierFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

class SupplierGridExtraPropertyTest extends KernelTestCase
{
    use ContextMockerTrait;

    private const MODULE = 'extrapropertysuppliergridtest';

    private static ExtraPropertyRegistryInterface $registry;

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

    public function testSupplierGridSelectsItsExtraProperty(): void
    {
        $supplierId = (int) Db::getInstance()->getValue('SELECT MIN(id_supplier) FROM `' . _DB_PREFIX_ . 'supplier`');
        $this->assertGreaterThan(0, $supplierId, 'The test installation must provide at least one supplier');

        /** @var ExtraPropertyWriterInterface $writer */
        $writer = self::getContainer()->get(ExtraPropertyWriterInterface::class);
        $writer->writeAll('supplier', 'id_supplier', $supplierId, [self::MODULE => [
            'erp_code' => 'SUP-0001',
        ]], ShopConstraint::allShops());

        /** @var GridDataFactoryInterface $gridDataFactory */
        $gridDataFactory = self::getContainer()->get('prestashop.core.grid.data_provider.supplier');
        $gridData = $gridDataFactory->getData(new SupplierFilters(SupplierFilters::getDefaults()));

        $this->assertGreaterThan(0, $gridData->getRecordsTotal());

        $records = iterator_to_array($gridData->getRecords());
        $recordsById = array_column($records, null, 'id_supplier');
        $this->assertArrayHasKey($supplierId, $recordsById);
        // The data factory must give the extra properties modifier the grid id of the definition
        // ("supplier"): with another id the extra table is never joined and the column stays empty.
        $this->assertSame('SUP-0001', $recordsById[$supplierId][self::commonDefinition()->getFieldName()] ?? null);
    }

    private static function commonDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'supplier',
            propertyName: 'erp_code',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
            size: 64,
            associatedGrids: ['supplier'],
            labelWording: 'ERP code',
            labelDomain: 'Modules.Extrapropertysuppliergridtest.Admin',
        );
    }
}
