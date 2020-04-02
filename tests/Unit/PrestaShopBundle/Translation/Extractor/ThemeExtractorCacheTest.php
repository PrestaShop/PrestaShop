<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Translation\Extractor;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorCache;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeExtractorCacheTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    /**
     * @var Filesystem
     */
    private static $filesystem;

    public static function setUpBeforeClass()
    {
        self::$filesystem = new Filesystem();
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ThemeExtractorCacheTest']);
    }

    public function setUp()
    {
        self::$filesystem->mkdir(self::$tempDir);
    }

    /**
     * Test that the catalogue cache extractor stores results in cache,
     * that it writes data to cache as it should,
     * and that the cache is read upon a second call without re-extracting
     */
    public function testItUsesCatalogueFromCache()
    {
        $catalogue = new MessageCatalogue(ThemeExtractorInterface::DEFAULT_LOCALE);
        $wordings = [
            'ShopSomeDomain' => [
                'Some wording' => 'Some wording',
                'Some other wording' => 'Some other wording',
            ],
            'ShopSomethingElse' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
            ],
        ];
        foreach ($wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }

        // the theme extractor should be called exactly once,
        // subsequent extractions should be performed from cache
        $mockThemeExtractor = $this->createMock(ThemeExtractor::class);
        $mockThemeExtractor->expects($this->once())
            ->method('extract')
            ->willReturn($catalogue);

        $mockTheme = $this->getMockTheme('mocktheme');

        $cacheExtractor = new ThemeExtractorCache($mockThemeExtractor, self::$tempDir);

        // first extraction, cache is empty, it calls the mocked method
        $extracted = $cacheExtractor->extract($mockTheme);

        $this->assertCatalogueHasMessages($extracted, $wordings);

        $this->assertDomainFilesExistInCache(array_keys($wordings), $cacheExtractor->getCachedFilesPath($mockTheme));

        // second extraction, cache is fresh, it doesn't call the mocked method
        $extracted = $cacheExtractor->extract($mockTheme);

        $this->assertCatalogueHasMessages($extracted, $wordings);
    }

    public function tearDown()
    {
        self::$filesystem->remove(self::$tempDir);
    }

    private function getMockTheme($themeName)
    {
        $mock = $this->createMock(Theme::class);
        $mock->method('getName')->willReturn($themeName);

        return $mock;
    }

    /**
     * @param MessageCatalogue $catalogue
     * @param array[] $expectedMessages
     */
    private function assertCatalogueHasMessages(MessageCatalogue $catalogue, array $expectedMessages)
    {
        foreach ($expectedMessages as $domain => $messages) {
            foreach ($messages as $message => $value) {
                $this->assertSame(
                    $value,
                    $catalogue->get($message, $domain),
                    sprintf(
                        'Invalid message in catalogue for "%s" in domain "%s"',
                        $message,
                        $domain
                    )
                );
            }
        }
    }

    /**
     * @param string[] $domains
     */
    private function assertDomainFilesExistInCache(array $domains, $directory)
    {
        $files = (new Finder())->files('*.xlf')->in($directory);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // store file names without the extension
            $fileDomains[] = substr($file->getFilename(), 0, -4);
        }

        sort($fileDomains);
        sort($domains);
        $this->assertSame($domains, $fileDomains, 'List of domains is not equal');
    }
}
