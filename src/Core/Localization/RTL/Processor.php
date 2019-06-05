<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

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
    private $processFOThemes = array();

    /**
     * @var array Indicates additional paths to process
     */
    private $processPaths = array();

    /**
     * @var bool Indicates if this is performed during install
     */
    private $isInstall = false;

    /**
     * @var bool Indicates if the RTL files should be generated even if they already exist
     */
    private $regenerate = false;

    /**
     * @var string[] Path to the default modules to process
     */
    private $defaultModulesToProcess = array();

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
     * Specifies if this is performed during install.
     *
     * @param bool $isInstall
     *
     * @return Processor
     */
    public function setIsInstall($isInstall)
    {
        $this->isInstall = $isInstall;

        return $this;
    }

    /**
     * Specifies if the RTL files should be generated even if they already exist.
     *
     * @param bool $regenerate
     *
     * @return Processor
     */
    public function setRegenerate($regenerate)
    {
        $this->regenerate = $regenerate;

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
     * @throws Exception\GenerationException
     * @throws \Exception
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

        if (!empty($this->processPaths)) {
            foreach ($this->processPaths as $path) {
                if (!empty($path) && is_dir($path)) {
                    $generator->generateInDirectory($path);
                }
            }
        }
    }
}
