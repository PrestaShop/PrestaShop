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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Symfony\Component\Finder\Finder;

/**
 * Methods to access data source of Module
 */
class ModuleRepository extends AbstractObjectModelRepository
{
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
            $modulesFiles = Finder::create()->directories()->in(_PS_MODULE_DIR_)->depth(0);
            $activeModules = $this->getActiveModules();

            foreach ($modulesFiles as $moduleFile) {
                $moduleName = $moduleFile->getFilename();
                if (in_array($moduleName, $activeModules)) {
                    $this->activeModulesPaths[$moduleName] = $moduleFile->getPathname();
                }
            }
        }

        return $this->activeModulesPaths;
    }
}
