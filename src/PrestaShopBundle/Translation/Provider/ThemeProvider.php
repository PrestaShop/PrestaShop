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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Provides translations from a theme to the translation interface
 */
class ThemeProvider implements ProviderInterface
{
    /**
     * @var string Path to the main "themes" directory
     */
    private $themeResourcesDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var ThemeExtractorInterface
     */
    private $themeExtractor;

    /**
     * @var ProviderInterface
     */
    private $frontOfficeProvider;
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @param ProviderInterface $frontOfficeProvider Provider for core front office translations
     * @param DatabaseTranslationLoader $databaseLoader
     * @param ThemeExtractorInterface $themeExtractor
     * @param ThemeRepository $themeRepository
     * @param Filesystem $filesystem
     * @param string $themeResourcesDir Path to the themes folder
     */
    public function __construct(
        ProviderInterface $frontOfficeProvider,
        DatabaseTranslationLoader $databaseLoader,
        ThemeExtractorInterface $themeExtractor,
        ThemeRepository $themeRepository,
        Filesystem $filesystem,
        $themeResourcesDir
    ) {
        $this->frontOfficeProvider = $frontOfficeProvider;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themeResourcesDirectory = $themeResourcesDir;
        $this->databaseLoader = $databaseLoader;
    }

    private function validateTheme(string $themeName)
    {
        $theme = $this->getThemeFromName($themeName);

        if (!($theme instanceof Theme)) {
            throw new \RuntimeException('Theme doesnt exist');
        }
    }

    private function getThemeFromName(string $themeName)
    {
        return $this->themeRepository->getInstanceByName($themeName);
    }

    /**
     * Returns the default (aka not translated) catalogue
     *
     * @param string $locale
     * @param string $themeName
     * @param bool $empty [default=true] Remove translations and return an empty catalogue
     * @param bool $refreshCache [default=false] Force cache to be refreshed
     *
     * @return MessageCatalogueInterface
     */
    public function getDefaultCatalogue(
        string $locale,
        string $themeName,
        bool $empty = true,
        $refreshCache = false
    ): MessageCatalogueInterface {
        $theme = $this->getThemeFromName($themeName);
        if (!($theme instanceof Theme)) {
            throw new \RuntimeException('Theme doesnt exist');
        }

        $defaultCatalogue = $this->frontOfficeProvider->getDefaultCatalogue($locale);

        $defaultCatalogue->addCatalogue(
            $this->extractDefaultCatalogueFromTheme(
                $theme,
                $locale,
                $refreshCache
            )
        );

        if ($empty) {
            $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileTranslatedCatalogue(string $locale, string $themeName): MessageCatalogueInterface
    {
        $this->validateTheme($themeName);

        // load front office catalogue
        $catalogue = $this->frontOfficeProvider->getFileTranslatedCatalogue($locale);

        try {
            $fileTranslatedCatalogue = (new FileTranslatedCatalogueProvider(
                $this->getResourceDirectory($themeName),
                ['*']
            ))
                ->getCatalogue($locale);

            // overwrite with the theme's own catalogue
            $catalogue->addCatalogue($fileTranslatedCatalogue);
        } catch (FileNotFoundException $e) {
            // there are no translation files, ignore them
            return new MessageCatalogue($locale);
        }

        return $catalogue;
    }

    /**
     * @param string $locale
     * @param string|null $themeName
     * @param string|null $domain
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(
        string $locale,
        string $themeName,
        ?string $domain = null
    ): MessageCatalogueInterface {
        $this->validateTheme($themeName);

        $translationDomains = ['*'];
        if (!empty($domain)) {
            $translationDomains = ['^' . $domain];
        }

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale, $themeName);
    }

    /**
     * @param string $themeName
     *
     * @return string
     */
    private function getResourceDirectory(string $themeName): string
    {
        $resourceDirectory = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->themeResourcesDirectory, DIRECTORY_SEPARATOR),
            $themeName,
            'translations',
        ]);
        $this->filesystem->mkdir($resourceDirectory);

        return $resourceDirectory;
    }

    /**
     * Extracts wordings from the theme's templates
     *
     * @param Theme $theme
     * @param string $locale
     * @param bool $refreshCache Indicates if extraction cache should be refreshed
     *
     * @return MessageCatalogue
     */
    private function extractDefaultCatalogueFromTheme(
        Theme $theme,
        string $locale,
        bool $refreshCache
    ): MessageCatalogue {
        return $this->themeExtractor->extract($theme, $locale, $refreshCache);
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    private function emptyCatalogue(MessageCatalogueInterface $messageCatalogue): MessageCatalogueInterface
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}
