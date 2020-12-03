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

namespace PrestaShop\PrestaShop\Adapter\Module;

use Exception;
use PrestaShopBundle\Event\ModuleZipManagementEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use ZipArchive;

/**
 * Responsible of unzipping of Module Zip archives.
 */
class ModuleZipManager
{
    /*
     * Data
     */
    private static $sources = [];

    /*
     * Services
     */
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Filesystem $filesystem,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher
        ) {
        $this->filesystem = $filesystem;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Detect module name from zipball.
     *
     * @param string $source
     *
     * @return string
     *
     * @throws Exception If unable to find the module name
     */
    public function getName($source)
    {
        $this->initSource($source);

        if ($this->getSource($source)->getName($source) !== null) {
            return $this->getSource($source)->getName($source);
        }

        if (!file_exists($source)) {
            throw new Exception($this->translator->trans('Unable to find uploaded module at the following path: %file%', ['%file%' => $source], 'Admin.Modules.Notification'));
        }

        $sandboxPath = $this->getSandboxPath($source);
        $zip = new ZipArchive();
        if ($zip->open($source) === false || !$zip->extractTo($sandboxPath) || !$zip->close()) {
            throw new Exception($this->translator->trans('Cannot extract module in %path% to get its name. %error%', ['%path%' => $sandboxPath, '%error%' => $zip->getStatusString()], 'Admin.Modules.Notification'));
        }

        // Check the structure and get the module name
        $directories = Finder::create()
            ->directories()
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
            $moduleFolder = Finder::create()
                    ->files()
                    ->in($sandboxPath . $moduleName)
                    ->depth('== 0')
                    ->exclude(['__MACOSX'])
                    ->ignoreVCS(true);
            foreach (iterator_to_array($moduleFolder) as $file) {
                if ($file->getFileName() === $moduleName . '.php') {
                    $validModuleStructure = true;

                    break;
                }
            }
        }

        if (!$validModuleStructure) {
            $this->filesystem->remove($sandboxPath);

            throw new Exception($this->translator->trans('This file does not seem to be a valid module zip', [], 'Admin.Modules.Notification'));
        }

        $this->getSource($source)->setName($moduleName);

        return $moduleName;
    }

    /**
     * When ready, send the module Zip in the modules folder.
     *
     * @param string $source
     */
    public function storeInModulesFolder($source)
    {
        $name = $this->getName($source);
        $sandboxPath = $this->getSandboxPath($source);
        // Now we are sure to have a valid module, we copy it to the modules folder
        $modulePath = _PS_MODULE_DIR_ . $name;
        $this->filesystem->mkdir($modulePath);
        $this->filesystem->mirror(
            $sandboxPath . $name,
            $modulePath,
            null,
            ['override' => true]
        );
        $this->eventDispatcher
            ->dispatch(
                ModuleZipManagementEvent::DOWNLOAD,
                new ModuleZipManagementEvent($this->getSource($source))
            );

        $this->filesystem->remove($sandboxPath);
    }

    /**
     * @param $source
     *
     * @return string|null
     */
    private function getSandboxPath($source)
    {
        $sandboxPath = $this->getSource($source)->getSandboxPath();
        if ($sandboxPath === null) {
            $sandboxPath = _PS_CACHE_DIR_ . 'sandbox/' . uniqid() . '/';
            $this->filesystem->mkdir($sandboxPath);
            $this->getSource($source)->setSandboxPath($sandboxPath);
        }

        return $sandboxPath;
    }

    /**
     * Get a ModuleZip instance from a given source (= zip filepath).
     *
     * @param string $source
     *
     * @return ModuleZip|null
     */
    private function getSource($source)
    {
        if (!array_key_exists($source, self::$sources)) {
            return null;
        }

        return self::$sources[$source];
    }

    /**
     * Init all data regarding a source before proceeding it.
     *
     * @param string $source
     */
    private function initSource($source)
    {
        if ((filter_var($source, FILTER_VALIDATE_URL))) {
            $source = Tools::createFileFromUrl($source);
        }

        if ($this->getSource($source) === null) {
            self::$sources[$source] = new ModuleZip($source);
        }
    }
}
