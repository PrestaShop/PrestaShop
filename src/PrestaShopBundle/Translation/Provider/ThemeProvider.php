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
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorCache;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * Provides translations from a theme to the translation interface
 */
class ThemeProvider extends AbstractProvider
{
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
     * @var Theme
     */
    private $theme;

    /**
     * @var ProviderInterface
     */
    private $frontOfficeProvider;

    /**
     * @param ProviderInterface $frontOfficeProvider Provider for core front office translations
     * @param LoaderInterface $databaseLoader
     * @param ThemeExtractorInterface $themeExtractor
     * @param ThemeRepository $themeRepository
     * @param Filesystem $filesystem
     * @param string $themeResourcesDir Path to the themes folder
     */
    public function __construct(
        ProviderInterface $frontOfficeProvider,
        LoaderInterface $databaseLoader,
        ThemeExtractorInterface $themeExtractor,
        ThemeRepository $themeRepository,
        Filesystem $filesystem,
        $themeResourcesDir
    ) {
        // resourceDirectory cannot be set because it depends on the theme, which is not set yet
        // DO NOT USE $this->resourceDirectory, use $this->getResourceDirectory() instead
        parent::__construct($databaseLoader, $resourceDirectory = '');

        $this->frontOfficeProvider = $frontOfficeProvider;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themeResourcesDirectory = $themeResourcesDir;
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
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        if (empty($this->domain)) {
            return ['*'];
        }

        return ['^' . $this->domain];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        if (empty($this->domain)) {
            return ['*'];
        }

        return ['#^' . $this->domain . '#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'theme';
    }

    /**
     * Returns the path to translations directory for the current theme in the current locale
     *
     * @param string|null $baseDir Base directory for the path. If not provided, it defaults to $this->resourceDirectory
     *
     * @return string Path to $baseDir/{themeName}/translations/{locale}
     */
    public function getResourceDirectory($baseDir = null)
    {
        if (null === $baseDir) {
            $baseDir = $this->themeResourcesDirectory;
        }

        $resourceDirectory = implode(
            DIRECTORY_SEPARATOR,
            [$baseDir, $this->getThemeName(), 'translations', $this->getLocale()]
        );

        return $resourceDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        return [
            $this->getResourceDirectory(),
        ];
    }

    /**
     * @param string $themeName The theme name
     *
     * @return self
     */
    public function setThemeName($themeName)
    {
        // make sure the theme exists and store it cache
        $this->theme = $this->themeRepository->getInstanceByName($themeName);

        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLocale($locale)
    {
        parent::setLocale($locale);

        $this->frontOfficeProvider->setLocale($locale);

        return $this;
    }

    /**
     * Returns the default (aka not translated) catalogue
     *
     * @param bool $empty [default=true] Remove translations and return an empty catalogue
     * @param bool $refreshCache [default=false] Force cache to be refreshed
     *
     * @return MessageCatalogue|MessageCatalogueInterface
     */
    public function getDefaultCatalogue($empty = true, $refreshCache = false)
    {
        $defaultCatalogue = $this->frontOfficeProvider->getDefaultCatalogue();

        $defaultCatalogue->addCatalogue(
            $this->extractDefaultCatalogueFromTheme(
                $this->getTheme(),
                $this->getLocale(),
                $refreshCache
            )
        );


        if ($empty) {
            $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        if (null === $themeName) {
            $themeName = $this->getThemeName();
        }

        return parent::getDatabaseCatalogue($themeName);
    }

    /**
     * Refresh the default catalogue cache
     */
    public function synchronizeTheme()
    {
        $this->extractDefaultCatalogueFromTheme($this->getTheme(), $this->getLocale(), true);
    }

    /**
     * Returns the catalogue from the Xliff files located within the theme itself
     *
     * @deprecated Since 1.7.6.5, use self::getXliffCatalogue instead
     *
     * @return MessageCatalogueInterface
     */
    public function getThemeCatalogue()
    {
        @trigger_error(__FUNCTION__ . 'is deprecated since version 1.7.6.5 Use ThemeProvider::getXliffCatalogue() instead.', E_USER_DEPRECATED);

        return $this->getXliffCatalogue();
    }

    /**
     * @inheritDoc
     */
    public function getXliffCatalogue()
    {
        // load front office catalogue
        $xliffCatalogue = $this->frontOfficeProvider->getXliffCatalogue();

        // overwrite with the theme's own catalogue
        $xliffCatalogue->addCatalogue(parent::getXliffCatalogue());

        return $xliffCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        if (!$this->themeExtractor instanceof ThemeExtractorCache) {
            throw new \LogicException(
                'This theme provider has not been configured with a cache extractor, so there is no directory for default resources'
            );
        }

        return $this->themeExtractor->getCachedFilesPath($this->getTheme());
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    private function getThemeName()
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
    private function getTheme()
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
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    private function extractDefaultCatalogueFromTheme(Theme $theme, $locale, $refreshCache)
    {
        return $this->themeExtractor->extract($theme, $locale, $refreshCache);
    }
}
