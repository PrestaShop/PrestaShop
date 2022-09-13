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

namespace Tests\Unit\Core\Theme;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Theme\ConfigReader;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;

class ConfigReaderTest extends TestCase
{
    /**
     * @var ConfigReader
     */
    protected $config;

    protected function setUp(): void
    {
        $this->config = new ConfigReader(__DIR__ . '/../../Resources/themes/');
    }

    public function testReadUnknownTheme(): void
    {
        $this->assertNull(
            $this->config->read('this-is-sparta-theme')
        );
    }

    public function testRead(): void
    {
        $theme = $this->config->read('my-theme');
        $this->assertInstanceOf(
            ArrayFinder::class,
            $theme
        );
        $this->assertEquals('themes/preview-fallback.png', $theme->get('preview'));
        $this->assertEquals('My super aweosome theme', $theme->get('display_name'));
    }

    public function testReadWithPreview(): void
    {
        $theme = $this->config->read('my-theme-with-preview');
        $this->assertInstanceOf(
            ArrayFinder::class,
            $theme
        );
        $this->assertArrayHasKey('preview', $theme);
    }
}
