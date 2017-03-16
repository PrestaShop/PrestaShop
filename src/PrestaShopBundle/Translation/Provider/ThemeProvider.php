<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ThemeProvider extends AbstractProvider
{
    private $themeName;

    public $themeResourcesDirectory;

    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository
     */
    public $themeRepository;

    /**
     * @var \PrestaShopBundle\Translation\Extractor\ThemeExtractor
     */
    public $themeExtractor;

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array('*');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array('*');
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
     * @param null $baseDir
     * @return string Path to app/themes/{themeName}/translations/{locale}
     */
    public function getResourceDirectory($baseDir = null)
    {
        if (is_null($baseDir)) {
            $baseDir = $this->resourceDirectory;
        }

        $resourceDirectory = $baseDir.'/'.$this->themeName.'/translations/'.$this->getLocale();
        $this->filesystem->mkdir($resourceDirectory);

        return $resourceDirectory;
    }

    /**
     * @return array
     */
    public function getDirectories()
    {
        return array(
            $this->getResourceDirectory(),
            $this->getThemeResourcesDirectory(),
        );
    }

    /**
     * @return string
     */
    public function getThemeResourcesDirectory()
    {
        return $this->getResourceDirectory($this->themeResourcesDirectory);
    }

    /**
     * @param $themeName string The theme name
     *
     * @return self
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @param null $themeName
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        if (is_null($themeName)) {
            $themeName = $this->themeName;
        }

        return parent::getDatabaseCatalogue($themeName);
    }

    public function synchronizeTheme()
    {
        $theme = $this->themeRepository->getInstanceByName($this->themeName);

        $path = $this->resourceDirectory.'/'.$this->themeName.'/translations';

        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);

        $this->themeExtractor
            ->setOutputPath($path)
            ->setThemeProvider($this)
            ->extract($theme, $this->locale)
        ;

        $translationFilesPath = $path.'/'.$this->locale;
        Flattenizer::flatten($translationFilesPath, $translationFilesPath, $this->locale, false);

        $finder = Finder::create();
        foreach ($finder->directories()->depth('== 0')->in($translationFilesPath) as $folder) {
            $this->filesystem->remove($folder);
        }
    }

    /**
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    public function getThemeCatalogue()
    {
        return $this->getCatalogueFromPaths($this->getThemeResourcesDirectory(), $this->locale, '*');
    }
}
