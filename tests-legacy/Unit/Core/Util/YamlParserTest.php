<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Util\File;

use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;

class YamlParserTest extends UnitTestCase
{
    public function getConfigDir()
    {
        return _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * @dataProvider getYamlFilesProvider
     */
    public function testParser($yamlFiles)
    {
        $yamlParser = new YamlParser(false);
        $cacheFile = $yamlParser->getCacheFile($yamlFiles);
        @unlink($cacheFile);

        // no cache file
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileNotExists($cacheFile);

        // create the cache file
        $yamlParser = new YamlParser(true);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $cacheTime = filemtime($cacheFile);

        // the source file hasn't changed, the cache file should be reused
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $this->assertEquals($cacheTime, filemtime($cacheFile));

        // if source yaml change, the cache should be refreshed
        sleep(1);
        touch($yamlFiles);
        $config = $yamlParser->parse($yamlFiles);
        $this->assertArrayHasKey('parameters', $config);
        $this->assertFileExists($cacheFile);
        $this->assertNotEquals($cacheTime, filemtime($cacheFile));
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
     * @return array
     */
    public function getYamlFilesProvider()
    {
        return array(array($this->getConfigDir() . DIRECTORY_SEPARATOR . 'config.yml'));
    }
}
