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

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeProvider implements ProviderInterface
{
    public const DEFAULT_LOCALE = 'en-US';

    /**
     * @var string the theme name
     */
    private $themeName;

    /**
     * @var string the theme resources directory
     */
    public $themeResourcesDirectory;

    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var ThemeRepository
     */
    public $themeRepository;

    /**
     * @var ThemeExtractor
     */
    public $themeExtractor;

    /**
     * @var string Path to app/Resources/translations/
     */
    public $defaultTranslationDir;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string the resource directory
     */
    protected $resourceDirectory;

    /**
     * @var string the Catalogue locale
     */
    protected $locale;

    /**
     * @var string the Catalogue domain
     */
    protected $domain;

    public function __construct(DatabaseTranslationLoader $databaseLoader, $resourceDirectory)
    {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        if (empty($this->domain)) {
            return ['*'];
        }

        return ['#^' . $this->domain . '#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        if (empty($this->domain)) {
            return ['*'];
        }

        return ['^' . $this->domain];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @TODO: Don't know why default catalogue was not taken
     */
    public function getMessageCatalogue(): MessageCatalogue
    {
        $xlfCatalogue = $this->getXliffCatalogue();
        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $xlfCatalogue->addCatalogue($databaseCatalogue);

        return $xlfCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogue
    {
        return (new DefaultCatalogueProvider(
            [$this->defaultTranslationDir . DIRECTORY_SEPARATOR . $this->locale],
            $this->getFilters()
        ))
            ->getCatalogue($this->locale, $empty);
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getXliffCatalogue(): MessageCatalogue
    {
        return (new XliffCatalogueProvider(
                    [
                        $this->getResourceDirectory(),
                        $this->getResourceDirectory($this->themeResourcesDirectory),
                    ],
                    $this->getFilters()
                ))
            ->getCatalogue($this->locale);
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param string|null $themeName
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue(string $themeName = null): MessageCatalogue
    {
        if (null === $themeName) {
            $themeName = $this->themeName;
        }

        return (new DatabaseCatalogueProvider($this->databaseLoader, $this->getTranslationDomains()))
            ->getCatalogue($this->locale, $themeName);
    }

    /**
     * @param string|null $baseDir
     *
     * @return string Path to app/themes/{themeName}/translations/{locale}
     */
    public function getResourceDirectory($baseDir = null): string
    {
        if (null === $baseDir) {
            $baseDir = $this->resourceDirectory;
        }

        $resourceDirectory = $baseDir . '/' . $this->themeName . '/translations/' . $this->getLocale();
        $this->filesystem->mkdir($resourceDirectory);

        return $resourceDirectory;
    }

    /**
     * Get domain.
     *
     * @deprecated since 1.7.6, to be removed in the next major
     *
     * @return string
     */
    public function getDomain(): string
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
    public function getIdentifier(): string
    {
        return 'theme';
    }

    /**
     * @param string $themeName The theme name
     *
     * @return self
     */
    public function setThemeName(string $themeName): self
    {
        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @throws \Exception
     *
     * Will update translations files of the Theme
     */
    public function synchronizeTheme(): void
    {
        $theme = $this->themeRepository->getInstanceByName($this->themeName);

        $path = $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->themeName . DIRECTORY_SEPARATOR . 'translations';

        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);

        $this->themeExtractor
            ->setOutputPath($path)
            ->setThemeProvider($this)
            ->extract($theme, $this->locale);

        $translationFilesPath = $path . DIRECTORY_SEPARATOR . $this->locale;
        Flattenizer::flatten($translationFilesPath, $translationFilesPath, $this->locale, false);

        $finder = Finder::create();
        foreach ($finder->directories()->depth('== 0')->in($translationFilesPath) as $folder) {
            $this->filesystem->remove($folder);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function getThemeCatalogue(): MessageCatalogue
    {
        $path = $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->themeName . DIRECTORY_SEPARATOR . 'translations';

        return (new TranslationFinder())->getCatalogueFromPaths([$path], $this->locale, current($this->getFilters()));
    }
}
