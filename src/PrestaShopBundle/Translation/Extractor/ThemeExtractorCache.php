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

namespace PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Cache adapter for theme translation extraction, stores extracted wordings in XLF files
 */
class ThemeExtractorCache implements ThemeExtractorInterface
{
    /**
     * @var ThemeExtractor
     */
    private $extractor;

    /**
     * @var string Path to /var/cache/{env}/themes/
     */
    private $cacheRootDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param ThemeExtractorInterface $extractor
     * @param string $themeCacheRootDirectory Path to /var/cache/{env}/themes/
     */
    public function __construct(ThemeExtractorInterface $extractor, $themeCacheRootDirectory)
    {
        $this->extractor = $extractor;
        $this->cacheRootDirectory = $themeCacheRootDirectory;
        $this->filesystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Theme $theme, $locale = self::DEFAULT_LOCALE, $forceRefresh = false)
    {
        if (!$forceRefresh && $this->cacheIsFresh($theme)) {
            return $this->loadFromCache($theme, $locale);
        }

        $catalogue = $this->extractor->extract($theme);
        $this->updateCache($theme, $catalogue);

        return $catalogue;
    }

    /**
     * @param Theme $theme
     *
     * @return bool
     */
    private function cacheIsFresh(Theme $theme)
    {
        // @todo: improve this method by fingerprinting files then refreshing cache only when they change

        $directory = $this->getCachedFilesPath($theme);

        return
            $this->filesystem->exists($directory)
            && 0 < $this->getCacheFiles($directory)->count()
        ;
    }

    /**
     * @param Theme $theme
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function loadFromCache(Theme $theme, $locale)
    {
        $cacheFiles = $this->getCacheFiles($this->getCachedFilesPath($theme));
        $xliffFileLoader = new XliffFileLoader();

        $catalogue = new MessageCatalogue($locale);

        /** @var SplFileInfo $file */
        foreach ($cacheFiles as $file) {
            $domain = explode('.', $file->getFilename(), 2)[0];
            $fileCatalogue = $xliffFileLoader->load($file->getPathname(), $locale, $domain);
            $catalogue->addCatalogue($fileCatalogue);
        }

        return $catalogue;
    }

    /**
     * Returns the path to the directory where default translations are stored in cache
     *
     * @param Theme $theme
     *
     * @return string
     */
    public function getCachedFilesPath(Theme $theme)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $this->cacheRootDirectory,
                $theme->getName(),
                'translations',
                'default',
            ]
        );
    }

    /**
     * @param Theme $theme
     * @param MessageCatalogue $catalogue
     */
    private function updateCache(Theme $theme, MessageCatalogue $catalogue)
    {
        $path = $this->getCachedFilesPath($theme);

        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);

        $options = [
            'path' => $path,
            'default_locale' => $catalogue->getLocale(),
        ];

        $dumper = new XliffFileDumper();

        $dumper->dump($catalogue, $options);
    }

    /**
     * Returns a finder with all the XLF files stored in cache
     *
     * @param string $directory
     *
     * @return Finder
     */
    private function getCacheFiles($directory)
    {
        return (new Finder())->files()->name('*.xlf')->in($directory);
    }
}
