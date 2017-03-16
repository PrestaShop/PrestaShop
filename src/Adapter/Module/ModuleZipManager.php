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

namespace PrestaShop\PrestaShop\Adapter\Module;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;

use Exception;
use Tools;
use ZipArchive;

class ModuleZipManager
{
    /*
     * Data
     */
    private static $sources = array();
    private $attributes = array('name', 'sandboxPath');

    /*
     * Services
     */
    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
    /**
     * @var Symfony\Component\Finder\Finder
     */
    private $finder;
    private $translator;
    
    public function __construct(Filesystem $filesystem, Finder $finder, TranslatorInterface $translator)
    {
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->translator = $translator;
    }

    /**
     * Detect module name from zipball.
     * @param String $source
     * @return String
     * @throws Exception If unable to find the module name
     */
    public function getName($source)
    {
        $this->initSource($source);
        
        if ($this->get($source, 'name') !== null) {
            return $this->get($source, 'name');
        }

        if (!file_exists($source)) {
            throw new Exception(
                $this->translator->trans(
                    'Unable to find uploaded module at the following path: %file%',
                    array('%file%' => $source),
                    'Admin.Modules.Notification'));
        }

        $sandboxPath = $this->getSandboxPath($source);
        $zip = new ZipArchive();
        if ($zip->open($source) === false || !$zip->extractTo($sandboxPath) || !$zip->close()) {
            throw new Exception(
                $this->translator->trans(
                    'Cannot extract module in %path% to get its name. %error%',
                    array(
                        '%path%' => $sandboxPath,
                        '%error%' => $zip->getStatusString()),
                    'Admin.Modules.Notification'));
        }

        // Check the structure and get the module name
        $directories = $this->finder->directories()
            ->in($sandboxPath)
            ->depth('== 0')
            ->exclude(['__MACOSX'])
            ->ignoreVCS(true);

        $validModuleStructure = false;
        // We must have only one folder in the zip, which contains the module files
        if (iterator_count($directories->directories()) == 1) {
            $directories = iterator_to_array($directories);
            $moduleName = basename(current($directories)->getFileName());

            // Inside of this folder, we MUST have a file called <module name>.php
            $moduleFolder = $this->finder->files()
                    ->in($sandboxPath.$moduleName)
                    ->depth('== 0')
                    ->exclude(['__MACOSX'])
                    ->ignoreVCS(true);
            foreach (iterator_to_array($moduleFolder) as $file) {
                if ($file->getFileName() === $moduleName.'.php') {
                    $validModuleStructure = true;
                    break;
                }
            }
        }

        if (!$validModuleStructure) {
            $this->filesystem->remove($sandboxPath);
            throw new Exception($this->translator->trans(
                    'This file does not seem to be a valid module zip',
                    array(),
                    'Admin.Modules.Notification'));
        }

        $this->set($source, 'name', $moduleName);
        return $moduleName;
    }

    /**
     * When ready, send the module Zip in the modules folder
     * @param String $source
     */
    public function storeInModulesFolder($source)
    {
        $name = $this->getName($source);
        $sandboxPath = $this->get($source, 'sandboxPath');
        // Now we are sure to have a valid module, we copy it to the modules folder
        $modulePath = _PS_MODULE_DIR_.$name;
        $this->filesystem->mkdir($modulePath);
        $this->filesystem->mirror(
            $sandboxPath.$name,
            $modulePath,
            null,
            array('override' => true)
        );
        $this->filesystem->remove($sandboxPath);
    }

    private function getSandboxPath($source)
    {
        $sandboxPath = $this->get($source, 'sandboxPath');
        if ($sandboxPath === null) {
            $sandboxPath = _PS_CACHE_DIR_.'sandbox/'.uniqid().'/';
            $this->filesystem->mkdir($sandboxPath);
            $this->set($source, 'sandboxPath', $sandboxPath);
        }
        return $sandboxPath;
    }

    /**
     * Get a attribute value about a source
     * @param String $source
     * @param String $attr defined in $attributes
     * @return mixed
     * @throws Exception if $attr value not in list
     */
    private function get($source, $attr)
    {
        if (!in_array($attr, $this->attributes)) {
            throw new Exception('Unknow source attribute');
        }
        return self::$sources[$source][$attr];
    }

    /**
     * Store a value about a source
     * @param String $source
     * @param String $attr
     * @param mixed $value
     * @throws Exception if $attr value not in list
     */
    private function set($source, $attr, $value)
    {
        if (!in_array($attr, $this->attributes)) {
            throw new Exception('Unknow source attribute');
        }
        self::$sources[$source][$attr] = $value;
    }

    /**
     * Init all data regarding a source before proceeding it
     * @param String $source
     */
    private function initSource($source)
    {
        if ((filter_var($source, FILTER_VALIDATE_URL))) {
            $source = Tools::createFileFromUrl($source);
        }

        if (isset(self::$sources[$source])) {
            return;
        }
        foreach ($this->attributes as $attr) {
            self::$sources[$source][$attr] = null;
        }
    }
}
