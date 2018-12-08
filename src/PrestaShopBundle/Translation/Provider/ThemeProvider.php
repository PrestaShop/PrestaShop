<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\TranslationToolsBundle\Translation\Extractor\Util\Flattenizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

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

    private $domain;

    /**
     * @var string Path to app/Resources/translations/
     */
    public $defaultTranslationDir;

    /**
     * Set domain.
     *
     * @param $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain.
     *
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        if (empty($this->domain)) {
            return array('*');
        } else {
            return array(
                '^'.$this->getDomain(),
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        if (empty($this->domain)) {
            return array('*');
        } else {
            return array(
                '#^'.$this->getDomain().'#',
            );
        }
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
     *
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
     *
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

        $path = $this->resourceDirectory.DIRECTORY_SEPARATOR.$this->themeName.DIRECTORY_SEPARATOR.'translations';

        $this->filesystem->remove($path);
        $this->filesystem->mkdir($path);

        $this->themeExtractor
            ->setOutputPath($path)
            ->setThemeProvider($this)
            ->extract($theme, $this->locale)
        ;

        $translationFilesPath = $path.DIRECTORY_SEPARATOR.$this->locale;
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
        $path = $this->resourceDirectory.DIRECTORY_SEPARATOR.$this->themeName.DIRECTORY_SEPARATOR.'translations';

        return $this->getCatalogueFromPaths($path, $this->locale, current($this->getFilters()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue($empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->getLocale());

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                array($this->getDefaultResourceDirectory()),
                $this->getLocale(),
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     * string Path to app/Resources/translations/{locale}.
     */
    public function getDefaultResourceDirectory()
    {
        return $this->defaultTranslationDir.DIRECTORY_SEPARATOR.$this->locale;
    }
}
