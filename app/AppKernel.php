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

use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    const VERSION = '8.0.0';
    const MAJOR_VERSION_STRING = '8';
    const MAJOR_VERSION = 8;
    const MINOR_VERSION = 0;
    const RELEASE_VERSION = 0;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // PrestaShop Core bundle
            new PrestaShopBundle\PrestaShopBundle(),
            // PrestaShop Translation parser
            new PrestaShop\TranslationToolsBundle\TranslationToolsBundle(),
            new League\Tactician\Bundle\TacticianBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        /* Will not work until PrestaShop is installed */
        $activeModules = $this->getActiveModules();
        if (!empty($activeModules)) {
            try {
                $this->enableComposerAutoloaderOnModules($activeModules);
            } catch (\Exception $e) {
            }
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
        $this->cleanKernelReferences();
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        parent::shutdown();
        $this->cleanKernelReferences();
    }

    /**
     * The kernel and especially its container is cached in several PrestaShop classes, services or components So we
     * need to clear this cache everytime the kernel is shutdown, rebooted, reset, ...
     *
     * This is very important in test environment to avoid invalid mocks to stay accessible and used, but it's also
     * important because we may need to reboot the kernel (during module installation, after currency is installed
     * to reset CLDR cache, ...)
     */
    protected function cleanKernelReferences(): void
    {
        // We have classes to access the container from legacy code, they need to be cleaned after reboot
        Context::getContext()->container = null;
        SymfonyContainer::resetStaticCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernelParameters()
    {
        $kernelParameters = parent::getKernelParameters();

        return array_merge(
            $kernelParameters,
            array('kernel.active_modules' => $this->getActiveModules())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return _PS_CACHE_DIR_;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', false);
            $container->addObjectResource($this);
        });

        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');

        // Add translation paths to load into the translator. The paths are loaded by the Symfony's FrameworkExtension
        $loader->load(function (ContainerBuilder $container) {
            $moduleTranslationsPaths = $container->getParameter('modules_translation_paths');
            foreach ($this->getActiveModules() as $activeModulePath) {
                $translationsDir = _PS_MODULE_DIR_ . $activeModulePath . '/translations';
                if (is_dir($translationsDir)) {
                    $moduleTranslationsPaths[] = $translationsDir;
                }
            }
            $container->setParameter('modules_translation_paths', $moduleTranslationsPaths);
        });
    }

    /**
     * Enable auto loading of module Composer autoloader if needed.
     * Need to be done as earlier as possible in application lifecycle.
     *
     * Note: this feature is also manage in PrestaShop\PrestaShop\Adapter\ContainerBuilder
     * for non Symfony environments.
     *
     * @param array $modules the list of modules
     */
    private function enableComposerAutoloaderOnModules($modules)
    {
        $moduleDirectoryPath = rtrim(_PS_MODULE_DIR_, '/') . '/';
        foreach ($modules as $module) {
            $autoloader = $moduleDirectoryPath . $module . '/vendor/autoload.php';

            if (file_exists($autoloader)) {
                include_once $autoloader;
            }
        }
    }

    /**
     * Gets the application root dir.
     * Override Kernel due to the fact that we remove the composer.json in
     * downloaded package. More we are not a framework and the root directory
     * should always be the parent of this file.
     *
     * @return string The project root dir
     */
    public function getProjectDir()
    {
        return realpath(__DIR__ . '/..');
    }

    private function getActiveModules(): array
    {
        $activeModules = [];
        try {
            $activeModules = (new ModuleRepository(_PS_ROOT_DIR_, _PS_MODULE_DIR_))->getActiveModules();
        } catch (\Exception $e) {
            //Do nothing because the modules retrieval must not block the kernel, and it won't work
            //during the installation process
        }

        return $activeModules;
    }
}
