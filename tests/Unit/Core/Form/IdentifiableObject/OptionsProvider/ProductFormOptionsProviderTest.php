<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\OptionsProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\ProductFormOptionsProvider;

class ProductFormOptionsProviderTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const VIRTUAL_PRODUCT_FILE_ID = 51;

    public function testGetDefaultOptions(): void
    {
        $provider = new ProductFormOptionsProvider();
        $defaultOptions = $provider->getDefaultOptions([]);
        $this->assertEquals([], $defaultOptions);
    }

    public function testVirtualProductOption(): void
    {
        $provider = new ProductFormOptionsProvider();
        $options = $provider->getOptions(self::PRODUCT_ID, []);
        $this->assertArrayHasKey('virtual_product_file_id', $options);
        $this->assertEquals(null, $options['virtual_product_file_id']);

        $options = $provider->getOptions(self::PRODUCT_ID, [
            'stock' => [
                'virtual_product_file' => [
                    'virtual_product_file_id' => self::VIRTUAL_PRODUCT_FILE_ID,
                ],
            ],
        ]);
        $this->assertArrayHasKey('virtual_product_file_id', $options);
        $this->assertEquals(self::VIRTUAL_PRODUCT_FILE_ID, $options['virtual_product_file_id']);
    }

    public function testActiveOptions(): void
    {
        $provider = new ProductFormOptionsProvider();
        $options = $provider->getOptions(self::PRODUCT_ID, []);
        $this->assertArrayHasKey('active', $options);
        $this->assertFalse($options['active']);

        $options = $provider->getOptions(self::PRODUCT_ID, [
            'header' => [
                'active' => true,
            ],
        ]);
        $this->assertArrayHasKey('active', $options);
        $this->assertTrue($options['active']);
    }

    public function testProductTypeOption(): void
    {
        $provider = new ProductFormOptionsProvider();
        $options = $provider->getOptions(self::PRODUCT_ID, []);
        $this->assertArrayHasKey('product_type', $options);
        $this->assertEquals(ProductType::TYPE_STANDARD, $options['product_type']);

        $options = $provider->getOptions(self::PRODUCT_ID, [
            'header' => [
                'type' => ProductType::TYPE_COMBINATIONS,
            ],
        ]);
        $this->assertArrayHasKey('product_type', $options);
        $this->assertEquals(ProductType::TYPE_COMBINATIONS, $options['product_type']);
    }
}
