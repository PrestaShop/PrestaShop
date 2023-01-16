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

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use AppKernel;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface as SymfonyCacheClearerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Tools;

/**
 * Class SymfonyCacheClearer clears Symfony cache directly from filesystem.
 *
 * @internal
 */
final class SymfonyCacheClearer implements CacheClearerInterface
{
    /**
     * @var bool
     */
    private $shutdownRegistered = false;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var SymfonyCacheClearerInterface
     */
    private $cacheClearer;

    /**
     * @var array
     */
    private $warmupFolders = [];

    public function __construct(SymfonyCacheClearerInterface $cacheClearer)
    {
        $this->cacheClearer = $cacheClearer;
        $this->fs = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        /*  @var KernelInterface */
        global $kernel;

        if (empty($kernel)) {
            Tools::clearSf2Cache();

            return;
        }

        // Reboot kernel right away so that it is up to date for the current process
        $this->rebootKernel($kernel);

        if (!$this->shutdownRegistered) {
            $this->shutdownRegistered = true;
            register_shutdown_function(function () use ($kernel) {
                $this->clearCacheFolders($kernel);
            });
        }
    }

    private function rebootKernel(AppKernel $kernel): void
    {
        $warmupDir = $this->getNewWarmupCacheDir($kernel);
        $this->warmupFolders[] = $warmupDir;
        $kernel->reboot($warmupDir);
    }

    private function clearCacheFolders(AppKernel $kernel): void
    {
        array_map(function ($warmupDir) {
            if (file_exists($warmupDir)) {
                $this->fs->remove($warmupDir);
            }
        }, $this->warmupFolders);
        $this->fs->remove($kernel->getCacheDir());
        // Clear cache
        $this->cacheClearer->clear($kernel->getCacheDir());
    }

    /**
     * @param AppKernel $kernel
     *
     * @return string
     */
    private function getNewWarmupCacheDir(AppKernel $kernel): string
    {
        $cacheDir = $kernel->getCacheDir();
        $offset = 0;
        $warmupDir = $cacheDir;
        while (file_exists($warmupDir)) {
            ++$offset;
            // Keep the same length for the name
            $warmupDir = substr($cacheDir, 0, strlen($cacheDir) - strlen((string) $offset)) . $offset;
        }

        return $warmupDir;
    }
}
