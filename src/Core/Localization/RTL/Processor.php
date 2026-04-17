<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use Exception;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException;

/**
 * Processes stylesheets by transforming them to RTL.
 */
class Processor
{
    /**
     * @var string Installed language 2-letter ISO code
     */
    private $languageCode = '';

    /**
     * @var bool Indicates if the BO theme should be processed
     */
    private $processBOTheme = false;

    /**
     * @var string[] Names of the FO themes to process
     */
    private $processFOThemes = [];

    /**
     * @var array Indicates additional paths to process
     */
    private $processPaths = [];

    /**
     * @var string[] Path to the default modules to process
     */
    private $defaultModulesToProcess = [];

    /**
     * @var bool Indicates if the default modules should be processed
     */
    private $processDefaultModules = false;

    /**
     * @var string Path to PrestaShop's admin directory
     */
    private $adminDir = '';

    /**
     * @var string Path to the themes directory
     */
    private $themesDir = '';

    /**
     * Processor constructor.
     *
     * @param string $adminDir Path to PrestaShop's admin directory
     * @param string $themesDir Path to the FO themes directory
     * @param string[] $defaultModulesToProcess Path to the default modules to process
     */
    public function __construct($adminDir, $themesDir, array $defaultModulesToProcess)
    {
        $this->adminDir = $adminDir;
        $this->themesDir = $themesDir;
        $this->defaultModulesToProcess = $defaultModulesToProcess;
    }

    /**
     * Specifies the installed language 2-letter ISO code.
     *
     * @param string $languageCode
     *
     * @return Processor
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * Specifies if the BO theme should be processed.
     *
     * @param bool $processBOTheme
     *
     * @return Processor
     */
    public function setProcessBOTheme($processBOTheme)
    {
        $this->processBOTheme = $processBOTheme;

        return $this;
    }

    /**
     * Specifies the names of the FO themes to process.
     *
     * @param string[] $processFOThemes
     *
     * @return Processor
     */
    public function setProcessFOThemes(array $processFOThemes)
    {
        $this->processFOThemes = $processFOThemes;

        return $this;
    }

    /**
     * Specifies additional paths to process.
     *
     * @param string[] $processPaths
     *
     * @return Processor
     */
    public function setProcessPaths(array $processPaths)
    {
        $this->processPaths = $processPaths;

        return $this;
    }

    /**
     * Specifies if the default modules should be processed.
     *
     * @param bool $processDefaultModules
     *
     * @return Processor
     */
    public function setProcessDefaultModules($processDefaultModules)
    {
        $this->processDefaultModules = $processDefaultModules;

        return $this;
    }

    /**
     * @throws GenerationException
     * @throws Exception
     */
    public function process()
    {
        if ($this->languageCode) {
            $lang_pack = Language::getLangDetails($this->languageCode);
            if (!$lang_pack['is_rtl']) {
                return;
            }
        }

        $generator = new StylesheetGenerator();
        // generate stylesheets for BO themes
        if ($this->processBOTheme) {
            if (!is_dir($this->adminDir)) {
                throw new GenerationException("Cannot generate BO themes: \"{$this->adminDir}\" is not a directory");
            }

            $generator->generateInDirectory($this->adminDir . DIRECTORY_SEPARATOR . 'themes');
        }

        // generate stylesheets for BO themes
        if ($this->processFOThemes) {
            foreach ($this->processFOThemes as $themeName) {
                $generator->generateInDirectory($this->themesDir . DIRECTORY_SEPARATOR . $themeName);
            }
        }

        // generate stylesheets for default modules
        if ($this->processDefaultModules) {
            $this->processPaths = array_merge($this->processPaths, $this->defaultModulesToProcess);
        }

        foreach ($this->processPaths as $path) {
            if (!empty($path) && is_dir($path)) {
                $generator->generateInDirectory($path);
            }
        }
    }
}
