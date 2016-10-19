<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class ThemeExporter
{
    protected $configuration;
    protected $fileSystem;
    protected $finder;

    public function __construct(ConfigurationInterface $configuration, Filesystem $fileSystem, Finder $finder)
    {
        $this->configuration = $configuration;
        $this->fileSystem = $fileSystem;
        $this->finder = $finder;
    }

    public function export(Theme $theme)
    {
        $cacheDir = $this->configuration->get('_PS_CACHE_DIR_').'/export-'.$theme->getName().'-'.time().'/';

        $this->copyTheme($theme->getDirectory(), $cacheDir);
        $this->copyModuleDependencies((array) $theme->get('dependencies.modules'), $cacheDir);

        $finalFile = $this->configuration->get('_PS_ALL_THEMES_DIR_').'/'.$theme->getName().'.zip';
        $this->createZip($cacheDir, $finalFile);

        $this->fileSystem->remove($cacheDir);

        return realpath($finalFile);
    }

    private function copyTheme($themeDir, $cacheDir)
    {
        $this->fileSystem->mirror($themeDir, $cacheDir);
    }

    private function copyModuleDependencies(array $moduleList, $cacheDir)
    {
        if (empty($moduleList)) {
            return;
        }

        $dependencyDir = $cacheDir.'/dependencies/modules/';
        $this->fileSystem->mkdir($dependencyDir);
        $moduleDir = $this->configuration->get('_PS_MODULE_DIR_');

        foreach ($moduleList as $moduleName) {
            $this->fileSystem->mirror($moduleDir.$moduleName, $dependencyDir.$moduleName);
        }
    }

    private function createZip($sourceDir, $destinationFileName)
    {
        $zip = new ZipArchive();
        $zip->open($destinationFileName, ZipArchive::CREATE);

        $finderClassName = get_class($this->finder);
        $this->finder = $finderClassName::create();
        $files = $this->finder
            ->files()
            ->in($sourceDir)
            ->exclude(['node_modules']);

        foreach ($files as $file) {
            $zip->addFile($file->getRealpath(), $file->getRelativePathName());
        }

        return $zip->close();
    }
}
