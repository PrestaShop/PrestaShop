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

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Translation\Exception\InvalidThemeException;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ThemeExtractor;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\FileTranslatedCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\UserTranslatedCatalogueFinder;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Returns the 3 layers of translation catalogues related to the Theme translations.
 * The default catalogue is extracted from Theme's templates
 * The file catalogue is extracted from Core's file (in any file starting with "Shop") and from theme directory themes/THEMENAME/translations
 * The user catalogue is stored in DB, domain starting with Shop and theme is equal to the desired theme.
 *
 * @see CatalogueLayersProviderInterface to understand the 3 layers.
 */
class ThemeCatalogueLayersProvider implements CatalogueLayersProviderInterface
{
    /**
     * We need a connection to DB to load user translated catalogue.
     *
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    /**
     * @var string
     */
    private $themeName;

    /**
     * @var CatalogueLayersProviderInterface
     */
    private $coreFrontProvider;

    /**
     * @var ThemeExtractor
     */
    private $themeExtractor;

    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $themeResourcesDir;

    /**
     * @var Theme
     */
    private $theme;

    /**
     * @param CatalogueLayersProviderInterface $coreFrontProvider
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param ThemeExtractor $themeExtractor
     * @param ThemeRepository $themeRepository
     * @param Filesystem $filesystem
     * @param string $themeResourcesDir
     * @param string $themeName
     */
    public function __construct(
        CatalogueLayersProviderInterface $coreFrontProvider,
        DatabaseTranslationLoader $databaseTranslationLoader,
        ThemeExtractor $themeExtractor,
        ThemeRepository $themeRepository,
        Filesystem $filesystem,
        string $themeResourcesDir,
        string $themeName
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->coreFrontProvider = $coreFrontProvider;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themeResourcesDir = $themeResourcesDir;
        $this->themeName = $themeName;

        $this->assertThemeIsValid();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue(
        string $locale,
        bool $refreshCache = false
    ): MessageCatalogue {
        // Extracts wordings from the theme's templates
        return $this->themeExtractor->extract($this->theme, $locale);
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogue
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue
    {
        // load front office catalogue
        $catalogue = $this->coreFrontProvider->getFileTranslatedCatalogue($locale);

        try {
            $fileTranslatedCatalogue = (new FileTranslatedCatalogueFinder(
                $this->getResourceDirectory(),
                ['*']
            ))
                ->getCatalogue($locale);

            // overwrite with the theme's own catalogue
            $catalogue->addCatalogue($fileTranslatedCatalogue);
        } catch (TranslationFilesNotFoundException $e) {
            // there are no translation files, ignore them
            return new MessageCatalogue($locale);
        }

        return $catalogue;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return (new UserTranslatedCatalogueFinder(
            $this->databaseTranslationLoader,
            ['*'],
            $this->themeName
        ))
            ->getCatalogue($locale);
    }

    /**
     * Check if theme is registered in DB and set class property
     */
    private function assertThemeIsValid(): void
    {
        try {
            $theme = $this->themeRepository->getInstanceByName($this->themeName);
            if (!$theme instanceof Theme) {
                throw new InvalidThemeException();
            }
            $this->theme = $theme;
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('The theme "%s" doesn\'t exist', $this->themeName), 0, $e);
        }
    }

    /**
     * @return string
     */
    private function getResourceDirectory(): string
    {
        $resourceDirectory = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->themeResourcesDir, DIRECTORY_SEPARATOR),
            $this->themeName,
            'translations',
        ]);
        $this->filesystem->mkdir($resourceDirectory);

        return $resourceDirectory;
    }
}
