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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\Repository;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Symfony\Component\Finder\Finder;

/**
 * Methods to access data source of Module
 */
class ModuleRepository extends AbstractObjectModelRepository
{
    /** @var string[] */
    public const ADDITIONAL_ALLOWED_MODULES = [
        'autoupgrade',
    ];

    private const COMPOSER_PACKAGE_TYPE = 'prestashop-module';

    /**
     * @var array
     */
    private $activeModulesPaths;

    /**
     * @param ModuleId $moduleId
     *
     * @throws CoreException
     * @throws ModuleNotFoundException
     */
    public function assertModuleExists(ModuleId $moduleId): void
    {
        $this->assertObjectModelExists($moduleId->getValue(), 'module', ModuleNotFoundException::class);
    }

    /**
     * Return active modules.
     *
     * @return array
     */
    public function getActiveModules(): array
    {
        if (!defined('_DB_PREFIX_')) {
            return []; // getActiveModules() can be called during install BEFORE the database configuration has been defined
        }

        $activeModules = [];
        try {
            $modulesData = \Db::getInstance()->executeS(
                'SELECT m.* FROM `' . _DB_PREFIX_ . 'module` m WHERE m.`active` = 1'
            );

            if (is_array($modulesData)) {
                $activeModules = array_map(function (array $module): string {
                    return $module['name'];
                }, $modulesData);
            }
        } catch (Exception $exception) {
            // DO nothing. getActiveModules() can be called during install BEFORE the database configuration has been defined
        }

        return $activeModules;
    }

    /**
     * Returns active module file paths.
     *
     * @return array<string, string> File paths indexed by module name
     */
    public function getActiveModulesPaths(): array
    {
        if (null === $this->activeModulesPaths) {
            $this->activeModulesPaths = [];
            $activeModules = $this->getActiveModules();

            foreach ($this->getModules() as $moduleName => $modulePath) {
                if (in_array($moduleName, $activeModules)) {
                    $this->activeModulesPaths[$moduleName] = $modulePath;
                }
            }
        }

        return $this->activeModulesPaths;
    }

    /**
     * Returns an array of native modules
     *
     * @return array<string>
     */
    public function getNativeModules(): array
    {
        // Native modules are the one integrated in PrestaShop release via composer
        // so we use the lock files to generate the list
        $content = file_get_contents(_PS_ROOT_DIR_ . '/composer.lock');
        $content = json_decode($content, true);
        if (empty($content['packages'])) {
            return [];
        }

        $modules = array_filter($content['packages'], function (array $package) {
            return self::COMPOSER_PACKAGE_TYPE === $package['type'] && !empty($package['name']);
        });
        $modules = array_map(function (array $package) {
            $vendorName = explode('/', $package['name']);

            return $vendorName[1];
        }, $modules);

        return array_merge(
            $modules,
            static::ADDITIONAL_ALLOWED_MODULES
        );
    }

    /**
     * Returns an array of non-native modules
     *
     * @return array<int, string>
     */
    public function getNonNativeModules(): array
    {
        $nativeModules = $this->getNativeModules();

        $modules = [];

        foreach ($this->getModules() as $moduleName => $modulePath) {
            if (!in_array($moduleName, $nativeModules)) {
                $modules[] = $moduleName;
            }
        }

        return $modules;
    }

    /**
     * Returns an iterable of module names
     *
     * @return iterable
     */
    private function getModules(): iterable
    {
        $modulesFiles = Finder::create()->directories()->in(_PS_MODULE_DIR_)->depth(0);
        foreach ($modulesFiles as $moduleFile) {
            yield $moduleFile->getFilename() => $moduleFile->getPathname();
        }
    }
}
