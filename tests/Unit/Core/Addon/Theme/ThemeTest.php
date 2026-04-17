<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;

class ThemeTest extends TestCase
{
    /**
     * @var Theme
     */
    protected $theme;

    public function testGetThemeAttributes(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'bar' => 'preston',
                'directory' => 'a/',
            ],
            '',
            ''
        );

        $this->assertSame('preston', $theme->get('bar'));
        $this->assertSame('foo', $theme->get('name'));
        $this->assertSame('a/', $theme->get('directory'));

        $this->assertTrue($theme->has('bar'));
        $this->assertTrue($theme->has('name'));
        $this->assertTrue($theme->has('directory'));

        $this->assertSame('foo', $theme->getName());
        $this->assertSame('a/', $theme->getDirectory());
    }

    public function testGetAttributesFromThemeParent(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'parent' => 'fake-theme',
                'directory' => 'a/',
            ],
            sys_get_temp_dir() . '/ThemeTest',
            dirname(__DIR__, 4) . '/Resources/themes/'
        );

        $this->assertSame('For testing purposes', $theme->get('display_name'));
    }

    public function testGetPageLayouts(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'directory' => 'a/',
                'theme_settings' => ['layouts' => 'z'],
            ],
            '',
            ''
        );

        $this->assertSame('z', $theme->getPageLayouts());
    }

    public function testGetAvailableLayouts(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'directory' => 'a/',
                'meta' => ['available_layouts' => 'z'],
            ],
            '',
            ''
        );

        $this->assertSame('z', $theme->getAvailableLayouts());
    }

    public function testGetLayoutNameForPage(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'directory' => 'a/',
                'theme_settings' => [
                    'default_layout' => 'm',
                    'layouts' => ['homepage' => 'o', 'checkout_page' => 'p'], ],
            ],
            '',
            ''
        );

        $this->assertSame('o', $theme->getLayoutNameForPage('homepage'));
        $this->assertSame('p', $theme->getLayoutNameForPage('checkout_page'));
        $this->assertSame('m', $theme->getLayoutNameForPage('not_exist'));
    }

    public function testGetPageSpecificCss(): void
    {
        $theme = new Theme(
            [
                'name' => 'foo',
                'directory' => 'a/',
                'assets' => ['css' => [
                    'all' => [[
                        'id' => 'custom-lib-style',
                        'path' => 'assets/css/custom-lib.css',
                    ]],
                    'a' => [[
                        'id' => 'product-style',
                        'path' => 'assets/css/product.css',
                        'media' => 'all',
                        'priority' => 200,
                    ]],
                ],
                ],
            ],
            '',
            ''
        );

        $this->assertSame(
            [
                'css' => [
                    [
                        'id' => 'custom-lib-style',
                        'path' => 'assets/css/custom-lib.css',
                        'media' => 'all',
                        'priority' => 50,
                        'inline' => false,
                    ],
                    [
                        'id' => 'product-style',
                        'path' => 'assets/css/product.css',
                        'media' => 'all',
                        'priority' => 200,
                        'inline' => false,
                    ],
                ],
                'js' => [],
            ], $theme->getPageSpecificAssets('a'));
    }
}
