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

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\ConfigReader;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;

class ConfigReaderTest extends TestCase
{
    /**
     * @var ConfigReader
     */
    protected $config;

    protected function setUp(): void
    {
        $this->config = new ConfigReader(__DIR__ . '/../../Resources/modules/');
    }

    public function testReadUnknownTheme(): void
    {
        $this->assertNull(
            $this->config->read('this-is-sparta-module', 'fr')
        );
    }

    public function testReadWithBrokenXml(): void
    {
        $module = $this->config->read('module-awesome', 'it');
        $this->assertNull(
            $module
        );
    }

    public function testReadWithIsoCode(): void
    {
        $module = $this->config->read('module-awesome', 'fr');
        $this->assertInstanceOf(
            ArrayFinder::class,
            $module
        );
    }

    public function testReadWithData(): void
    {
        $module = $this->config->read('module-awesome', 'en');
        $this->assertInstanceOf(
            ArrayFinder::class,
            $module
        );
        $this->assertEquals('Awesome module', $module->get('displayName'));
    }
}
