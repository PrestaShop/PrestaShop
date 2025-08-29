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
