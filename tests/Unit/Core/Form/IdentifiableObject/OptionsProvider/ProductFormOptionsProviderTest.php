<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
