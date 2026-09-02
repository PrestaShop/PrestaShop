<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Locator;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\Locator\ModuleTemplateLoader;

class ModuleTemplateLoaderTest extends TestCase
{
    /**
     * @var ModuleTemplateLoader|null
     */
    private $loader;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $namespaces = [
            'AdvancedParameters' => 'Admin/Configure/AdvancedParameters',
            'ShopParameters' => 'Admin/Configure/ShopParameters',
            'PrestaShop' => '',
        ];

        $paths = [
            dirname(__DIR__, 3) . '/Resources/twig/module1',
            dirname(__DIR__, 3) . '/Resources/twig/module2',
            dirname(__DIR__, 3) . '/Resources/twig/module3',
        ];

        $this->loader = new ModuleTemplateLoader($namespaces, $paths);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->loader = null;
    }

    public function testGetPaths(): void
    {
        $this->assertCount(
            2,
            $this->loader->getPaths('ShopParameters'),
            'Two templates for the namespace "ShopParameters" should be found.'
        );

        $this->assertCount(
            3,
            $this->loader->getPaths('PrestaShop'),
            'One templates should be found.'
        );
    }

    /**
     * @dataProvider getSourceContextsProvider
     *
     * @param string $sourceContent the template file content
     * @param string $twigPathAsked the Twig path asked during Twig template rendering
     * @param string $successMessage in case of failure, describe what is expected
     */
    public function testGetSourceContext(string $sourceContent, string $twigPathAsked, string $successMessage): void
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
    public function getSourceContextsProvider(): array
    {
        return [
            ['module1', '@ShopParameters/test.html.twig', 'Module 1 wins as Module 3 is loaded after.'],
            ['module1', '@PrestaShop/Admin/Configure/ShopParameters/test.html.twig', 'PrestaShop is the main namespace.'],
            ['List from module 3', '@AdvancedParameters/list.html.twig', 'Module 3 templates are available.'],
            ['module2', '@PrestaShop/test.html.twig', 'Module 2 templates are availables.'],
        ];
    }

    public function testEmptyConstructor(): void
    {
        $loader = new ModuleTemplateLoader([]);

        $this->assertEquals([], $loader->getPaths());
    }

    public function testGetNamespaces(): void
    {
        $loader = new ModuleTemplateLoader([]);

        $this->assertEquals([], $loader->getNamespaces());

        $loader->addPath(sys_get_temp_dir(), 'named');

        $this->assertEquals(['named'], $loader->getNamespaces());
    }
}
