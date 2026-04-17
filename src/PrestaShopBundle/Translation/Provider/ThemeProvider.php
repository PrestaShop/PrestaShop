<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use Exception;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use PrestaShopBundle\Translation\Extractor\ThemeExtractor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class ThemeProvider extends AbstractProvider
{
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
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $xlfCatalogue = $this->getXliffCatalogue();
        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $xlfCatalogue->addCatalogue($databaseCatalogue);

        return $xlfCatalogue;
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
     * @param string|null $themeName
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        if (null === $themeName) {
            $themeName = $this->themeName;
        }

        return parent::getDatabaseCatalogue($themeName);
    }

    /**
     * @throws Exception
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
     * @throws Exception
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
