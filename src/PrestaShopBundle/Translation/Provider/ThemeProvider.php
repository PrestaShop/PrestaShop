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

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class ThemeProvider implements ProviderInterface
{
    const DEFAULT_LOCALE = 'en-US';

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
    public function getDirectories()
    {
        return [
            $this->getResourceDirectory(),
            $this->getThemeResourcesDirectory(),
        ];
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
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get the PrestaShop locale from real locale.
     *
     * @return string The PrestaShop locale
     *
     * @deprecated since 1.7.6, to be removed in the next major
     */
    public function getPrestaShopLocale()
    {
        @trigger_error(
            '`ThemeProvider::getPrestaShopLocale` function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return Converter::toPrestaShopLocale($this->locale);
    }

    /**
     * {@inheritdoc}
     *
     * @TODO: Don't know why default catalogue was not taken
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
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
    public function getDefaultCatalogue(bool $empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                [$this->getDefaultResourceDirectory()],
                $this->locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty && $this->locale !== self::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getXliffCatalogue()
    {
        $xlfCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                $this->getDirectories(),
                $this->locale,
                $filter
            );
            $xlfCatalogue->addCatalogue($filteredCatalogue);
        }

        return $xlfCatalogue;
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param null $theme
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue($theme = null)
    {
        if (null === $theme) {
            $theme = $this->themeName;
        }

        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            if (!($this->getDatabaseLoader() instanceof DatabaseTranslationLoader)) {
                continue;
            }
            $domainCatalogue = $this->getDatabaseLoader()->load($this->locale, $translationDomain, $theme);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * @param string|null $baseDir
     *
     * @return string Path to app/themes/{themeName}/translations/{locale}
     */
    public function getResourceDirectory($baseDir = null)
    {
        if (null === $baseDir) {
            $baseDir = $this->resourceDirectory;
        }

        $resourceDirectory = $baseDir . '/' . $this->themeName . '/translations/' . $this->getLocale();
        $this->filesystem->mkdir($resourceDirectory);

        return $resourceDirectory;
    }

    /**
     * @return DatabaseTranslationLoader
     */
    public function getDatabaseLoader()
    {
        return $this->databaseLoader;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogueInterface $messageCatalogue)
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }

    /**
     * @param array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths($paths, $locale, $pattern = null)
    {
        return (new TranslationFinder())->getCatalogueFromPaths($paths, $locale, $pattern);
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
    public function getIdentifier(): string
    {
        return 'theme';
    }

    /**
     * @return string the path to the Theme translations folder
     */
    public function getThemeResourcesDirectory()
    {
        return $this->getResourceDirectory($this->themeResourcesDirectory);
    }

    /**
     * @param string $themeName The theme name
     *
     * @return self
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @throws \Exception
     *
     * Will update translations files of the Theme
     */
    public function synchronizeTheme()
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
     * @return MessageCatalogueInterface
     *
     * @throws \Exception
     */
    public function getThemeCatalogue()
    {
        $path = $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->themeName . DIRECTORY_SEPARATOR . 'translations';

        return $this->getCatalogueFromPaths([$path], $this->locale, current($this->getFilters()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->defaultTranslationDir . DIRECTORY_SEPARATOR . $this->locale;
    }
}
