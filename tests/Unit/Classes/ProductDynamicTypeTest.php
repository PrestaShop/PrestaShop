<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Product;
use ReflectionClass;

/**
 * Product::getDynamicProductType() computes the product type from existing associations.
 */
class ProductDynamicTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Prime the Configuration static cache so Pack::isFeatureActive() (called by Pack::isPack())
        // resolves to the default (false) without hitting the database.
        $reflection = new ReflectionClass(Configuration::class);
        $initialized = $reflection->getProperty('_initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(null, true);
    }

    protected function tearDown(): void
    {
        Configuration::resetStaticCache();
        parent::tearDown();
    }

    /**
     * @dataProvider provideDynamicTypeCases
     */
    public function testGetDynamicProductType(bool $isVirtual, bool $hasCombinations, string $expected): void
    {
        $product = new DynamicTypeProductStub();
        $product->is_virtual = $isVirtual;
        $product->stubHasCombinations = $hasCombinations;

        $this->assertSame($expected, $product->getDynamicProductType());
    }

    public function provideDynamicTypeCases(): iterable
    {
        yield 'virtual only => virtual' => [true, false, ProductType::TYPE_VIRTUAL];
        yield 'combinations only => combinations' => [false, true, ProductType::TYPE_COMBINATIONS];
        yield 'neither => standard' => [false, false, ProductType::TYPE_STANDARD];
    }
}

/**
 * Lightweight seam over the legacy Product object: stubs the DB-backed hasCombinations()
 * so getDynamicProductType() can be exercised in isolation. id stays null so Pack::isPack()
 * short-circuits to false without touching the database.
 */
class DynamicTypeProductStub extends Product
{
    public bool $stubHasCombinations = false;

    public function __construct()
    {
        // Intentionally skip the parent constructor to avoid DB access.
        $this->id = null;
    }

    public function hasCombinations()
    {
        return $this->stubHasCombinations;
    }
}
