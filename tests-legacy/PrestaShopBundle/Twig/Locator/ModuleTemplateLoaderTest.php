<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\PrestaShopBundle\Twig\Locator;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\Locator\ModuleTemplateLoader;

/**
 * @group sf
 */
class ModuleTemplateLoaderTest extends TestCase
{
    /**
     * @var ModuleTemplateLoader
     */
    private $loader;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $namespaces = [
            'Product' => 'Admin/Product',
            'PrestaShop' => '',
        ];

        $paths = [
            __DIR__.'/../Fixtures/module1',
            __DIR__.'/../Fixtures/module2',
            __DIR__.'/../Fixtures/module3',
        ];

        $this->loader = new ModuleTemplateLoader($namespaces, $paths);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->loader = null;
    }

    public function testGetPaths()
    {
        $this->assertCount(
            2,
            $this->loader->getPaths('Product'),
            'Two templates for the namespace "Product" should be found.'
        );

        $this->assertCount(
            3,
            $this->loader->getPaths('PrestaShop'),
            'One templates should be found.'
        );
    }

    /**
     * @dataProvider getSourceContextsProvider
     * @param string $sourceContent The template file content.
     * @param string $twigPathAsked The Twig path asked during Twig template rendering.
     * @param string $successMessage In case of failure, describe what is expected.
     */
    public function testGetSourceContext($sourceContent, $twigPathAsked, $successMessage)
    {
        $this->assertEquals(
            $sourceContent . PHP_EOL,
            $this->loader->getSourceContext($twigPathAsked)->getCode(),
            $successMessage
        );
    }

    /**
     * @return array
     */
    public function getSourceContextsProvider()
    {
        return [
            ['module1', '@Product/test.html.twig', 'Module 1 wins as Module 3 is loaded after.'],
            ['module1', '@PrestaShop/Admin/Product/test.html.twig', 'PrestaShop is the main namespace.'],
            ['List from module 3', '@Product/ProductPage/Lists/list.html.twig', 'Module 3 templates are available.'],
            ['module2', '@PrestaShop/test.html.twig', 'Module 2 templates are availables.'],
        ];
    }

    public function testEmptyConstructor()
    {
        $loader = new ModuleTemplateLoader([]);

        $this->assertEquals(array(), $loader->getPaths());
    }

    /**
     * @throws \Twig_Error_Loader
     */
    public function testGetNamespaces()
    {
        $loader = new ModuleTemplateLoader([]);

        $this->assertEquals([], $loader->getNamespaces());

        $loader->addPath(sys_get_temp_dir(), 'named');

        $this->assertEquals(['named'], $loader->getNamespaces());
    }
}
