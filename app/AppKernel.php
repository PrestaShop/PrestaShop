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
use PrestaShop\PrestaShop\Core\Version;
use PrestaShop\TranslationToolsBundle\TranslationToolsBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;


abstract class AppKernel extends Kernel
{
    const VERSION = Version::VERSION;
    const MAJOR_VERSION_STRING = Version::MAJOR_VERSION_STRING;
    const MAJOR_VERSION = Version::MAJOR_VERSION;
    const MINOR_VERSION = Version::MINOR_VERSION;
    const RELEASE_VERSION = Version::RELEASE_VERSION;

    /**
     * Lock stream is saved as static field, this way if multiple AppKernel are instanciated (this can happen in
     * test environment, they will be able to detect that a lock has already been made by the current process).
     *
     * @var resource|null
     */
    protected static $lockStream = null;

    /**
     * @var ModuleRepository
     */
    protected $moduleRepository = null;

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
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new ApiPlatform\Symfony\Bundle\ApiPlatformBundle(),
            // PrestaShop Core bundle
            new PrestaShopBundle\PrestaShopBundle($this),
            // PrestaShop Translation parser
            new TranslationToolsBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Symfony\UX\TwigComponent\TwigComponentBundle(),
            new Twig\Extra\TwigExtraBundle\TwigExtraBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        /* Will not work until PrestaShop is installed */
        $installedModules = $this->getModuleRepository()->getInstalledModules();
        if (!empty($installedModules)) {
            try {
                $this->enableComposerAutoloaderOnModules($installedModules);
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
        $this->waitUntilCacheClearIsOver();
        parent::boot();
        $this->cleanKernelReferences();
    }

    /**
     * Perform a lock on a file before cache clear is performed, this lock will be unlocked once the cache has been cleared.
     * Until then any other process will have to wait until the file is unlocked.
     *
     * @return bool Returns boolean indicating if the lock file was successfully locked.
     */
    public function locksCacheClear(): bool
    {
        $clearCacheLockPath = $this->getContainerClearCacheLockPath();
        $lockStream = fopen($clearCacheLockPath, 'w');
        if (false === $lockStream) {
            // Could not open writable lock for some reason
            return false;
        }

        // Non-blocking flock, if false is returned it means the file is already locked (meaning the cache is being cleared by another process)
        $clearCacheLocked = flock($lockStream, LOCK_EX | LOCK_NB);
        if (false === $clearCacheLocked) {
            // Clear cache is already locked by another process, so we simply return
            fclose($lockStream);
            return false;
        }

        // Save the locked stream so that we can close it later and most importantly, the process doesn't block it self
        // during the cache clear operation which reboots the app
        self::$lockStream = $lockStream;

        return true;
    }

    public function unlocksCacheClear(): void
    {
        if (null === self::$lockStream) {
            return;
        }

        $this->unlockCacheStream(self::$lockStream);
        self::$lockStream = null;
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
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');

        $activeModules = $this->getModuleRepository()->getActiveModules();
        // We only load translations and services of active modules (not simply installed)
        $moduleTranslationsPaths = [];
        foreach ($activeModules as $activeModulePath)
        {
            $modulePath = _PS_MODULE_DIR_ . $activeModulePath;
            $translationsPath = sprintf('%s/translations', $modulePath);

            $configFiles = [
                sprintf('%s/config/services.yml', $modulePath),
                sprintf('%s/config/admin/services.yml', $modulePath),
                // @todo Uncomment to Load this file once we'll have a unique container
                // sprintf('%s/config/front/services.yml', $modulePath),
            ];

            foreach ($configFiles as $file) {
                if(is_file($file)) {
                    $loader->load($file);
                }
            }

            if (is_dir($translationsPath)) {
                $moduleTranslationsPaths[] = $translationsPath;
            }
        }

        $installedModules = $this->getModuleRepository()->getInstalledModules();
        $loader->load(function (ContainerBuilder $container) use ($moduleTranslationsPaths, $activeModules, $installedModules) {
            $container->setParameter('container.autowiring.strict_mode', true);
            $container->setParameter('container.dumper.inline_class_loader', false);
            $container->setParameter('prestashop.module_dir', _PS_MODULE_DIR_);
            /** @deprecated kernel.active_modules is deprecated. Use prestashop.active_modules instead. */
            $container->setParameter('kernel.active_modules', $activeModules);
            $container->setParameter('prestashop.active_modules', $activeModules);
            $container->setParameter('prestashop.installed_modules', $installedModules);
            $container->addObjectResource($this);
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

    protected function getModuleRepository(): ModuleRepository
    {
        if ($this->moduleRepository === null) {
            $this->moduleRepository = new ModuleRepository(_PS_ROOT_DIR_, _PS_MODULE_DIR_);
        }

        return $this->moduleRepository;
    }

    protected function getContainerClearCacheLockPath(): string
    {
        $class = $this->getContainerClass();
        $cacheDir = $this->getCacheDir();

        return sprintf('%s/%s.php.cache_clear.lock', $cacheDir, $class);
    }

    protected function waitUntilCacheClearIsOver(): void
    {
        if (null !== self::$lockStream) {
            // If lockStream is not null it means we are actually in the process that locked it, we don't wait for anything
            // or the cache clear will never happen
            return;
        }

        $clearCacheLockPath = $this->getContainerClearCacheLockPath();
        // No lock file no need to wait for its unlock
        if (!file_exists($clearCacheLockPath)) {
            return;
        }

        $lockStream = fopen($clearCacheLockPath, 'w');
        if (false === $lockStream) {
            // Could not open writable lock for some reason
            return;
        }

        // Check if the lock file is currently locked (see locksCacheClear responsible for locking this file), this
        // function call is blocking until the lock has been released.
        flock($lockStream, LOCK_SH);

        // Now that the file is unlocked it means the cache has been cleared we can safely continue the process as the container
        // has been rebuilt and is good to go.
        $this->unlockCacheStream($lockStream);
    }

    /**
     * @param resource $lockStream
     */
    protected function unlockCacheStream($lockStream): void
    {
        flock($lockStream, LOCK_UN);
        fclose($lockStream);

        // Also remove the lock file so that the lock check is ignored right away
        $clearCacheLockPath = $this->getContainerClearCacheLockPath();
        if (file_exists($clearCacheLockPath)) {
            unlink($clearCacheLockPath);
        }
    }

    /**
     * Get App type of current Kernel based on kernel class name. (admin or front)
     *
     * @return string
     */
     public function getAppType(): string
     {
         return $this instanceof \AdminKernel ? 'admin' : 'front';
     }
}
