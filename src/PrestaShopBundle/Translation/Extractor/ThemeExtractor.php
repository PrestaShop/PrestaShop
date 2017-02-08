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

namespace PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Translation\Dumper\FileDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extract all theme translations from Theme templates.
 *
 * xliff is the default file format, you can add custom File dumpers.
 */
class ThemeExtractor
{
    private $catalog;
    private $dumpers = array();
    private $format = 'xlf';
    private $outputPath;
    private $smartyExtractor;
    private $themeProvider;

    private $overrideFromDatabase = false;

    public function __construct(SmartyExtractor $smartyExtractor)
    {
        $this->smartyExtractor = $smartyExtractor;
        $this->dumpers[] = new XliffFileDumper();
    }

    public function setThemeProvider(ThemeProvider $themeProvider)
    {
        $this->themeProvider = $themeProvider;

        return $this;
    }

    public function extract(Theme $theme, $locale = 'en-US', $rootDir = false)
    {
        $this->catalog = new MessageCatalogue($locale);
        // remove the last "/"
        $themeDirectory = substr($theme->getDirectory(), 0, -1);

        $options = array(
            'path' => $themeDirectory,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
        );
        $this->smartyExtractor->extract($themeDirectory, $this->catalog, $options['root_dir']);

        if ($this->overrideFromDatabase) {
            $this->overrideFromDatabase($theme->getName(), $locale, $this->catalog);
        }

        foreach ($this->dumpers as $dumper) {
            if ($this->format === $dumper->getExtension()) {
                if (null !== $this->outputPath) {
                    $options['path'] = $this->outputPath;
                }

                return $dumper->dump($this->catalog, $options);
            }
        }

        throw new \LogicException(sprintf('The format %s is not supported.', $this->format));
    }

    private function overrideFromDatabase($themeName, $locale, &$catalogue)
    {
        if (is_null($this->themeProvider)) {
            throw new \Exception('Theme provider is required.');
        }

        $databaseCatalogue = $this->themeProvider
            ->setLocale($locale)
            ->setThemeName($themeName)
            ->getDatabaseCatalogue()
        ;

        $catalogue->addCatalogue($databaseCatalogue);
    }

    public function addDumper(FileDumper $dumper)
    {
        $this->dumpers[] = $dumper;

        return $this;
    }

    public function getDumpers()
    {
        return $this->dumpers;
    }

    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;

        return $this;
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function getCatalog()
    {
        return $this->catalog;
    }

    public function disableOverridingFromDatabase()
    {
        $this->overrideFromDatabase = false;

        return $this;
    }

    public function enableOverridingFromDatabase()
    {
        $this->overrideFromDatabase = true;

        return $this;
    }
}
