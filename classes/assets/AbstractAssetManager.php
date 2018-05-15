<?php

/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

abstract class AbstractAssetManagerCore
{
    protected $directories;
    protected $configuration;
    protected $list = array();

    const DEFAULT_MEDIA = 'all';
    const DEFAULT_PRIORITY = 50;
    const DEFAULT_JS_POSITION = 'bottom';

    use PrestaShop\PrestaShop\Adapter\Assets\AssetUrlGeneratorTrait;

    public function __construct(array $directories, ConfigurationInterface $configuration)
    {
        $this->directories = $directories;
        $this->configuration = $configuration;

        $this->list = $this->getDefaultList();
    }

    abstract protected function getDefaultList();
    abstract protected function getList();

    protected function getFullPath($relativePath)
    {
        foreach ($this->getDirectories() as $baseDir) {
            $fullPath = $baseDir . ltrim($relativePath, '/'); // not DIRECTORY_SEPARATOR because, it's path included manualy
            if (file_exists($this->getPathFromUri($fullPath))) {
                return $fullPath;
            }
        }
        return false;
    }

    private function getDirectories()
    {
        static $directories;

        if (null === $directories) {
            foreach ($this->directories as $baseDir) {
                if (!empty($baseDir)) {
                    $directories[] = $baseDir;
                }
            }
        }

        return $directories;
    }
}
