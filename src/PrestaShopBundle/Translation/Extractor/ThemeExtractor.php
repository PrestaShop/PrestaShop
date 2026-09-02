<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Extractor;

use Exception;
use LogicException;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\XliffFileDumper;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Dumper\FileDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extract all theme translations from Theme templates.
 *
 * xliff is the default file format, you can add custom File dumpers.
 */
class ThemeExtractor
{
    /**
     * @var MessageCatalogue|null the Message catalogue
     */
    private $catalog;

    /**
     * @var array the list of Translation dumpers
     */
    private $dumpers = [];

    /**
     * @var string the format of extracted files
     */
    private $format = 'xlf';

    /**
     * @var string the output path for extraction
     */
    private $outputPath;

    /**
     * @var SmartyExtractor the Smarty Extractor
     */
    private $smartyExtractor;

    /**
     * @var ThemeProvider the Theme Provider
     */
    private $themeProvider;

    /**
     * @var bool checks wether we should override the database with results or not
     */
    private $overrideFromDatabase = false;

    public function __construct(SmartyExtractor $smartyExtractor)
    {
        $this->smartyExtractor = $smartyExtractor;
        $this->dumpers[] = new XliffFileDumper();
    }

    /**
     * @param ThemeProvider $themeProvider
     *
     * @return $this
     */
    public function setThemeProvider(ThemeProvider $themeProvider)
    {
        $this->themeProvider = $themeProvider;

        return $this;
    }

    /**
     * @param Theme $theme
     * @param string $locale
     * @param bool $rootDir
     *
     * @return MessageCatalogue|null
     *
     * @throws Exception
     */
    public function extract(Theme $theme, $locale = 'en-US', $rootDir = false): ?MessageCatalogue
    {
        $this->catalog = new MessageCatalogue($locale);
        // remove the last "/"
        $themeDirectory = substr($theme->getDirectory(), 0, -1);

        $options = [
            'path' => $themeDirectory,
            'default_locale' => $locale,
            'root_dir' => $rootDir,
        ];
        $this->smartyExtractor->extract($themeDirectory, $this->catalog);

        $this->overrideFromDefaultCatalog($locale, $this->catalog);

        if ($this->overrideFromDatabase) {
            $this->overrideFromDatabase($theme->getName(), $locale, $this->catalog);
        }

        foreach ($this->dumpers as $dumper) {
            if ($this->format === $dumper->getExtension()) {
                if (null !== $this->outputPath) {
                    $options['path'] = $this->outputPath;
                }

                $dumper->dump($this->catalog, $options);

                return $this->catalog;
            }
        }

        throw new LogicException(sprintf('The format %s is not supported.', $this->format));
    }

    /**
     * Add default catalogue in this &$catalogue when the translation exists.
     *
     * @param string $locale
     * @param MessageCatalogue $catalogue
     */
    private function overrideFromDefaultCatalog($locale, &$catalogue)
    {
        $defaultCatalogue = $this->themeProvider
            ->setLocale($locale)
            ->getDefaultCatalogue();

        if (empty($defaultCatalogue)) {
            return;
        }

        $defaultCatalogue = $defaultCatalogue->all();

        if (empty($defaultCatalogue)) {
            return;
        }

        $defaultDomainsCatalogue = $catalogue->getDomains();

        foreach ($defaultCatalogue as $domain => $translation) {
            // AdminCatalogFeature.fr-FR to AdminCatalogFeature
            $domain = str_replace('.' . $locale, '', $domain);

            // AdminCatalogFeature to Admin.Catalog.Feature
            $domain = implode('.', preg_split('/(?=[A-Z])/', $domain, -1, PREG_SPLIT_NO_EMPTY));

            if (in_array($domain, $defaultDomainsCatalogue, true)) {
                foreach ($translation as $key => $trans) {
                    if ($catalogue->has($key, $domain)) {
                        $catalogue->set($key, $trans, $domain);
                    }
                }
            }
        }
    }

    /**
     * Add database catalogue in this &$catalogue.
     *
     * @param string $themeName
     * @param string $locale
     * @param MessageCatalogue $catalogue
     *
     * @throws Exception
     */
    private function overrideFromDatabase($themeName, $locale, &$catalogue): void
    {
        if (null === $this->themeProvider) {
            throw new Exception('Theme provider is required.');
        }

        $databaseCatalogue = $this->themeProvider
            ->setLocale($locale)
            ->setThemeName($themeName)
            ->getDatabaseCatalogue();

        $catalogue->addCatalogue($databaseCatalogue);
    }

    /**
     * @param FileDumper $dumper
     *
     * @return $this
     */
    public function addDumper(FileDumper $dumper)
    {
        $this->dumpers[] = $dumper;

        return $this;
    }

    /**
     * @return DumperInterface[]
     */
    public function getDumpers()
    {
        return $this->dumpers;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $outputPath
     *
     * @return $this
     */
    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * @return MessageCatalogue|null
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * @return $this
     */
    public function disableOverridingFromDatabase()
    {
        $this->overrideFromDatabase = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableOverridingFromDatabase()
    {
        $this->overrideFromDatabase = true;

        return $this;
    }
}
