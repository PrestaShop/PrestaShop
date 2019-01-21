<?php
/**
 * 2007-2019 PrestaShop.
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Composer;

use Composer\IO\IOInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

final class ModulesConfigurationProcessor
{
    /**
     * @var IOInterface the CLI IO interface
     */
    private $io;

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * Foreach module, will install the module dependencies
     * into /modules/{module}/vendor folder.
     *
     * @param array $configuration the Composer configuration
     * @param string $rootPath the modules directory location
     */
    public function processInstallation(array $configuration, $rootPath)
    {
        $this->io->write('<info>PrestaShop Module installer</info>');

        if (!array_key_exists('native-modules', $configuration) || !array_key_exists('modules-dir', $configuration)) {
            return;
        }

        $nativeModules = $configuration['native-modules'];
        $modulesLocation = $rootPath . $configuration['modules-dir'];

        foreach ($nativeModules as $moduleName => $moduleVersion) {
            $this->io->write(sprintf('<info>Looked into "%s" module (version %s)</info>', $moduleName, $moduleVersion));

            $this->installModule($moduleName, $moduleVersion, $modulesLocation);
        }
    }


    /**
     * Foreach module, will install the module dependencies
     * into /modules/{module}/vendor folder.
     *
     * @param array $configuration the Composer configuration
     * @param string $rootPath the modules directory location
     */
    public function processUpdate(array $configuration, $rootPath)
    {
        $this->io->write('<info>PrestaShop Module installer</info>');

        if (!array_key_exists('native-modules', $configuration) || !array_key_exists('modules-dir', $configuration)) {
            return;
        }

        $nativeModules = $configuration['native-modules'];
        $modulesLocation = $rootPath . $configuration['modules-dir'];

        foreach ($nativeModules as $moduleName => $moduleVersion) {
            $this->io->write(sprintf('<info>Looked into "%s" module (version %s)</info>', $moduleName, $moduleVersion));

            $this->updateModule($moduleName, $moduleVersion, $modulesLocation);
        }
    }

    private function installModule($moduleName, $moduleVersion, $location)
    {
        $moduleInformation = ModuleInformation::createFromString($moduleName, $moduleVersion);

        if (file_exists($location . $moduleInformation->getName())) {
            $this->io->write(sprintf('Module "%s" is already installed, skipped.', $moduleInformation->getName()));

            return;
        }

        $command = 'composer create-project '.$moduleName.':'.$moduleVersion;

        $process = new Process($command);
        $process->setWorkingDirectory($location);

        try {
            $process->mustRun();
            $this->io->write(sprintf('Module "%s" successfully installed!', $moduleInformation->getName()));

            $this->io->write($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->io->writeError($process->getErrorOutput());
        }

    }

    private function updateModule($moduleName, $moduleVersion, $location)
    {
        $command = 'composer create-project '.$moduleName.':'.$moduleVersion;

        $process = new Process($command);
        $process->setWorkingDirectory($location);

        try {
            $process->mustRun();

            $this->io->write($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->io->writeError($process->getErrorOutput());
        }
    }
}

