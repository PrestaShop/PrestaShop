<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
