<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
