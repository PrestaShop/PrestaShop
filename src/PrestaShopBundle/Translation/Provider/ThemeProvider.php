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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
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
     * @var string Catalogue domain
     */
    protected $domain;

    /**
     * @var string locale
     */
    protected $locale;

    /**
     * @var Theme
     */
    private $theme;

    /**
     * @var string the theme name
     */
    private $themeName;

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

    /**
     * Get domain.
     *
     * @deprecated since 1.7.6, to be removed in the next major
     *
     * @return mixed
     */
    public function getDomain()
    {
        @trigger_error(
            'getDomain function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return $this->domain;
    }

    /**
     * @param string $themeName The theme name
     *
     * @return ThemeProvider
     */
    public function setThemeName($themeName): ThemeProvider
    {
        // make sure the theme exists and store it cache
        $this->theme = $this->themeRepository->getInstanceByName($themeName);

        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return ThemeProvider
     */
    public function setLocale(string $locale): ThemeProvider
    {
        $this->locale = $locale;

        $this->frontOfficeProvider->setLocale($locale);

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return ThemeProvider
     */
    public function setDomain(string $domain): ThemeProvider
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Returns the fully translated message catalogue
     *
     * @return MessageCatalogueInterface
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        // Merge catalogues

        $xlfCatalogue = $this->getFilesystemCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);
        unset($xlfCatalogue);

        $databaseCatalogue = $this->getUserTranslatedCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);
        unset($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * Returns the default (aka not translated) catalogue
     *
     * @param bool $empty [default=true] Remove translations and return an empty catalogue
     * @param bool $refreshCache [default=false] Force cache to be refreshed
     *
     * @return MessageCatalogueInterface
     */
    public function getDefaultCatalogue(bool $empty = true, $refreshCache = false): MessageCatalogueInterface
    {
        $defaultCatalogue = $this->frontOfficeProvider->getDefaultCatalogue();

        $defaultCatalogue->addCatalogue(
            $this->extractDefaultCatalogueFromTheme(
                $this->getTheme(),
                $this->locale,
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
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        // load front office catalogue
        $catalogue = $this->frontOfficeProvider->getFilesystemCatalogue();

        $fileTranslatedCatalogue = (new FileTranslatedCatalogueProvider(
            $this->locale,
            $this->themeResourcesDirectory,
            ['*']
        ))
            ->getCatalogue();

        // overwrite with the theme's own catalogue
        $catalogue->addCatalogue($fileTranslatedCatalogue);

        return $catalogue;
    }

    /**
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $themeName = null): MessageCatalogueInterface
    {
        $translationDomains = ['*'];
        if (!empty($this->domain)) {
            $translationDomains = ['^' . $this->domain];
        }

        if (null === $themeName) {
            $themeName = $this->getThemeName();
        }

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $this->locale,
            $translationDomains
        ))
            ->getCatalogue($themeName);
    }

    /**
     * Refresh the default catalogue cache
     */
    public function synchronizeTheme()
    {
        $this->extractDefaultCatalogueFromTheme($this->getTheme(), $this->locale, true);
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'theme';
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    private function getThemeName(): string
    {
        if (empty($this->themeName)) {
            throw new \RuntimeException('Theme has not been defined yet for this provider');
        }

        return $this->themeName;
    }

    /**
     * @return Theme
     *
     * @throws \RuntimeException
     */
    private function getTheme(): Theme
    {
        if (empty($this->theme)) {
            throw new \RuntimeException('Theme has not been defined yet for this provider');
        }

        return $this->theme;
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
    private function extractDefaultCatalogueFromTheme(Theme $theme, $locale, $refreshCache): MessageCatalogue
    {
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
