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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Converter\RoutingCacheKeyGenerator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RoutingCacheKeyGeneratorTest.
 */
class RoutingCacheKeyGeneratorTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;
    private $filesTestDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->filesTestDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'routing';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanTestDir();
    }

    public function testCoreFilesOrder()
    {
        $testFiles = [
            'admin/sell/catalog/products/products.yml' => 10,
            'admin/sell/catalog/products/categories.yaml' => 5,
            'admin/configure/shop_parameters/product_preferences.config' => 2,
            'admin/configure/shop_parameters/product_preferences.yml' => -3,
            'admin/configure/advanced_parameters/webservice.yml' => 8,
            'admin/improve/payment/payment_methods.yaml' => 0,
            'admin/configure/advanced_parameters/README.md' => 10,
            'admin/improve/international/translations.yml' => 3200,
            'api/stock_movements.yml' => 10,
            'api/features.yaml' => 5,
        ];
        $this->generateFiles($testFiles);

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], []);
        $lastModifications = $generator->getLastModifications();
        $this->assertCount(6, $lastModifications);
        $this->assertSame([
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/international/translations.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/products.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/configure/advanced_parameters/webservice.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/categories.yaml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/payment/payment_methods.yaml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/configure/shop_parameters/product_preferences.yml',
        ], array_keys($lastModifications));

        $this->fs->touch($this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/payment/payment_methods.yaml');

        $lastModifications = $generator->getLastModifications();
        $this->assertCount(6, $lastModifications);
        $this->assertSame([
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/payment/payment_methods.yaml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/international/translations.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/products.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/configure/advanced_parameters/webservice.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/categories.yaml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/configure/shop_parameters/product_preferences.yml',
        ], array_keys($lastModifications));
    }

    public function testLatestCoreFile()
    {
        $originalTime = time();
        $testFiles = [
            'admin/sell/catalog/products/products.yml' => 10,
            'admin/sell/catalog/products/categories.yaml' => 5,
            'admin/configure/shop_parameters/product_preferences.config' => 2,
            'admin/configure/shop_parameters/product_preferences.yml' => -3,
            'admin/configure/advanced_parameters/webservice.yml' => 10,
            'admin/improve/payment/payment_methods.yaml' => 0,
            'admin/configure/advanced_parameters/README.md' => 10,
            'admin/improve/international/translations.yml' => 3200,
            'api/stock_movements.yml' => 10,
            'api/features.yaml' => 5,
        ];
        $this->generateFiles($testFiles, $originalTime);

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], []);
        $this->assertEquals($originalTime + 3200, $generator->getLatestModificationTime());
    }

    public function testCacheKeyCoreFile()
    {
        $originalTime = time();
        $testFiles = [
            'admin/sell/catalog/products/products.yml' => 10,
            'admin/sell/catalog/products/categories.yaml' => 5,
            'admin/configure/shop_parameters/product_preferences.config' => 2,
            'admin/configure/shop_parameters/product_preferences.yml' => -3,
            'admin/configure/advanced_parameters/webservice.yml' => 10,
            'admin/improve/payment/payment_methods.yaml' => 0,
            'admin/configure/advanced_parameters/README.md' => 10,
            'admin/improve/international/translations.yml' => 3200,
            'api/stock_movements.yml' => 10,
            'api/features.yaml' => 5,
        ];
        $this->generateFiles($testFiles, $originalTime);

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], []);
        $cacheKey = $generator->getCacheKey();
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . ($originalTime + 3200), $cacheKey);
    }

    public function testModuleFilesOrder()
    {
        $testFiles = [
            'modules/ps_linklist/config/routes.yml' => 42,
            'modules/ps_featuredproducts/config/routes.yaml' => -20,
            'modules/ps_viewedproducs/config/routes.yml' => 10,
            'modules/ps_gamification/config/routes.yml' => 0,
        ];
        $this->generateFiles($testFiles);
        $modules = [
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification',
        ];

        $generator = new RoutingCacheKeyGenerator([], $modules);
        $lastModifications = $generator->getLastModifications();
        $this->assertCount(4, $lastModifications);
        $this->assertSame([
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts/config/routes.yaml',
        ], array_keys($lastModifications));

        $this->fs->touch($this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification/config/routes.yml');

        $lastModifications = $generator->getLastModifications();
        $this->assertCount(4, $lastModifications);
        $this->assertSame([
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts/config/routes.yaml',
        ], array_keys($lastModifications));
    }

    public function testLatestModulesFile()
    {
        $originalTime = time();
        $testFiles = [
            'modules/ps_linklist/config/routes.yml' => 42,
            'modules/ps_featuredproducts/config/routes.yaml' => -20,
            'modules/ps_viewedproducs/config/routes.yml' => 10,
            'modules/ps_gamification/config/routes.yml' => 0,
        ];
        $this->generateFiles($testFiles, $originalTime);
        $modules = [
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification',
        ];

        $generator = new RoutingCacheKeyGenerator([], $modules);
        $this->assertEquals($originalTime + 42, $generator->getLatestModificationTime());
    }

    public function testCacheKeyModulesFile()
    {
        $originalTime = time();
        $testFiles = [
            'modules/ps_linklist/config/routes.yml' => 42,
            'modules/ps_featuredproducts/config/routes.yaml' => -20,
            'modules/ps_viewedproducs/config/routes.yml' => 10,
            'modules/ps_gamification/config/routes.yml' => 0,
        ];
        $this->generateFiles($testFiles, $originalTime);
        $modules = [
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification',
        ];

        $generator = new RoutingCacheKeyGenerator([], $modules);
        $cacheKey = $generator->getCacheKey();
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . ($originalTime + 42), $cacheKey);
    }

    public function testCoreAndModules()
    {
        $originalTime = time() - 3600;
        $testFiles = [
            'admin/sell/catalog/products/products.yml' => 10,
            'admin/sell/catalog/products/categories.yaml' => 5,
            'admin/configure/shop_parameters/product_preferences.yml' => -3,
            'admin/improve/international/translations.yml' => 3200,
            'api/stock_movements.yml' => 10,
            'api/features.yaml' => 5,

            'modules/ps_linklist/config/routes.yml' => 42,
            'modules/ps_featuredproducts/config/routes.yaml' => -20,
            'modules/ps_viewedproducs/config/routes.yml' => 8,
            'modules/ps_gamification/config/routes.yml' => 0,
        ];
        $this->generateFiles($testFiles, $originalTime);
        $modules = [
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification',
        ];

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], $modules);
        $lastModifications = $generator->getLastModifications();
        $this->assertCount(8, $lastModifications);
        $this->assertSame([
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/international/translations.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/products.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/sell/catalog/products/categories.yaml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification/config/routes.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/configure/shop_parameters/product_preferences.yml',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts/config/routes.yaml',
        ], array_keys($lastModifications));

        $this->assertEquals($originalTime + 3200, $generator->getLatestModificationTime());
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . ($originalTime + 3200), $generator->getCacheKey());

        $this->fs->touch($this->filesTestDir . DIRECTORY_SEPARATOR . 'admin/improve/international/translations.yml', $originalTime);

        $this->assertEquals($originalTime + 42, $generator->getLatestModificationTime());
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . ($originalTime + 42), $generator->getCacheKey());

        $now = time();
        $this->fs->touch($this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts/config/routes.yaml', $now);
        $this->assertEquals($now, $generator->getLatestModificationTime());
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . $now, $generator->getCacheKey());
    }

    public function testNoRouteFiles()
    {
        $generator = new RoutingCacheKeyGenerator([], []);
        $lastModifications = $generator->getLastModifications();
        $this->assertNotNull($lastModifications);
        $this->assertEmpty($lastModifications);

        $this->assertNull($generator->getLatestModificationTime());

        $cacheKey = $generator->getCacheKey();
        $this->assertEquals('PrestaShopBundle_Routing_Converter', $cacheKey);
    }

    public function testProdEnvironment()
    {
        $originalTime = time() - 3600;
        $testFiles = [
            'admin/sell/catalog/products/products.yml' => 10,
            'admin/sell/catalog/products/categories.yaml' => 5,
            'admin/configure/shop_parameters/product_preferences.yml' => -3,
            'admin/improve/international/translations.yml' => 3200,
            'api/stock_movements.yml' => 10,
            'api/features.yaml' => 5,

            'modules/ps_linklist/config/routes.yml' => 42,
            'modules/ps_featuredproducts/config/routes.yaml' => -20,
            'modules/ps_viewedproducs/config/routes.yml' => 10,
            'modules/ps_gamification/config/routes.yml' => 0,
        ];
        $this->generateFiles($testFiles, $originalTime);
        $modules = [
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_linklist',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_featuredproducts',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_viewedproducs',
            $this->filesTestDir . DIRECTORY_SEPARATOR . 'modules/ps_gamification',
        ];

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], $modules);
        $this->assertEquals('PrestaShopBundle_Routing_Converter_' . ($originalTime + 3200), $generator->getCacheKey());

        $generator = new RoutingCacheKeyGenerator([$this->filesTestDir . DIRECTORY_SEPARATOR . 'admin'], $modules, 'prod');
        $this->assertEquals('PrestaShopBundle_Routing_Converter', $generator->getCacheKey());
    }

    /**
     * @param array $fileOffsets
     * @param int|null $originalTime
     */
    private function generateFiles(array $fileOffsets, $originalTime = null)
    {
        if (null === $originalTime) {
            //By default original time one hour ago
            $originalTime = time() - 3600;
        }

        $this->cleanTestDir();
        if (!file_exists($this->filesTestDir)) {
            $this->fs->mkdir($this->filesTestDir);
        }

        foreach ($fileOffsets as $filePath => $fileOffset) {
            $filePath = $this->filesTestDir . DIRECTORY_SEPARATOR . $filePath;
            if (!file_exists(dirname($filePath))) {
                $this->fs->mkdir(dirname($filePath));
            }
            $this->fs->touch($filePath, $originalTime + $fileOffset);
        }
    }

    private function cleanTestDir()
    {
        $this->fs->remove($this->filesTestDir);
    }
}
