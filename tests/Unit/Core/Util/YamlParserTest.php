<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;

class YamlParserTest extends TestCase
{
    /**
     * @param string $yamlFiles
     *
     * @return string
     */
    private function clearCacheFile(string $yamlFiles): string
    {
        $yamlParser = new YamlParser($this->getCacheDir(), false);
        $cacheFile = $yamlParser->getCacheFile($yamlFiles);
        @unlink($cacheFile);

        return $cacheFile;
    }

    public function getConfigDir(): string
    {
        return _PS_ROOT_DIR_ . '/app/config/';
    }

    public function getCacheDir(): string
    {
        return _PS_ROOT_DIR_ . '/var/cache/test/';
    }

    /**
     * @dataProvider getYamlFilesProvider
     */
    public function testParserNoCache(string $yamlFiles): void
    {
        $cacheFile = $this->clearCacheFile($yamlFiles);
        $yamlParser = new YamlParser($this->getCacheDir(), false);

        // no cache file
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileDoesNotExist($cacheFile);
    }

    /**
     * @dataProvider getYamlFilesProvider
     */
    public function testParserCache(string $yamlFiles): void
    {
        $cacheFile = $this->clearCacheFile($yamlFiles);

        // create the cache file
        $yamlParser = new YamlParser($this->getCacheDir(), true);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $cacheTime = filemtime($cacheFile);

        sleep(1);
        // the source file hasn't changed, the cache file should be reused
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $this->assertEquals($cacheTime, filemtime($cacheFile));
    }

    /**
     * @dataProvider getYamlFilesProvider
     */
    public function testParserCacheRefreshedAfterChangingSourceFile(string $yamlFiles): void
    {
        $cacheFile = $this->clearCacheFile($yamlFiles);

        // create the cache file
        $yamlParser = new YamlParser($this->getCacheDir(), true);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $cacheTime = filemtime($cacheFile);

        sleep(1);

        // if source yaml change, the cache should be refreshed
        touch($yamlFiles, time() + 1);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $this->assertNotEquals($cacheTime, filemtime($cacheFile));
    }

    /**
     * @dataProvider getYamlFilesProvider
     */
    public function testParserCacheRefresh(string $yamlFiles): void
    {
        $cacheFile = $this->clearCacheFile($yamlFiles);

        // create the cache file
        $yamlParser = new YamlParser($this->getCacheDir(), true);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $cacheTime = filemtime($cacheFile);

        // if the forceRefresh flag is used, the cache should be refreshed
        sleep(1);
        $config = $yamlParser->parse($yamlFiles, true);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $this->assertNotEquals($cacheTime, filemtime($cacheFile));
    }

    /**
     * Data provider
     *
     * @return array<array<string>>
     */
    public function getYamlFilesProvider(): array
    {
        return [
            [$this->getConfigDir() . '/config_test.yml'],
        ];
    }
}
